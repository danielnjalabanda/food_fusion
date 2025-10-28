<?php
// (file header and HTML unchanged up to the scripts — the contents mirror the original
// partials/footer.php but with the duplicate script block removed and the bookmark
// handler improved as described in the message)
?>
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <h3>About FoodFusion</h3>
                <p>Bringing home cooks together to share recipes, techniques, and culinary inspiration since 2023.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pages/recipes/">Recipes</a></li>
                    <li><a href="pages/tips/">Culinary Tips</a></li>
                    <li><a href="pages/forum/">Community</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h3>Connect With Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-newsletter">
                <h3>Stay Updated</h3>
                <form id="newsletterForm">
                    <label>
                        <input type="email" placeholder="Your email address" required>
                    </label>
                    <button class="acceptCookies" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> FoodFusion. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Join Us Modal -->
<?php
require 'modals/auth.php';
?>

<!-- Cookie Consent Banner -->
<div id="cookieConsent">
    <p>We use cookies to enhance your experience. By continuing to visit this site, you agree to our use of cookies.
        <a href="privacy.php">Learn more</a></p>
    <button class="acceptCookies" id="acceptCookies">Accept</button>
</div>

<script src="assets/js/main.js"></script>
<script src="assets/js/carousel.js"></script>
<script src="assets/js/auth.js"></script>
<script src="assets/js/jquery-3.7.1.min.js"></script>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
    $(document).ready(function () {
        const saveRecipeButton = $('.save-recipe-btn');
        const formValue = $('#form-value');
        const firstNameField = $('#first_name');
        const lastNameField = $('#last_name');
        const loginButton = $('.cta-button');
        const toggleLoginBtn = $('#toggleLoginBtn');
        const registerForm = $('#registerForm');

        // Toggle between login and register forms
        $(toggleLoginBtn).on('click', function (e) {
            e.preventDefault();
            if (formValue.val() === '0') {
                formValue.val('1');
                firstNameField.hide();
                lastNameField.hide();
                toggleLoginBtn.html('Sign up here');
                loginButton.html('Sign in');
                $('h2').text('Login to FoodFusion');
            } else {
                formValue.val('0');
                firstNameField.show();
                lastNameField.show();
                toggleLoginBtn.html('Sign in here');
                loginButton.html('Sign up');
                $('h2').text('Join FoodFusion');
            }
        });

        // Form submission handler
        $(registerForm).on('submit', function (e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const action = formValue.val() === '0' ? 'register' : 'login';

            // Basic client-side validation
            if (!validateForm()) {
                return;
            }

            // Show loading state
            loginButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: 'actions.php?action=' + action,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    loginButton.prop('disabled', false).html(formValue.val() === '0' ? 'Sign up' : 'Sign in');

                    if (response.success) {
                        // Success - show SweetAlert and redirect
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = response.redirect || 'index.php';
                        });
                    } else {
                        // Error - show SweetAlert with error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                            confirmButtonColor: '#FF6B6B'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    loginButton.prop('disabled', false).html(formValue.val() === '0' ? 'Sign up' : 'Sign in');

                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#FF6B6B'
                    });
                    console.error('AJAX Error:', error);
                }
            });
        });

        // Form validation function
        function validateForm() {
            const email = $('#email').val();
            const password = $('#password').val();

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.',
                    confirmButtonColor: '#FF6B6B'
                });
                return false;
            }

            // Password validation
            if (password.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Weak Password',
                    text: 'Password must be at least 6 characters long.',
                    confirmButtonColor: '#FF6B6B'
                });
                return false;
            }

            // For registration, validate name fields
            if (formValue.val() === '0') {
                const firstName = firstNameField.val().trim();
                const lastName = lastNameField.val().trim();

                if (firstName.length < 2 || lastName.length < 2) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Name',
                        text: 'First and last name must be at least 2 characters long.',
                        confirmButtonColor: '#FF6B6B'
                    });
                    return false;
                }
            }

            return true;
        }

        // Bookmark functionality
        $(saveRecipeButton).on('click', function () {
            const $button = $(this);
            const recipeId = $button.data('recipe-id');
            const $icon = $button.find('i');

            console.log('Bookmark button clicked - Recipe ID:', recipeId);
            console.log('Current icon classes:', $icon.attr('class'));

            // Check if user is logged in
            if (!isUserLoggedIn()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please log in to save recipes to your bookmarks.',
                    confirmButtonColor: '#FF6B6B',
                    showCancelButton: true,
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#joinModal').show();
                    }
                });
                return;
            }

            // Determine action based on current icon state
            const isCurrentlyBookmarked = $icon.hasClass('fas');
            const action = isCurrentlyBookmarked ? 'remove_bookmark' : 'bookmark_recipe';

            console.log('Action to perform:', action);
            console.log('Is currently bookmarked:', isCurrentlyBookmarked);

            // Disable button while request is in flight
            $button.prop('disabled', true);

            $.ajax({
                url: 'actions.php?action=' + action,
                method: 'POST',
                data: {recipe_id: recipeId},
                dataType: 'json',
                success: function (response) {
                    console.log('Server response:', response);

                    if (response.success) {
                        // Toggle bookmark icon
                        if (action === 'bookmark_recipe') {
                            $icon.removeClass('far').addClass('fas');
                            $button.attr('title', 'Remove from Bookmarks');
                            console.log('Bookmark added - icon updated');
                        } else {
                            $icon.removeClass('fas').addClass('far');
                            $button.attr('title', 'Save Recipe');
                            console.log('Bookmark removed - icon updated');
                        }

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Special-case: server says "Bookmark not found" when trying to remove — treat as already removed
                        if (action === 'remove_bookmark' && response.message && response.message.toLowerCase().indexOf('bookmark not found') !== -1) {
                            // Ensure UI is in unbookmarked state
                            $icon.removeClass('fas').addClass('far');
                            $button.attr('title', 'Save Recipe');

                            Swal.fire({
                                icon: 'info',
                                title: 'Not bookmarked',
                                text: 'This recipe was not in your bookmarks.',
                                showConfirmButton: false,
                                timer: 1400
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                                confirmButtonColor: '#FF6B6B'
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('XHR:', xhr);

                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'There was an error processing your request. Please try again.',
                        confirmButtonColor: '#FF6B6B'
                    });
                },
                complete: function () {
                    // Re-enable button after request
                    $button.prop('disabled', false);
                }
            });
        });

        // Check bookmark status on page load
        function checkBookmarkStatus() {
            if (!isUserLoggedIn()) return;

            $(saveRecipeButton).each(function () {
                const $button = $(this);
                const recipeId = $button.data('recipe-id');
                const $icon = $button.find('i');

                $.ajax({
                    url: 'actions.php?action=check_bookmark',
                    method: 'POST',
                    data: {recipe_id: recipeId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.success && response.bookmarked) {
                            $icon.removeClass('far').addClass('fas');
                            $button.attr('title', 'Remove from Bookmarks');
                        } else {
                            $icon.removeClass('fas').addClass('far');
                            $button.attr('title', 'Save Recipe');
                        }
                    },
                    error: function () {
                        // Silently fail - don't show error for status checks
                    }
                });
            });
        }

        // Check if user is logged in
        function isUserLoggedIn() {
            return $('.user-profile').length > 0;
        }

        // Initialize bookmark status check
        checkBookmarkStatus();

        $('#logoutBtn').on('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6B6B',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'actions.php?action=logout',
                        method: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = response.redirect;
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Logout Failed',
                                text: 'There was an error logging out. Please try again.',
                                confirmButtonColor: '#FF6B6B'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>