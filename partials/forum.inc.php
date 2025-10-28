<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

<main>
    <!-- Hero Section -->
    <?php
    $heading = "Community Cookbook";
    $sub = "Share your favorite recipes with the FoodFusion community";
    require 'banner.php';
    ?>

    <!-- Recipe Submission -->
    <section class="submit-recipe-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Submit Your Recipe</h2>
                <div class="title-underline"></div>
                <p class="section-sub">Share a recipe and contribute to the community cookbook. Please provide clear ingredients and step-by-step instructions.</p>
            </div>

            <div class="submit-recipe-grid">
                <div class="submit-recipe-form-container">
                    <form id="recipeForm" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Recipe Title</label>
                            <input type="text" id="title" name="title" placeholder="e.g., Classic Margherita Pizza" required>
                        </div>

                        <div class="form-group">
                            <label for="image">Recipe Image (optional)</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            <small class="muted">Recommended: 800x600px. Max 2MB.</small>
                        </div>

                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea id="ingredients" name="ingredients" rows="6" placeholder="List ingredients, one per line" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="instructions">Instructions</label>
                            <textarea id="instructions" name="instructions" rows="8" placeholder="Step-by-step instructions" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group small">
                                <label for="prep_time">Prep Time (mins)</label>
                                <input type="number" id="prep_time" name="prep_time" min="0" placeholder="e.g., 15">
                            </div>

                            <div class="form-group small">
                                <label for="cook_time">Cook Time (mins)</label>
                                <input type="number" id="cook_time" name="cook_time" min="0" placeholder="e.g., 20">
                            </div>

                            <div class="form-group small">
                                <label for="servings">Servings</label>
                                <input type="number" id="servings" name="servings" min="1" placeholder="e.g., 4">
                            </div>

                            <div class="form-group small">
                                <label for="difficulty">Difficulty</label>
                                <select id="difficulty" name="difficulty">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="cuisine_type">Cuisine Type</label>
                                <input type="text" id="cuisine_type" name="cuisine_type" placeholder="e.g., Italian">
                            </div>

                            <div class="form-group">
                                <label for="dietary_tags">Dietary Tags (comma-separated)</label>
                                <input type="text" id="dietary_tags" name="dietary_tags" placeholder="e.g., vegetarian, gluten-free">
                            </div>
                        </div>

                        <button type="submit" class="cta-button full-width" id="submitRecipeBtn">Submit Recipe</button>
                    </form>
                </div>

                <!-- Info / Guidelines Column -->
                <aside class="submission-guidelines">
                    <div class="section-header">
                        <h3 class="section-title">Submission Guidelines</h3>
                        <div class="title-underline"></div>
                    </div>

                    <ul class="guidelines-list">
                        <li>Provide clear ingredient quantities and steps.</li>
                        <li>One ingredient per line is preferred.</li>
                        <li>Include prep/cook times and servings when possible.</li>
                        <li>Images help boost visibility — consider adding one.</li>
                        <li>By submitting, you agree to our community rules and terms.</li>
                    </ul>

                    <p class="muted">After submission, recipes may be reviewed and published by site moderators. You'll be notified if any changes are needed.</p>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php
require 'partials/footer.php';
?>

<script>
    $(document).ready(function () {
        const $recipeForm = $('#recipeForm');
        const $submitBtn = $('#submitRecipeBtn');

        // Simple helpers
        function isUserLoggedIn() {
            return $('.user-profile').length > 0;
        }

        // Client-side validation
        function validateRecipeForm(formData) {
            const title = (formData.get('title') || '').trim();
            const ingredients = (formData.get('ingredients') || '').trim();
            const instructions = (formData.get('instructions') || '').trim();
            const prep = formData.get('prep_time');
            const cook = formData.get('cook_time');

            if (!title || title.length < 3) {
                Swal.fire({ icon: 'warning', title: 'Title required', text: 'Please enter a recipe title (at least 3 characters).', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            if (!ingredients || ingredients.length < 10) {
                Swal.fire({ icon: 'warning', title: 'Ingredients required', text: 'Please provide the ingredients (at least 10 characters).', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            if (!instructions || instructions.length < 20) {
                Swal.fire({ icon: 'warning', title: 'Instructions required', text: 'Please provide step-by-step instructions (at least 20 characters).', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            if (prep && isNaN(prep)) {
                Swal.fire({ icon: 'warning', title: 'Invalid prep time', text: 'Prep time must be a number.', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            if (cook && isNaN(cook)) {
                Swal.fire({ icon: 'warning', title: 'Invalid cook time', text: 'Cook time must be a number.', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            // Optionally check image size/type client-side
            const imageFile = $('#image')[0].files[0];
            if (imageFile && imageFile.size > 2 * 1024 * 1024) { // 2MB
                Swal.fire({ icon: 'warning', title: 'Image too large', text: 'Please use an image smaller than 2MB.', confirmButtonColor: '#FF6B6B' });
                return false;
            }

            return true;
        }

        $recipeForm.on('submit', function (e) {
            e.preventDefault();

            if (!isUserLoggedIn()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login required',
                    text: 'Please log in to submit a recipe.',
                    confirmButtonColor: '#FF6B6B',
                    showCancelButton: true,
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#joinModal').show(); // existing modal in the app
                    }
                });
                return;
            }

            // Build FormData from the form (supports file upload)
            const fd = new FormData(this);

            // Validate form contents
            if (!validateRecipeForm(fd)) return;

            // Disable submit button and show spinner
            $submitBtn.prop('disabled', true);
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

            $.ajax({
                url: 'actions.php?action=submit_recipe',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    console.log('submit_recipe response:', response);

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Recipe submitted',
                            text: response.message || 'Thanks — your recipe has been submitted for review.',
                            showConfirmButton: false,
                            timer: 1800
                        }).then(() => {
                            // Option A: redirect to recipes page if response provided redirect
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                // Reset form
                                $recipeForm[0].reset();
                                // Reset any UI icons/state if needed
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission failed',
                            text: response.message || 'There was an error submitting your recipe. Please try again.',
                            confirmButtonColor: '#FF6B6B'
                        });
                    }
                },
                error: function (xhr, status, err) {
                    console.error('submit_recipe AJAX error', status, err, xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'There was an error submitting your recipe. Please try again later.',
                        confirmButtonColor: '#FF6B6B'
                    });
                },
                complete: function () {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html(originalText);
                }
            });
        });
    });
</script>