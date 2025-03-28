<!-- JavaScript block for logout confirmation and ticker functionality -->
<script>
    /**
     * Function to confirm logout action
     * Displays a confirmation dialog and redirects to logout.php if confirmed
     */
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) { // Show confirmation dialog
            window.location.href = '../account/logout.php'; // Redirect to logout page if "OK" is clicked
        }
        // If "Cancel" is clicked, do nothing
    }

    /**
     * Function to update the date and time ticker
     * Formats the current date and time as "DD-MM-YYYY, HH:MM" and updates the ticker element
     */
    function updateTicker() {
        var now = new Date(); // Get the current date and time
        var day = String(now.getDate()).padStart(2, '0'); // Get day and pad with 0 if needed (e.g., "02")
        var month = String(now.getMonth() + 1).padStart(2, '0'); // Get month (0-based, so +1) and pad
        var year = now.getFullYear(); // Get full year (e.g., 2025)
        var hours = String(now.getHours()).padStart(2, '0'); // Get hours and pad (24-hour format)
        var minutes = String(now.getMinutes()).padStart(2, '0'); // Get minutes and pad
        var dateTimeString = `${day}-${month}-${year}, ${hours}:${minutes}`; // Format string like "22-03-2025, 14:46"
        document.getElementById("ticker").innerText = dateTimeString; // Update the ticker element's text
    }

    /**
     * Event listener to run code when the DOM is fully loaded
     * Initializes the ticker and sets it to update every second
     */
    document.addEventListener("DOMContentLoaded", function() {
        updateTicker(); // Call updateTicker immediately to set initial value
        setInterval(updateTicker, 1000); // Update ticker every 1000ms (1 second)
    });
</script>

<!-- PHP block to display success message if set in session -->
<?php if (isset($_SESSION['success'])): ?>
    <!-- Success message div: styled as a green banner at the top of the page -->
    <div id="successMessage"
        style="display: block; background: #4CAF50; color: white; padding: 10px; text-align: center; position: fixed; top: 10px; left: 50%; transform: translateX(-50%); border-radius: 5px;">
        <?= htmlspecialchars($_SESSION['success']) ?> <!-- Display the success message, escaped for security -->
    </div>

    <!-- JavaScript to hide the success message after 3 seconds -->
    <script>
        /**
         * Event listener to run when DOM is loaded
         * Hides the success message after a 3-second delay
         */
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() { // Set a 3-second timer
                var successMessage = document.getElementById("successMessage"); // Get the success message element
                if (successMessage) { // Check if it exists
                    successMessage.style.display = "none"; // Hide it by setting display to none
                }
            }, 3000); // 3000ms = 3 seconds
        });
    </script>

    <?php unset($_SESSION['success']); ?> <!-- Remove the success message from session so it doesn’t reappear -->
<?php endif; ?> <!-- End of the if block -->

<!-- Start of HTML document -->
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php include 'head.php'; ?> <!-- Include external head.php file for meta tags, styles, etc. -->
    <!-- End Meta Tags -->
</head>

<!-- Topbar Start -->

<body>
    <div class="container-fluid"> <!-- Fluid container for full-width layout -->
        <!-- First row: Secondary background with ticker on the left and account on the right -->
        <div class="row bg-secondary py-1 px-xl-5"> <!-- Row with light gray background, padding -->
            <div class="col-lg-12"> <!-- Full-width column -->
                <div class="d-flex justify-content-between align-items-center"> <!-- Flex container to split left and right -->
                    <!-- Button group for the ticker (date and time) on the left -->
                    <div class="btn-group mr-2"> <!-- Margin-right 2 units for spacing -->
                        <button type="button" class="btn btn-sm btn-light" id="ticker"></button>
                    </div>

                    <!-- My Account -->
                    <div class="d-inline-flex align-items-center">
                        <!-- Button group for My Account dropdown -->
                        <div class="btn-group">
                            <?php if (isset($_SESSION['name'])): ?> <!-- Check if user is logged in -->
                                <!-- If logged in, show username and dropdown -->
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                    <?= htmlspecialchars($_SESSION['name']); ?> <!-- Display escaped username -->
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"> <!-- Dropdown menu aligned to the right -->
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?> <!-- Check if user is admin -->
                                        <a class="dropdown-item" href="../admin/admin.php">Dashboard</a> <!-- Admin dashboard link -->
                                    <?php endif; ?> <!-- End of admin check -->
                                    <a class="dropdown-item" href="../pages/account.php">My Account</a> <!-- Link to account page -->
                                    <a class="dropdown-item" href="../account/change_password.php">Change Password</a> <!-- Link to change password -->

                                    <a class="dropdown-item" href="#" onclick="confirmLogout()">Logout</a> <!-- Logout link, triggers confirmLogout -->
                                </div>
                            <?php else: ?> <!-- If not logged in -->
                                <!-- Show generic My Account button with sign-in/up options -->
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                    My Account <!-- Static text for non-logged-in users -->
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"> <!-- Dropdown menu for login/signup -->
                                    <a class="dropdown-item" href="../account/login.php">Sign in</a> <!-- Link to login page -->
                                    <a class="dropdown-item" href="../account/signup.php">Sign up</a> <!-- Link to signup page -->
                                </div>
                            <?php endif; ?> <!-- End of login check -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second row: Logo and customer service info -->
        <div class="row align-items-center bg-light py-3 px-xl-5 d-none d-lg-flex"> <!-- Row hidden on small screens, visible on large -->
            <div class="col-lg-4"> <!-- Left column for logo -->
                <a href="../pages/index.php" class="text-decoration-none"> <!-- Link to homepage, no underline -->
                    <span class="h1 text-uppercase text-primary bg-dark px-2">Bronx</span> <!-- "Bronx" in white on dark background -->
                    <span class="h1 text-uppercase text-dark bg-primary px-2 ml-n1">Luggage</span> <!-- "Luggage" in dark on primary background -->
                </a>
            </div>
            <div class="col-lg-4 col-6 text-left"> <!-- Middle column (empty for spacing) -->
            </div>
            <div class="col-lg-4 col-6 text-right"> <!-- Right column for customer service info -->
                <p class="m-0">Customer Service</p> <!-- Static text -->
                <h5 class="m-0">+012 345 6789</h5> <!-- Phone number -->
            </div>
        </div>
    </div>
    <!-- Topbar End -->
</body>

</html>