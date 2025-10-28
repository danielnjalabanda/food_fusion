<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Joy of Home Cooking</h1>
            <p>Join our community of food enthusiasts sharing recipes, tips, and culinary inspiration.</p>
            <button id="joinBtn" class="cta-button">Join Our Community</button>
        </div>
    </section>

    <!-- Mission Statement -->
    <section class="mission">
        <div class="section-header">
            <h2 class="section-title">Our Mission</h2>
            <div class="title-underline"></div>
        </div>
        <div class="mission-content">
            <p>At FoodFusion, we believe everyone can be a great cook. We're dedicated to helping home chefs of all skill levels discover new recipes, learn techniques, and connect with fellow food lovers.</p>
        </div>
    </section>

    <!-- Featured Recipes Carousel -->
    <section class="featured-recipes">
        <div class="section-header">
            <h2 class="section-title">Featured Recipes</h2>
            <div class="title-underline"></div>
        </div>
        <div class="recipe-grid">
            <!-- Recipe Card 1 -->
            <?php getRecipes('featured'); ?>
        </div>
    </section>

    <!-- News Feed -->
    <section class="featured-recipes">
        <div class="section-header">
            <h2 class="section-title">Culinary Trends & News</h2>
            <div class="title-underline"></div>
        </div>
        <div class="recipe-grid">
            <!-- Recipe Card 1 -->
            <?php getRecipes('recent'); ?>
        </div>
    </section>
</main>

<?php
require 'partials/footer.php';
?>


