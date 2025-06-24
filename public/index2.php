<?php
// session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Add this at the beginning of the file, after the PHP opening tag
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    // Set headers for file download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="goravels-framework.zip"');
    
    // Create a temporary directory
    $tempDir = sys_get_temp_dir() . '/goravels-temp-' . uniqid();
    mkdir($tempDir);
    
    // Copy framework files to temp directory
    $sourceDir = __DIR__ . '/..';
    $excludeDirs = ['vendor', 'node_modules', '.git'];
    
    function copyDir($source, $dest, $exclude) {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src = $source . '/' . $file;
                $dst = $dest . '/' . $file;
                
                if (is_dir($src)) {
                    if (!in_array($file, $exclude)) {
                        copyDir($src, $dst, $exclude);
                    }
                } else {
                    copy($src, $dst);
                }
            }
        }
        closedir($dir);
    }
    
    copyDir($sourceDir, $tempDir, $excludeDirs);
    
    // Create zip file
    $zipFile = $tempDir . '.zip';
    $zip = new ZipArchive();
    $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($tempDir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();
    
    // Output the zip file
    readfile($zipFile);
    
    // Clean up
    unlink($zipFile);
    array_map('unlink', glob("$tempDir/**/*.*"));
    rmdir($tempDir);
    
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Root-Dev</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism-tomorrow.min.css">
    <style>
        .typewriter-text::after {
            content: '|';
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .full-height {
            min-height: 100vh;
        }

        .code-block {
            background-color: #000000;
            padding: 1.5rem;
            border-radius: 0.5rem;
            font-family: 'Fira Code', monospace;
            overflow-x: auto;
            position: relative;
            border: 1px solid #ff0000;
            box-shadow: 0 4px 6px rgba(255, 0, 0, 0.1);
        }

        .code-block pre {
            margin: 0;
            padding-top: 1rem;
        }

        /* Syntax highlighting colors */
        .code-keyword { color: #ff0000; }  /* Red for keywords */
        .code-string { color: #00ff00; }   /* Green for strings */
        .code-comment { color: #ff00ff; }  /* Magenta for comments */
        .code-function { color: #00ffff; } /* Cyan for functions */
        .code-variable { color: #ffff00; } /* Yellow for variables */
        .code-operator { color: #ff0000; } /* Red for operators */
        .code-property { color: #00ff00; } /* Green for properties */
        .code-class { color: #00ffff; }    /* Cyan for classes */
        .code-namespace { color: #ff00ff; }/* Magenta for namespaces */
        .code-type { color: #ffff00; }     /* Yellow for types */
        .code-number { color: #ff8800; }   /* Orange for numbers */
        .code-constant { color: #ff00ff; } /* Magenta for constants */
        .code-attribute { color: #00ff00; }/* Green for attributes */
        .code-tag { color: #ff0000; }      /* Red for HTML/XML tags */
        .code-parameter { color: #ffff00; }/* Yellow for parameters */
        .code-sql-keyword { color: #ff0000; }    /* Red for SQL keywords */
        .code-sql-table { color: #00ffff; }      /* Cyan for table names */
        .code-sql-column { color: #ffff00; }     /* Yellow for column names */
        .code-sql-type { color: #00ff00; }       /* Green for SQL types */
        .code-sql-constraint { color: #ff00ff; } /* Magenta for constraints */

        /* File structure icons */
        .folder-icon::before {
            content: '\f07b';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
            color: #ff0000;
        }

        .file-icon::before {
            content: '\f15c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
            color: #ffffff;
        }

        .php-file-icon::before {
            content: '\f457';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
            color: #ff0000;
        }

        /* Guide section enhancements */
        .guide-section {
            background: #000000;
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #ff0000;
            border-radius: 0.5rem;
            flex: 1 1 calc(50% - 1rem);
            min-width: 300px;
            box-shadow: 0 4px 6px rgba(255, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .guide-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(255, 0, 0, 0.2);
        }

        /* Code block header */
        .code-header {
            background: #000000;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #ff0000;
            border-radius: 0.5rem 0.5rem 0 0;
            font-family: 'Fira Code', monospace;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .code-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .code-dot.red { background-color: #ff0000; }
        .code-dot.yellow { background-color: #ff0000; }
        .code-dot.green { background-color: #ff0000; }

        .guide-section {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid #ff0000;
            border-radius: 0.5rem;
            flex: 1 1 calc(50% - 1rem);
            min-width: 300px;
        }

        .step-number {
            background-color: #ff0000;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .step {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-left: 2px solid #ff0000;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s, transform 0.5s;
        }

        .step.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .step-content {
            display: none;
        }

        .step-content.visible {
            display: block;
        }

        /* New animations and enhanced styles */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .header-gradient {
            background: linear-gradient(45deg, #000000, #1a0000, #000000);
            background-size: 200% 200%;
            animation: gradientBG 15s ease infinite;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #ff0000;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .guide-section {
            animation: fadeInUp 0.6s ease-out;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #000000, #1a0000);
        }

        .guide-section:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(255, 0, 0, 0.2);
        }

        .code-block {
            animation: slideInLeft 0.6s ease-out;
            transition: all 0.3s ease;
        }

        .code-block:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 16px rgba(255, 0, 0, 0.2);
        }

        .step {
            animation: fadeInUp 0.6s ease-out;
            transition: all 0.3s ease;
        }

        .step:hover {
            transform: translateX(10px);
            background: rgba(255, 0, 0, 0.1);
        }

        .step-number {
            animation: pulse 2s infinite;
        }

        /* Enhanced button styles */
        .btn-primary {
            background: linear-gradient(45deg, #ff0000, #990000);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        /* Enhanced code header */
        .code-header {
            background: linear-gradient(45deg, #000000, #1a0000);
            transition: all 0.3s ease;
        }

        .code-header:hover {
            background: linear-gradient(45deg, #1a0000, #000000);
        }

        /* Scroll animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-out;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Header -->
    <header class="py-4 ">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold text-red-600">Next Gen </div>
                <nav class="space-x-6">
                    <a href="?download=true" class="btn-primary inline-flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Download Framework
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Welcome Section -->
    <div class="container mx-auto px-4 py-8 mt-20">
        <div class="text-center mb-12">
            <h1 class="text-6xl font-bold mb-6">
                <span class="text-red-600">Goravels</span> PHP Framework
            </h1>

            <p id="typewriter" class="text-xl text-gray-300 mb-10 typewriter-text"></p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-black border border-red-600 p-6 rounded-lg">
                    <i class="fas fa-bolt text-red-600 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Lightning Fast</h3>
                    <p class="text-gray-300">Built for speed with optimized routing and caching. Experience blazing fast response times.</p>
                </div>
                <div class="bg-black border border-red-600 p-6 rounded-lg">
                    <i class="fas fa-code text-red-600 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Clean Code</h3>
                    <p class="text-gray-300">Follows modern PHP practices with elegant syntax and intuitive structure.</p>
                </div>
                <div class="bg-black border border-red-600 p-6 rounded-lg">
                    <i class="fas fa-shield-alt text-red-600 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">Secure by Default</h3>
                    <p class="text-gray-300">Built-in security features protect your applications from common vulnerabilities.</p>
                </div>
            </div>
        </div>

        <!-- Framework Guide -->
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-6 text-red-600">Framework Guide</h2>

            <!-- Guide Sections Grid -->
            <div class="flex flex-wrap gap-4">
                <!-- Project Structure -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Project Structure</h3>
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-dot red"></span>
                            <span class="code-dot yellow"></span>
                            <span class="code-dot green"></span>
                            <span>Project Structure</span>
                        </div>
                        <pre>
<span class="folder-icon">framework/</span>
├── <span class="folder-icon">app/</span>
│   ├── <span class="folder-icon">controller/</span>    <span class="code-comment"># Controllers</span>
│   ├── <span class="folder-icon">models/</span>        <span class="code-comment"># Database models</span>
│   └── <span class="folder-icon">views/</span>         <span class="code-comment"># View files</span>
├── <span class="folder-icon">bin/</span>               <span class="code-comment"># CLI tools</span>
│   └── <span class="php-file-icon">cli.php</span>      <span class="code-comment"># Command line interface</span>
├── <span class="folder-icon">config/</span>            <span class="code-comment"># Configuration files</span>
├── <span class="folder-icon">core/</span>              <span class="code-comment"># Framework core</span>
├── <span class="folder-icon">database/</span>          <span class="code-comment"># Database files</span>
│   ├── <span class="folder-icon">migrations/</span>    <span class="code-comment"># Migration files</span>
│   └── <span class="php-file-icon">migrate.php</span>   <span class="code-comment"># Migration runner</span>
├── <span class="folder-icon">docs/</span>              <span class="code-comment"># Documentation</span>
├── <span class="folder-icon">logs/</span>              <span class="code-comment"># Log files</span>
├── <span class="folder-icon">public/</span>            <span class="code-comment"># Public assets</span>
│   ├── <span class="php-file-icon">index.php</span>    <span class="code-comment"># Entry point</span>
│   └── <span class="php-file-icon">router.php</span>   <span class="code-comment"># Router</span>
├── <span class="folder-icon">scripts/</span>           <span class="code-comment"># Utility scripts</span>
│   └── <span class="folder-icon">admin/</span>         <span class="code-comment"># Admin scripts</span>
└── <span class="folder-icon">vendor/</span>            <span class="code-comment"># Composer dependencies</span></pre>
                    </div>
                </div>

                <!-- Creating a New Controller -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Creating a New Controller</h3>
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-dot red"></span>
                            <span class="code-dot yellow"></span>
                            <span class="code-dot green"></span>
                            <span class="php-file-icon">ExampleController.php</span>
                        </div>
                        <pre>
<span class="code-comment">// app/controller/ExampleController.php</span>
<span class="code-keyword">namespace</span> <span class="code-namespace">root_dev\Controller</span>;

<span class="code-keyword">class</span> <span class="code-class">ExampleController</span> {
    <span class="code-keyword">public function</span> <span class="code-function">index</span>() {
        <span class="code-comment">// Return view with data</span>
        <span class="code-keyword">return</span> <span class="code-variable">$this</span>->view->render(<span class="code-string">'example/index'</span>, [
            <span class="code-string">'title'</span> => <span class="code-string">'Welcome'</span>
        ]);
    }

    <span class="code-keyword">public function</span> <span class="code-function">about</span>() {
        <span class="code-comment">// Simple about page</span>
        <span class="code-keyword">return</span> <span class="code-variable">$this</span>->view->render(<span class="code-string">'example/about'</span>);
    }
}</pre>
                    </div>
                </div>

                <!-- Using CLI -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Using CLI</h3>
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-dot red"></span>
                            <span class="code-dot yellow"></span>
                            <span class="code-dot green"></span>
                            <span>CLI Commands</span>
                        </div>
                        <pre>
<span class="code-comment"># Generate a new controller</span>
php bin/cli.php make:controller UserController

<span class="code-comment"># Generate a new model</span>
php bin/cli.php make:model User

<span class="code-comment"># Generate a new view</span>
php bin/cli.php make:view user_view</pre>
                    </div>
                </div>

                <!-- Database Migrations -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Database Migrations</h3>
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-dot red"></span>
                            <span class="code-dot yellow"></span>
                            <span class="code-dot green"></span>
                            <span class="php-file-icon">2024_03_20_create_examples_table.php</span>
                        </div>
                        <pre>
<span class="code-comment">// database/migrations/2024_03_20_create_examples_table.php</span>
<span class="code-keyword">class</span> <span class="code-class">CreateExamplesTable</span> {
    <span class="code-keyword">public function</span> <span class="code-function">up</span>(<span class="code-variable">$pdo</span>) {
        <span class="code-variable">$query</span> = <span class="code-string">"
            CREATE TABLE IF NOT EXISTS examples (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        "</span>;
        <span class="code-variable">$pdo</span>->exec(<span class="code-variable">$query</span>);
    }

    <span class="code-keyword">public function</span> <span class="code-function">down</span>(<span class="code-variable">$pdo</span>) {
        <span class="code-variable">$query</span> = <span class="code-string">"DROP TABLE IF EXISTS examples;"</span>;
        <span class="code-variable">$pdo</span>->exec(<span class="code-variable">$query</span>);
    }
}</pre>
                    </div>
                    <div class="mt-4">
                        <p class="text-gray-300">Run migrations using:</p>
                        <pre class="bg-gray-800 p-2 rounded mt-2">
php database/migrate.php</pre>
                    </div>
                </div>

                <!-- Admin Management -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Admin Management</h3>
                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-dot red"></span>
                            <span class="code-dot yellow"></span>
                            <span class="code-dot green"></span>
                            <span>Admin Scripts</span>
                        </div>
                        <pre>
<span class="code-comment"># Create a new admin user</span>
php scripts/admin/create_admin.php

<span class="code-comment"># Fix admin user issues</span>
php scripts/admin/fix_admin.php

<span class="code-comment"># Check admin status</span>
php scripts/admin/check_admin.php</pre>
                    </div>
                </div>
            </div>

            <!-- Step by Step Guide -->
            <div class="mt-12">
                <h2 class="text-3xl font-bold mb-6 text-red-600">Step by Step Guide</h2>
                
                <!-- Creating Files -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Creating New Files</h3>
                    <div class="space-y-4">
                        <div class="step" id="step1">
                            <span class="step-number">1</span>
                            <span class="font-bold">Create Controller</span>
                            <div class="step-content" id="step1-content">
                                <p class="ml-8 mt-2">Create a new file in <code>app/controller/</code> with your controller name (e.g., <code>UserController.php</code>)</p>
                            </div>
                        </div>
                        <div class="step" id="step2">
                            <span class="step-number">2</span>
                            <span class="font-bold">Create Model</span>
                            <div class="step-content" id="step2-content">
                                <p class="ml-8 mt-2">Create a new file in <code>app/models/</code> with your model name (e.g., <code>User.php</code>)</p>
                            </div>
                        </div>
                        <div class="step" id="step3">
                            <span class="step-number">3</span>
                            <span class="font-bold">Create View</span>
                            <div class="step-content" id="step3-content">
                                <p class="ml-8 mt-2">Create a new file in <code>app/views/</code> with your view name (e.g., <code>user.php</code>)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Migration Steps -->
                <div class="guide-section">
                    <h3 class="text-2xl font-bold mb-4">Database Migration Steps</h3>
                    <div class="space-y-4">
                        <div class="step" id="step4">
                            <span class="step-number">1</span>
                            <span class="font-bold">Create Migration File</span>
                            <div class="step-content" id="step4-content">
                                <p class="ml-8 mt-2">Create a new file in <code>app/database/migrations/</code> with timestamp (e.g., <code>2024_03_20_create_users_table.php</code>)</p>
                            </div>
                        </div>
                        <div class="step" id="step5">
                            <span class="step-number">2</span>
                            <span class="font-bold">Define Table Structure</span>
                            <div class="step-content" id="step5-content">
                                <p class="ml-8 mt-2">Write your table creation SQL in the <code>up()</code> method</p>
                            </div>
                        </div>
                        <div class="step" id="step6">
                            <span class="step-number">3</span>
                            <span class="font-bold">Define Rollback</span>
                            <div class="step-content" id="step6-content">
                                <p class="ml-8 mt-2">Write your table deletion SQL in the <code>down()</code> method</p>
                            </div>
                        </div>
                        <div class="step" id="step7">
                            <span class="step-number">4</span>
                            <span class="font-bold">Run Migration</span>
                            <div class="step-content" id="step7-content">
                                <p class="ml-8 mt-2">Execute the migration using the framework's migration command</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Welcome message typewriter effect
        const text = "Why settle for the old ways? Start better with PHP and build modern web apps faster, easier, and cleaner — only with Goravels. Experience the power of a lightweight, secure, and developer-friendly framework that makes coding a joy. From simple websites to complex applications, Goravels has got you covered with its elegant architecture and powerful features.";
        const element = document.getElementById("typewriter");
        let i = 0;

        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, 30); // Slightly faster typing speed
            } else {
                // Start showing steps after welcome message is done
                setTimeout(showSteps, 1000);
            }
        }

        // Step by step animation
        function showSteps() {
            const steps = document.querySelectorAll('.step');
            const stepContents = document.querySelectorAll('.step-content');
            let currentStep = 0;

            function showNextStep() {
                if (currentStep < steps.length) {
                    // Show step
                    steps[currentStep].classList.add('visible');
                    
                    // Type out step content
                    const content = stepContents[currentStep];
                    const text = content.querySelector('p').textContent;
                    content.classList.add('visible');
                    content.querySelector('p').textContent = '';
                    
                    let charIndex = 0;
                    function typeContent() {
                        if (charIndex < text.length) {
                            content.querySelector('p').textContent += text.charAt(charIndex);
                            charIndex++;
                            setTimeout(typeContent, 30);
                        } else {
                            currentStep++;
                            setTimeout(showNextStep, 500);
                        }
                    }
                    typeContent();
                }
            }

            showNextStep();
        }

        window.onload = type;

        function goToLogin() {
            window.location.href = '/login';
        }

        // Add scroll animations
        function handleScrollAnimations() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }

        window.addEventListener('scroll', handleScrollAnimations);
        window.addEventListener('load', handleScrollAnimations);

        // Add hover effects to guide sections
        document.querySelectorAll('.guide-section').forEach(section => {
            section.classList.add('animate-on-scroll');
        });

        // Add hover effects to code blocks
        document.querySelectorAll('.code-block').forEach(block => {
            block.classList.add('animate-on-scroll');
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-sql.min.js"></script>
    <script>
        // Initialize Prism.js
        Prism.highlightAll();
    </script>
</body>
</html>
