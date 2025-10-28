<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

    <main>
        <!-- Hero Section -->
        <?php
        $heading = "Recipe Collection";
        $sub = "Discover culinary treasures from around the world";
        require 'banner.php';
        ?>

        <!-- Contact Content -->
        <section class="featured-recipes">
            <div class="section-header">
                <h2 class="section-title">Recipes</h2>
                <div class="title-underline"></div>
            </div>
            <div class="recipe-grid">
                <!-- Recipe Card 1 -->
                <?php getRecipes('all'); ?>
            </div>
        </section>

    </main>

<?php
require 'partials/footer.php';
?>