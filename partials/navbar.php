<header>
    <div class="container">
        <div class="navbar-container">
            <!-- Logo on the left -->
            <div class="navbar-brand">
                <a href="http://localhost/example/">
                    <img src="assets/images/logos/foodfusion-logo.png" alt="FoodFusion Logo" class="navbar-logo">
                </a>
            </div>

            <!-- Navigation links centered -->
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="http://localhost/example/">Home</a></li>
                    <li><a href="?pages=recipes">Recipes</a></li>
                    <li><a href="?pages=tips">Culinary Tips</a></li>
                    <li><a href="?pages=forum">Community</a></li>
                    <li><a href="?pages=about">About</a></li>
                    <li><a href="?pages=educational">Educational</a></li>
                    <li><a href="?pages=contact">Contact</a></li>
                </ul>
            </nav>

            <!-- Action buttons on the right -->
            <div class="nav-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-profile">
                        <a href="#">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                    </div>
                    <a href="#" id="logoutBtn" class="nav-button logout-btn">Logout</a>
                <?php else: ?>
                    <a href="#" id="headerJoinBtn" class="nav-button login-btn">Join Now</a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </div>
</header>
