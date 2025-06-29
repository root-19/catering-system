<?php
// public/chatbot_api.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
$config = require __DIR__ . '/../config/config.php';

session_start();

function getServices() {
    $stmt = Database::connect()->query('SELECT id, package_name, description, price, location, packs FROM services ORDER BY created_at DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getReservedDates($month) {
    $stmt = Database::connect()->prepare('SELECT reservation_date FROM orders WHERE reservation_date LIKE ?');
    $stmt->execute(["$month-%"]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $dates;
}

function getAvailableDates($month) {
    $reserved = getReservedDates($month);
    $daysInMonth = date('t', strtotime($month . '-01'));
    $available = [];
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $date = sprintf('%s-%02d', $month, $d);
        if (!in_array($date, $reserved)) {
            $available[] = $date;
        }
    }
    return $available;
}

function isServiceQuestion($msg) {
    $msg = strtolower($msg);
    return (
        strpos($msg, 'service') !== false ||
        strpos($msg, 'package') !== false ||
        strpos($msg, 'menu') !== false ||
        strpos($msg, 'offer') !== false ||
        strpos($msg, 'reservation') !== false ||
        strpos($msg, 'date') !== false ||
        strpos($msg, 'available') !== false
    );
}

function isPriceQuestion($msg) {
    $msg = strtolower($msg);
    return (
        strpos($msg, 'price') !== false ||
        strpos($msg, 'cost') !== false ||
        strpos($msg, 'pricing') !== false
    );
}

function isAvailableDatesQuestion($msg) {
    $msg = strtolower($msg);
    return (
        (strpos($msg, 'available') !== false && strpos($msg, 'date') !== false) ||
        strpos($msg, 'free date') !== false ||
        strpos($msg, 'open date') !== false ||
        strpos($msg, 'reservation date') !== false
    );
}

function isBookNowQuestion($msg) {
    $msg = strtolower($msg);
    return (
        strpos($msg, 'book now') !== false ||
        (strpos($msg, 'book') !== false && strpos($msg, 'now') !== false) ||
        strpos($msg, 'make a reservation') !== false
    );
}

function isYes($msg) {
    $msg = strtolower(trim($msg));
    return in_array($msg, ['yes', 'y', 'yeah', 'sure', 'ok', 'okay']);
}

function isNo($msg) {
    $msg = strtolower(trim($msg));
    return in_array($msg, ['no', 'n', 'nope', 'not now']);
}

function isDietaryQuestion($msg) {
    $msg = strtolower($msg);
    return (
        strpos($msg, 'dietary') !== false ||
        strpos($msg, 'restriction') !== false ||
        strpos($msg, 'allergy') !== false ||
        strpos($msg, 'vegetarian') !== false ||
        strpos($msg, 'vegan') !== false ||
        strpos($msg, 'gluten') !== false ||
        strpos($msg, 'halal') !== false ||
        strpos($msg, 'kosher') !== false
    );
}

function getDietaryMenuItems() {
    $pdo = Database::connect();
    $keywords = ['vegetarian', 'vegan', 'gluten', 'halal', 'kosher', 'allergy', 'nut-free', 'dairy-free', 'keto', 'low-sodium', 'diabetic'];
    $where = [];
    $params = [];
    foreach ($keywords as $kw) {
        $where[] = "category LIKE ? OR item LIKE ?";
        $params[] = "%$kw%";
        $params[] = "%$kw%";
    }
    $sql = "SELECT category, item FROM service_items WHERE " . implode(' OR ', $where) . " ORDER BY category, item";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$items) return null;
    $grouped = [];
    foreach ($items as $row) {
        $cat = $row['category'];
        if (!isset($grouped[$cat])) $grouped[$cat] = [];
        $grouped[$cat][] = $row['item'];
    }
    return $grouped;
}

function getDietaryMessage() {
    $grouped = getDietaryMenuItems();
    if ($grouped) {
        $msg = "Here are some of our menu items for special dietary needs:\n\n";
        foreach ($grouped as $cat => $items) {
            $msg .= "• $cat:\n";
            foreach ($items as $item) {
                $msg .= "   - $item\n";
            }
        }
        $msg .= "\nWe always discuss dietary needs during consultation. Would you like to see our full special dietary menu?";
        return $msg;
    }
    // fallback static message
    return "Absolutely! We accommodate various dietary requirements:\n\n" .
        "🥗 Vegetarian & Vegan: Plant-based protein options, dairy-free alternatives, fresh seasonal vegetables\n" .
        "🌾 Gluten-Free: Certified gluten-free kitchen, dedicated preparation areas, extensive GF menu options\n" .
        "🦐 Allergies & Restrictions: Nut-free options, halal and kosher options, custom allergy accommodations\n" .
        "📋 Special Diets: Keto-friendly, low-sodium, diabetic-friendly choices\n\n" .
        "We always discuss dietary needs during consultation. Would you like to see our special dietary menu?";
}

