<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

    <main>
        <!-- Hero Section -->
        <?php
        $heading = "Community Cookbook";
        $sub = "Discover the passion behind FoodFusion and the people who make it all possible";
        require 'banner.php';
        ?>

        <!-- Contact Content -->
        <section class="mission">
            <div class="section-header">
                <h2 class="section-title">Our Culinary Philosophy</h2>
                <div class="title-underline"></div>
            </div>
            <div class="mission-content">
                <p>At FoodFusion, we believe that cooking should be an adventure, not a chore. Our philosophy is built on three core principles that guide everything we do:

                    Passion First
                    We approach every recipe with enthusiasm and love, because great food starts with great energy.

                    Quality Ingredients
                    We champion seasonal, sustainable ingredients that let natural flavors shine through.

                    Community Driven
                    We believe food tastes better when shared, and knowledge grows when passed on.</p>
            </div>
        </section>
    </main>

<?php
require 'partials/footer.php';
?>