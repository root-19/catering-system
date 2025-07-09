<?php
require_once __DIR__ . '/../../../config/database.php';

$pdo = Database::connect();

// Pagination settings
$perPage = 8;
$activePage = isset($_GET['active_page']) ? max(1, intval($_GET['active_page'])) : 1;
$inactivePage = isset($_GET['inactive_page']) ? max(1, intval($_GET['inactive_page'])) : 1;

// Count total active instructors
$stmtCountActive = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'instructor' AND status = 'active'");
$totalActive = $stmtCountActive->fetchColumn();
$totalActivePages = ceil($totalActive / $perPage);
$activeOffset = ($activePage - 1) * $perPage;

// Count total inactive instructors
$stmtCountInactive = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'instructor' AND status = 'inactive'");
$totalInactive = $stmtCountInactive->fetchColumn();
$totalInactivePages = ceil($totalInactive / $perPage);
$inactiveOffset = ($inactivePage - 1) * $perPage;

// Get paginated active instructors
$stmtActive = $pdo->prepare("SELECT * FROM users WHERE role = 'instructor' AND status = 'active' ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmtActive->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmtActive->bindValue(':offset', $activeOffset, PDO::PARAM_INT);
$stmtActive->execute();
$activeInstructors = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

// Get paginated inactive instructors
$stmtInactive = $pdo->prepare("SELECT * FROM users WHERE role = 'instructor' AND status = 'inactive' ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmtInactive->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmtInactive->bindValue(':offset', $inactiveOffset, PDO::PARAM_INT);
$stmtInactive->execute();
$inactiveInstructors = $stmtInactive->fetchAll(PDO::FETCH_ASSOC);

include "layout/sidebar.php";
?>

<div class="bg-gray-900 text-white flex justify-center items-center p-6 mt-60 ">
    <div class="container mx-auto p-6 bg-gray-800 rounded-lg shadow-lg w-full max-w-4xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-orange-400">Instructor Management</h1>

        <!-- Active Instructors Table -->
        <h2 class="text-xl font-semibold mb-4 text-orange-400">Active Instructors</h2>
        <table class="min-w-full bg-gray-700 text-gray-200 rounded-lg overflow-hidden shadow-md mb-8">
            <thead>
                <tr class="bg-orange-400">
                    <th class="px-6 py-3 text-left text-sm font-medium">No.</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Username</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeInstructors as $index => $instructor): ?>
                    <tr class="border-t border-gray-600">
                        <td class="px-6 py-4"><?= $index + 1 ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($instructor['username']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($instructor['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-md bg-green-500 text-white">
                                <?= ucfirst(htmlspecialchars($instructor['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-row gap-2">
                                <button type="button" onclick="showDetails(<?= htmlspecialchars(json_encode($instructor)) ?>)" 
                                        class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 transition text-sm font-medium shadow">
                                    View
                                </button>
                                <button type="button" onclick="showEditForm(<?= htmlspecialchars(json_encode($instructor)) ?>)"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-sm font-medium shadow">
                                    Edit
                                </button>
                                <button type="button" 
                                        onclick="toggleInstructorStatus(<?= htmlspecialchars($instructor['id']) ?>, '<?= $instructor['status'] ?>')"
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition text-sm font-medium shadow">
                                    Archive
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination for Active Instructors -->
        <div class="flex justify-center items-center gap-2 mb-8">
            <?php if ($activePage > 1): ?>
                <a href="?active_page=<?= $activePage - 1 ?>&inactive_page=<?= $inactivePage ?>" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Previous</a>
            <?php endif; ?>
            <span class="px-3 py-2 text-gray-300">Page <?= $activePage ?> of <?= $totalActivePages ?></span>
            <?php if ($activePage < $totalActivePages): ?>
                <a href="?active_page=<?= $activePage + 1 ?>&inactive_page=<?= $inactivePage ?>" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Next</a>
            <?php endif; ?>
        </div>

        <!-- Inactive Instructors Table -->
        <h2 class="text-xl font-semibold mb-4 text-orange-400">Archived Instructors</h2>
        <table class="min-w-full bg-gray-700 text-gray-200 rounded-lg overflow-hidden shadow-md">
            <thead>
                <tr class="bg-orange-400">
                    <th class="px-6 py-3 text-left text-sm font-medium">No.</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Username</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inactiveInstructors as $index => $instructor): ?>
                    <tr class="border-t border-gray-600">
                        <td class="px-6 py-4"><?= $index + 1 ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($instructor['username']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($instructor['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-md bg-red-500 text-white">
                                <?= ucfirst(htmlspecialchars($instructor['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-row gap-2">
                                <button type="button" onclick="showDetails(<?= htmlspecialchars(json_encode($instructor)) ?>)" 
                                        class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 transition text-sm font-medium shadow">
                                    View
                                </button>
                                <button type="button" onclick="showEditForm(<?= htmlspecialchars(json_encode($instructor)) ?>)"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-sm font-medium shadow">
                                    Edit
                                </button>
                                <button type="button" 
                                        onclick="toggleInstructorStatus(<?= htmlspecialchars($instructor['id']) ?>, '<?= $instructor['status'] ?>')"
                                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition text-sm font-medium shadow">
                                    Activate
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination for Inactive Instructors -->
        <div class="flex justify-center items-center gap-2 mb-8">
            <?php if ($inactivePage > 1): ?>
                <a href="?active_page=<?= $activePage ?>&inactive_page=<?= $inactivePage - 1 ?>" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Previous</a>
            <?php endif; ?>
            <span class="px-3 py-2 text-gray-300">Page <?= $inactivePage ?> of <?= $totalInactivePages ?></span>
            <?php if ($inactivePage < $totalInactivePages): ?>
                <a href="?active_page=<?= $activePage ?>&inactive_page=<?= $inactivePage + 1 ?>" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Next</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Instructor Details and Edit Form -->
<div id="instructorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-gray-800 p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-orange-400" id="modalTitle">Instructor Details</h2>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="instructorDetails" class="text-gray-200">
            <!-- Details will be populated by JavaScript -->
        </div>
        <div id="editForm" class="hidden">
            <form id="updateInstructorForm" class="space-y-4">
                <input type="hidden" id="editInstructorId" name="id">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 mb-2">Username</label>
                        <input type="text" id="editUsername" name="username" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Email</label>
                        <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Status</label>
                        <select id="editStatus" name="status" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Phone</label>
                        <input type="tel" id="editPhone" name="phone_number" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Profile Image</label>
                        <input type="file" id="editProfileImage" name="profile_image" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Birthday</label>
                        <input type="date" id="editBirthday" name="birthday" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Bank Name</label>
                        <input type="text" id="editBankName" name="bank_name" class="w-full px-3 py-2 bg-gray-700 rounded text-white">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-400 text-white rounded hover:bg-orange-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showDetails(instructor) {
    const modal = document.getElementById('instructorModal');
    const detailsContainer = document.getElementById('instructorDetails');
    const editForm = document.getElementById('editForm');
    const modalTitle = document.getElementById('modalTitle');
    
    modalTitle.textContent = 'Instructor Details';
    editForm.classList.add('hidden');
    detailsContainer.classList.remove('hidden');
    
    // Create HTML for instructor details
    const detailsHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400">Username</p>
                    <p class="font-semibold">${instructor.username}</p>
                </div>
                <div>
                    <p class="text-gray-400">Email</p>
                    <p class="font-semibold">${instructor.email}</p>
                </div>
                <div>
                    <p class="text-gray-400">Status</p>
                    <p class="font-semibold">${instructor.status}</p>
                </div>
                <div>
                    <p class="text-gray-400">Phone Number</p>
                    <p class="font-semibold">${instructor.phone_number || 'Not set'}</p>
                </div>
                <div>
                    <p class="text-gray-400">Birthday</p>
                    <p class="font-semibold">${instructor.birthday || 'Not set'}</p>
                </div>
                <div>
                    <p class="text-gray-400">Bank Name</p>
                    <p class="font-semibold">${instructor.bank_name || 'Not set'}</p>
                </div>
            </div>
        </div>
    `;
    
    detailsContainer.innerHTML = detailsHTML;
    modal.classList.remove('hidden');
}

function showEditForm(instructor) {
    const modal = document.getElementById('instructorModal');
    const detailsContainer = document.getElementById('instructorDetails');
    const editForm = document.getElementById('editForm');
    const modalTitle = document.getElementById('modalTitle');
    
    modalTitle.textContent = 'Edit Instructor';
    detailsContainer.classList.add('hidden');
    editForm.classList.remove('hidden');
    
    // Populate form fields
    document.getElementById('editInstructorId').value = instructor.id;
    document.getElementById('editUsername').value = instructor.username;
    document.getElementById('editEmail').value = instructor.email;
    document.getElementById('editStatus').value = instructor.status;
    document.getElementById('editPhone').value = instructor.phone_number || '';
    document.getElementById('editBirthday').value = instructor.birthday || '';
    document.getElementById('editBankName').value = instructor.bank_name || '';
    
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('instructorModal');
    modal.classList.add('hidden');
}

// Handle form submission
document.getElementById('updateInstructorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/admin/update-instructor', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Instructor updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                closeModal();
                location.reload(); // Reload the page to show updated data
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error updating instructor: ' + result.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while updating the instructor.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

// Close modal when clicking outside
document.getElementById('instructorModal').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) {
        closeModal();
    }
});

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
    }
});

function toggleInstructorStatus(instructorId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = currentStatus === 'active' ? 'Inactive' : 'Activate';

    Swal.fire({
        title: `Are you sure?`,
        text: `Do you want to set this instructor as ${action.toLowerCase()}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Yes, ${action.toLowerCase()}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/toggle_user_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userId: instructorId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Success!',
                        `Instructor status updated to ${action.toLowerCase()}.`,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'Failed to update instructor status. Please try again.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'An error occurred while updating the instructor status.',
                    'error'
                );
            });
        }
    });
}
</script>