<?php
require 'partials/header.php';
require 'partials/navbar.php';
?>

    <main>
        <!-- Hero Section -->
        <?php
        $heading = "Contact Us";
        $sub = "Have questions, suggestions, or feedback? We'd love to hear from you!";
        require 'banner.php';
        ?>

        <!-- Contact Content -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <!-- Contact Form -->
                    <div class="contact-form-container">
                        <div class="section-header">
                            <h2 class="section-title">Send Us a Message</h2>
                            <div class="title-underline"></div>
                        </div>

                        <form id="contactForm" action="process-contact.php" method="POST">
                            <div class="form-row">
                                <div class="form-group half-width">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" placeholder="Your first name" required>
                                </div>
                                <div class="form-group half-width">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" placeholder="Your last name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <select id="subject" name="subject" required>
                                    <option value="" disabled selected>Select a topic</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="recipe">Recipe Request</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <textarea id="message" name="message" rows="6" placeholder="Type your message here..." required></textarea>
                            </div>

                            <button type="submit" class="cta-button full-width">Send Message</button>
                        </form>
                    </div>

                    <!-- Contact Info -->
                    <div class="contact-info-container">
                        <div class="section-header">
                            <h2 class="section-title">Our Information</h2>
                            <div class="title-underline"></div>
                        </div>

                        <div class="contact-info">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h3>Visit Us</h3>
                                    <p>123 Culinary Street<br>Foodie City, FC 10001</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h3>Call Us</h3>
                                    <p>+1 (555) 123-4567<br>Mon-Fri, 9am-5pm EST</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h3>Email Us</h3>
                                    <p>hello@foodfusion.com<br>support@foodfusion.com</p>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h3>Hours</h3>
                                    <p>Monday - Friday: 9am - 5pm<br>Saturday: 10am - 2pm<br>Sunday: Closed</p>
                                </div>
                            </div>
                        </div>

                        <div class="social-links">
                            <h3>Follow Us</h3>
                            <div class="social-icons">
                                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="map-container">
                    <div class="section-header">
                        <h2 class="section-title">Find Us</h2>
                        <div class="title-underline"></div>
                    </div>
                    <div class="map-wrapper">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3839.023241335004!2d35.0436355098819!3d-15.802728484775573!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x18d84f5d9634fc61%3A0x6421aea68154f889!2sNational%20College%20of%20Information%20and%20Comunication%20Technology!5e0!3m2!1sen!2smw!4v1754862320799!5m2!1sen!2smw"
                                width="100%"
                                height="450"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php
require 'partials/footer.php';
?>