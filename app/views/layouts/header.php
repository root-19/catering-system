<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="bg-white shadow-lg border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Navigation -->
            <div class="flex items-center">
                <span class="text-gray-800 text-xl font-bold">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <div class="hidden md:flex ml-10 space-x-4">
                    <a href="/dashboard" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Dashboard
                    </a>
                     <a href="/services" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                     Services
                    </a>
                    <a href="/review" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                     Review
                    </a>
                      <a href="/chatbot" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                     Chatbot
                    </a>
                    <!-- <a href="/profile" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Profile
                    </a>
                    <?php if ($_SESSION['role'] === 'user'): ?>
                    <a href="/admin/dashboard" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Admin Panel
                    </a>
                    <?php endif; ?> -->
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <a href="/profile" class="flex items-center group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition duration-150">
                        <span class="text-gray-700 text-sm font-medium">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </span>
                    </div>
                </a>
                <form action="/logout" method="POST">
                    <button type="submit" 
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition duration-150 ease-in-out text-sm font-medium">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500 transition duration-150">
                    <span class="sr-only">Open menu</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden mobile-menu bg-white border-t border-gray-100">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="/dashboard" class="text-gray-600 hover:bg-gray-50 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150">
                Dashboard
            </a>
            <a href="/profile" class="text-gray-600 hover:bg-gray-50 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150">
                Profile
            </a>
            <?php if ($_SESSION['role'] === 'user'): ?>
            <a href="/admin/dashboard" class="text-gray-600 hover:bg-gray-50 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150">
                Admin Panel
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.querySelector('.mobile-menu-button').addEventListener('click', function() {
    document.querySelector('.mobile-menu').classList.toggle('hidden');
});
</script> 