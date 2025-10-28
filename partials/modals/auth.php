<div id="joinModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Join FoodFusion</h2>
        <form id="registerForm" method="POST">
            <div class="form-group">
                <label>
                    <input type="hidden" name="form-value" id="form-value" value="0" required>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="email" name="email" id="email" placeholder="Email Address" required>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </label>
            </div>
            <button type="submit" class="cta-button">Sign Up</button>
        </form>
        <p>Already a member? <a id="toggleLoginBtn" href="#">Log in here</a></p>
    </div>
</div>
