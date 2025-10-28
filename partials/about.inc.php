<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

    <main>
        <!-- Hero Section -->
        <?php
        $heading = "About Us";
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

        <section class="featured-recipes">
            <div class="section-header">
                <h2 class="section-title">Meet Our Team</h2>
                <div class="title-underline"></div>
                <p class="section-subtitle">The passionate food enthusiasts behind FoodFusion</p>
            </div>
            <div class="recipe-grid">
                <!-- Card 1 -->
                <div class="recipe-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-image-container">
                        <img src="assets/images/teams/sarah.jpg" alt="Sarah Johnson - Founder" class="recipe-image">
                        <div class="image-overlay"></div>
                    </div>
                    <div class="card-content">
                        <h3 class="recipe-title">Sarah Johnson</h3>
                        <div class="recipe-meta">
                            <span class="meta-item">Founder & CEO</span>
                        </div>
                        <div class="recipe-meta">
                            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-linkedin-in"></i></span></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-instagram"></i></span></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-twitter"></i></span></a>
                        </div>
                        <p class="recipe-excerpt">Professional chef with 15 years experience, Sarah founded FoodFusion to make gourmet cooking accessible to everyone.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="recipe-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-image-container">
                        <img src="assets/images/teams/michael.jpg" alt="Michael Chen - Head Chef" class="recipe-image">
                        <div class="image-overlay"></div>
                    </div>
                    <div class="card-content">
                        <h3 class="recipe-title">Michael Chen</h3>
                        <div class="recipe-meta">
                            <span class="meta-item">Head Chef</span>
                        </div>
                        <div class="recipe-meta">
                            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-linkedin-in"></i></span></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-instagram"></i></span></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-twitter"></i></span></a>
                        </div>
                        <p class="recipe-excerpt">Michelin-star trained chef specializing in fusion cuisine, Michael develops many of our signature recipes.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="recipe-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-image-container">
                        <img src="assets/images/teams/emma.jpg" alt="Emma Rodriguez - Community Manager" class="recipe-image">
                        <div class="image-overlay"></div>
                    </div>
                    <div class="card-content">
                        <h3 class="recipe-title">Emma Rodriguez</h3>
                        <div class="recipe-meta">
                            <span class="meta-item">Community Manager</span>
                        </div>
                        <div class="recipe-meta">
                            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-linkedin-in"></i></span></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-instagram"></i></span></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer"><span class="meta-item"><i class="fab fa-twitter"></i></span></a>
                        </div>
                        <p class="recipe-excerpt">Food blogger turned community builder, Emma ensures our platform remains welcoming and engaging for all.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Milestones Section -->
        <section class="journey-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Our Culinary Journey</h2>
                    <p class="section-subtitle">Highlighting key milestones that shaped our community</p>
                    <div class="title-underline"></div>
                </div>

                <div class="culinary-timeline">
                    <!-- Timeline Item 1 -->
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="marker-year">2015</div>
                            <div class="marker-icon">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="content-image">
                                <img src="assets/images/timeline/home-kitchen.jpg" alt="Home kitchen">
                            </div>
                            <div class="content-text">
                                <h3>Founded in a Home Kitchen</h3>
                                <p>What started as Sarah's personal recipe blog quickly grew into something bigger as friends and family encouraged her to share more.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 2 -->
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="marker-year">2017</div>
                            <div class="marker-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="content-image">
                                <img src="assets/images/timeline/team-join.jpg" alt="Team members">
                            </div>
                            <div class="content-text">
                                <h3>First Team Members Joined</h3>
                                <p>Michael and Emma came on board, transforming the blog into a full-fledged culinary platform.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 3 -->
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="marker-year">2019</div>
                            <div class="marker-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="content-image">
                                <img src="assets/images/timeline/community.jpg" alt="Community celebration">
                            </div>
                            <div class="content-text">
                                <h3>100,000 Community Members</h3>
                                <p>We celebrated our growing community of food lovers sharing recipes and culinary tips.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 4 -->
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="marker-year">2022</div>
                            <div class="marker-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="content-image">
                                <img src="assets/images/timeline/mobile-app.jpg" alt="Mobile app">
                            </div>
                            <div class="content-text">
                                <h3>New Mobile App Launch</h3>
                                <p>Expanded our reach with a dedicated mobile application for cooking on the go.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 5 -->
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="marker-year">Today</div>
                            <div class="marker-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="content-image">
                                <img src="assets/images/timeline/global.jpg" alt="Global community">
                            </div>
                            <div class="content-text">
                                <h3>Over 1 Million Monthly Users</h3>
                                <p>Continuing our mission to make cooking accessible and enjoyable for home chefs worldwide.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php
require 'partials/footer.php';
?>