function getServiceMessage() {
    $services = getServices();
    if (!$services) return 'No services found.';
    $msg = "Here are our catering packages and services:\n\n";
    foreach ($services as $s) {
        $msg .= "• {$s['package_name']} ({$s['packs']} pax) - {$s['location']}\n  {$s['description']}\n";
    }
    $msg .= "\nAsk for available dates or more details about any package!";
    return $msg;
}

function getPriceMessage() {
    $services = getServices();
    if (!$services) return 'No packages found.';
    $msg = "Here are our catering packages with prices and packs:\n\n";
    foreach ($services as $s) {
        $msg .= "• {$s['package_name']} - ₱{$s['price']} per person, {$s['packs']} pax\n";
    }
    $msg .= "\nLet me know if you want more details about any package!";
    return $msg;
}

function getAvailableDatesMessage($month = null) {
    if (!$month) $month = date('Y-m');
    $dates = getAvailableDates($month);
    $monthName = date('F Y', strtotime($month . '-01'));
    if (!$dates) return "Sorry, there are no available dates for $monthName.";
    $msg = "Here are the available dates for $monthName:\n\n";
    foreach ($dates as $d) {
        $msg .= "+ $d\n";
    }
    $msg .= "\nLet me know if you want to reserve a date or see another month!";
    return $msg;
}

function callDeepSeek($userMsg, $context, $config) {
    $apiKey = $config['deepseek']['api_key'];
    $apiUrl = $config['deepseek']['api_url'];
    $model = $config['deepseek']['model'];

    $messages = [
        ["role" => "system", "content" => $context],
        ["role" => "user", "content" => $userMsg]
    ];

    $data = [
        "model" => $model,
        "messages" => $messages,
        "temperature" => 0.7
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return "Sorry, there was an error connecting to the AI service.";
    }
    $result = json_decode($response, true);
    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    }
    return "Sorry, I couldn't get a response from the AI.";
}

function findPackageByName($name) {
    $services = getServices();
    foreach ($services as $s) {
        if (stripos($s['package_name'], $name) !== false) {
            return $s;
        }
    }
    return null;
}

function getPackageDetailMessage($package) {
    if (!$package) return 'Sorry, I could not find details for that package.';
    $msg = "Here are the details for the {$package['package_name']} package:\n";
    $msg .= "• Price: ₱{$package['price']} per person\n";
    $msg .= "• Packs: {$package['packs']} pax\n";
    $msg .= "• Location: {$package['location']}\n";
    $msg .= "• Description: {$package['description']}\n";
    $msg .= "\nLet me know if you want to check available dates or book this package!";
    return $msg;
}

// Main handler
$type = $_GET['type'] ?? '';
if ($type === 'services') {
    echo json_encode(['services' => getServices()]);
    exit;
} elseif ($type === 'available_dates' && isset($_GET['month'])) {
    $month = $_GET['month']; // format: YYYY-MM
    echo json_encode(['available_dates' => getAvailableDates($month)]);
    exit;
} elseif ($type === 'ask' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    // Booking intent logic
    if (isset($_SESSION['booking_intent']) && $_SESSION['booking_intent'] === true) {
        if (isYes($msg)) {
            unset($_SESSION['booking_intent']);
            echo json_encode(['reply' => 'Redirecting you to our services page to book your event!', 'redirect' => '/app/views/services.php']);
            exit;
        } elseif (isNo($msg)) {
            unset($_SESSION['booking_intent']);
            echo json_encode(['reply' => 'Okay, let me know what I can help you with.']);
            exit;
        }
    }
    if (isBookNowQuestion($msg)) {
        $_SESSION['booking_intent'] = true;
        echo json_encode(['reply' => 'Do you want to book right now?']);
        exit;
    }
    // Check for follow-up on a package
    if (isset($_SESSION['last_package']) && stripos($msg, 'detail') !== false) {
        $package = findPackageByName($_SESSION['last_package']);
        echo json_encode(['reply' => getPackageDetailMessage($package)]);
        exit;
    }
    // If user asks about a specific package
    $services = getServices();
    foreach ($services as $s) {
        if (stripos($msg, $s['package_name']) !== false) {
            $_SESSION['last_package'] = $s['package_name'];
            echo json_encode(['reply' => getPackageDetailMessage($s)]);
            exit;
        }
    }
    if (isAvailableDatesQuestion($msg)) {
        echo json_encode(['reply' => getAvailableDatesMessage()]);
        exit;
    } else if (isPriceQuestion($msg)) {
        echo json_encode(['reply' => getPriceMessage()]);
        exit;
    } elseif (isServiceQuestion($msg)) {
        echo json_encode(['reply' => getServiceMessage()]);
        exit;
    } elseif (isDietaryQuestion($msg)) {
        echo json_encode(['reply' => getDietaryMessage()]);
        exit;
    } else {
        $context = getServiceMessage();
        $reply = callDeepSeek($msg, $context, $config);
        echo json_encode(['reply' => $reply]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
    exit;
} 