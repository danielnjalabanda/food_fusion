<?php

session_start();
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_fusion";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    sendResponse(false, "Database connection failed: " . $conn->connect_error);
    exit();
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Get action from URL parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Route to appropriate handler
switch ($action) {
    case 'register':
        handleRegister($conn);
        break;
    case 'login':
        handleLogin($conn);
        break;
    case 'logout':
        handleLogout();
        break;
    case 'bookmark_recipe':
        handleBookmarkRecipe($conn);
        break;
    case 'remove_bookmark':
        handleRemoveBookmark($conn);
        break;
    case 'check_bookmark':
        handleCheckBookmark($conn);
        break;
    case 'contact_us':
        handleContactUs($conn);
        break;
    case 'submit_recipe':
        handleSubmitRecipe($conn);
        break;
    default:
        sendResponse(false, "Invalid action");
        break;
}

$conn->close();

// Logout handler
function handleLogout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    sendResponse(true, "Logged out successfully", "index.php");
}

function handleSubmitRecipe($conn) {
    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "Please log in to submit a recipe");
        return;
    }

    // Basic required fields
    if (empty($_POST['title']) || empty($_POST['ingredients']) || empty($_POST['instructions'])) {
        sendResponse(false, "Title, ingredients and instructions are required");
        return;
    }

    // Sanitize inputs (uses existing sanitizeInput function)
    $title = sanitizeInput($conn, $_POST['title']);
    $ingredients = sanitizeInput($conn, $_POST['ingredients']);
    $instructions = sanitizeInput($conn, $_POST['instructions']);
    $prep_time = isset($_POST['prep_time']) && $_POST['prep_time'] !== '' ? intval($_POST['prep_time']) : null;
    $cook_time = isset($_POST['cook_time']) && $_POST['cook_time'] !== '' ? intval($_POST['cook_time']) : null;
    $servings = isset($_POST['servings']) && $_POST['servings'] !== '' ? intval($_POST['servings']) : null;
    $difficulty = isset($_POST['difficulty']) ? sanitizeInput($conn, $_POST['difficulty']) : 'medium';
    $cuisine_type = isset($_POST['cuisine_type']) ? sanitizeInput($conn, $_POST['cuisine_type']) : null;
    $dietary_tags = isset($_POST['dietary_tags']) ? sanitizeInput($conn, $_POST['dietary_tags']) : null;

    $user_id = $_SESSION['user_id'];

    // Validate lengths
    if (strlen($title) < 3) {
        sendResponse(false, "Recipe title must be at least 3 characters");
        return;
    }
    if (strlen($ingredients) < 5) {
        sendResponse(false, "Please provide ingredient details");
        return;
    }
    if (strlen($instructions) < 10) {
        sendResponse(false, "Please provide detailed instructions");
        return;
    }

    // Handle optional image upload
    $image_filename = ''; // will store filename (or empty => default image handled in display)
    $uploadDir = __DIR__ . '/assets/images/recipes/'; // actions.php directory level
    // Ensure upload dir ends with slash and exists
    if (!is_dir($uploadDir)) {
        // try to create (best-effort)
        @mkdir($uploadDir, 0755, true);
    }

    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $img = $_FILES['image'];

        // Basic upload error check
        if ($img['error'] !== UPLOAD_ERR_OK) {
            error_log("Image upload error: " . $img['error']);
            sendResponse(false, "Image upload failed (error code " . $img['error'] . ")");
            return;
        }

        // Validate size (2MB max)
        $maxSize = 2 * 1024 * 1024;
        if ($img['size'] > $maxSize) {
            sendResponse(false, "Image is too large. Maximum size is 2MB.");
            return;
        }

        // Validate MIME/type with getimagesize
        $tmpPath = $img['tmp_name'];
        $imgInfo = @getimagesize($tmpPath);
        if ($imgInfo === false) {
            sendResponse(false, "Uploaded file is not a valid image.");
            return;
        }

        $mime = $imgInfo['mime'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        if (!isset($allowed[$mime])) {
            sendResponse(false, "Unsupported image type. Allowed: JPG, PNG, GIF.");
            return;
        }

        // Generate a safe unique filename
        $ext = $allowed[$mime];
        $image_filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $destination = $uploadDir . $image_filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            error_log("Failed to move uploaded file to $destination");
            sendResponse(false, "Failed to save uploaded image.");
            return;
        }

        // Optionally, you could perform image resizing/optimization here.
    }

    // Insert into recipes table
    // Schema reference from setup.php:
    // title, image, ingredients, instructions, prep_time, cook_time, servings, difficulty, cuisine_type, dietary_tags, user_id
    $sql = "INSERT INTO recipes 
        (title, image, ingredients, instructions, prep_time, cook_time, servings, difficulty, cuisine_type, dietary_tags, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed for submit_recipe: " . $conn->error);
        sendResponse(false, "Failed to submit recipe: database error (prepare failed).");
        return;
    }

    // For DB columns expecting ints, pass null as NULL or 0; MySQLi bind_param doesn't accept null type directly,
    // so we cast nulls to null and use null coalescing to 0 where desired. Here we bind integers (use 0 if null).
    $prep_time_db = $prep_time !== null ? $prep_time : 0;
    $cook_time_db = $cook_time !== null ? $cook_time : 0;
    $servings_db = $servings !== null ? $servings : 0;
    $image_db = $image_filename; // can be empty string

    $stmt->bind_param(
        "ssssiiisssi",
        $title,
        $image_db,
        $ingredients,
        $instructions,
        $prep_time_db,
        $cook_time_db,
        $servings_db,
        $difficulty,
        $cuisine_type,
        $dietary_tags,
        $user_id
    );

    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        error_log("Recipe submitted successfully - ID: $new_id by user $user_id");
        // Return recipe id and redirect to recipes listing page
        sendResponse(true, "Recipe submitted successfully and will be reviewed.", "index.php?pages=recipes", ['recipe_id' => $new_id]);
    } else {
        error_log("Recipe insert failed: " . $stmt->error);
        // If image was uploaded but DB insert failed, you might want to remove uploaded image to avoid orphan files.
        if ($image_filename) {
            @unlink($uploadDir . $image_filename);
        }
        sendResponse(false, "Failed to submit recipe: " . $stmt->error);
    }

    $stmt->close();
}

/**
 * Handle contact us submissions from the contact page.
 * Expected POST fields:
 *  - name
 *  - email
 *  - message
 *
 * Inserts into contact_us (name, email, message). Table already created by setup.php.
 */
function handleContactUs($conn) {
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        sendResponse(false, "All fields are required");
        return;
    }

    // Sanitize input
    $name = sanitizeInput($conn, $_POST['name']);
    $subject = sanitizeInput($conn, $_POST['subject']);
    $email = sanitizeInput($conn, $_POST['email']);
    $message = sanitizeInput($conn, $_POST['message']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Invalid email format");
        return;
    }

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Prepare failed for contact_us insert: " . $conn->error . " (errno: " . $conn->errno . ")");
        sendResponse(false, "Failed to submit message: database error (prepare failed).");
        return;
    }

    $stmt->bind_param("ssss", $name, $subject, $email, $message);

    if ($stmt->execute()) {
        $insertId = $stmt->insert_id;
        error_log("Contact message saved - ID: $insertId, from: $email");
        // Optionally: you could email admins here
        sendResponse(true, "Your message has been received. We'll get back to you soon.");
    } else {
        error_log("Contact us insert failed: " . $stmt->error);
        sendResponse(false, "Failed to submit message: " . $stmt->error);
    }

    $stmt->close();
}

// Bookmark recipe handler
function handleBookmarkRecipe($conn) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "Please log in to save recipes");
        return;
    }

    // Validate required fields
    if (empty($_POST['recipe_id'])) {
        sendResponse(false, "Recipe ID is required");
        return;
    }

    $user_id = $_SESSION['user_id'];
    $recipe_id = intval($_POST['recipe_id']);

    // Debug: Log the values
    error_log("Bookmark attempt - User ID: $user_id, Recipe ID: $recipe_id");

    // Check if recipe exists
    if (!recipeExists($conn, $recipe_id)) {
        sendResponse(false, "Recipe not found");
        return;
    }

    // Check if already bookmarked (fast-path)
    if (isBookmarked($conn, $user_id, $recipe_id)) {
        sendResponse(false, "Recipe already bookmarked");
        return;
    }

    // Prepare insert statement and check for prepare errors
    $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe_id) VALUES (?, ?)");
    if ($stmt === false) {
        // Prepare failed — return DB error
        error_log("Prepare failed for bookmark insert: " . $conn->error . " (errno: " . $conn->errno . ")");
        sendResponse(false, "Failed to save recipe: database error (prepare failed).");
        return;
    }

    $stmt->bind_param("ii", $user_id, $recipe_id);

    if ($stmt->execute()) {
        $bookmark_id = $stmt->insert_id;
        error_log("Bookmark added successfully - Bookmark ID: $bookmark_id");
        sendResponse(true, "Recipe saved to your bookmarks!");
    } else {
        // If duplicate entry (race condition), return a friendly message
        $errno = $stmt->errno ?: $conn->errno;
        $error = $stmt->error ?: $conn->error;
        error_log("Bookmark failed (errno $errno): $error");

        if ($errno == 1062) { // duplicate entry
            sendResponse(false, "Recipe already bookmarked");
        } else {
            sendResponse(false, "Failed to save recipe: " . $error);
        }
    }

    $stmt->close();
}

// Remove bookmark handler
function handleRemoveBookmark($conn) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "Please log in to manage bookmarks");
        return;
    }

    // Validate required fields
    if (empty($_POST['recipe_id'])) {
        sendResponse(false, "Recipe ID is required");
        return;
    }

    $user_id = $_SESSION['user_id'];
    $recipe_id = intval($_POST['recipe_id']);

    // Debug: Log the values
    error_log("Remove bookmark attempt - User ID: $user_id, Recipe ID: $recipe_id");

    // Prepare delete statement and check for prepare errors
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND recipe_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed for bookmark delete: " . $conn->error . " (errno: " . $conn->errno . ")");
        sendResponse(false, "Failed to remove bookmark: database error (prepare failed).");
        return;
    }

    $stmt->bind_param("ii", $user_id, $recipe_id);

    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        error_log("Bookmark removal - Affected rows: $affected_rows");

        if ($affected_rows > 0) {
            sendResponse(true, "Recipe removed from bookmarks");
        } else {
            sendResponse(false, "Bookmark not found");
        }
    } else {
        error_log("Bookmark removal failed: " . $stmt->error);
        sendResponse(false, "Failed to remove bookmark: " . $stmt->error);
    }

    $stmt->close();
}


// Check if recipe is bookmarked
function handleCheckBookmark($conn) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "User not logged in");
        return;
    }

    // Validate required fields
    if (empty($_POST['recipe_id'])) {
        sendResponse(false, "Recipe ID is required");
        return;
    }

    $user_id = $_SESSION['user_id'];
    $recipe_id = intval($_POST['recipe_id']);

    $isBookmarked = isBookmarked($conn, $user_id, $recipe_id);

    sendResponse(true, "Bookmark status checked", null, ['bookmarked' => $isBookmarked]);
}

// Helper function to check if recipe exists
function recipeExists($conn, $recipe_id) {
    $stmt = $conn->prepare("SELECT id FROM recipes WHERE id = ?");
    if ($stmt === false) {
        error_log("Prepare failed for recipeExists: " . $conn->error);
        return false;
    }
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    return $exists;
}

// Helper function to check if recipe is bookmarked
function isBookmarked($conn, $user_id, $recipe_id) {
    $stmt = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed for isBookmarked: " . $conn->error);
        // Assume not bookmarked on DB error (safer to let client attempt an insert which will surface the real error)
        return false;
    }
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookmarked = $result->num_rows > 0;
    $stmt->close();

    return $bookmarked;
}

$conn->close();

// Register handler
function handleRegister($conn) {
    // Validate required fields
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['password'])) {
        sendResponse(false, "All fields are required");
        return;
    }

    // Sanitize input data
    $firstname = sanitizeInput($conn, $_POST['first_name']);
    $lastname = sanitizeInput($conn, $_POST['last_name']);
    $email = sanitizeInput($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Invalid email format");
        return;
    }

    // Validate name length
    if (strlen($firstname) < 2 || strlen($lastname) < 2) {
        sendResponse(false, "First and last name must be at least 2 characters long");
        return;
    }

    // Validate password strength
    if (strlen($password) < 6) {
        sendResponse(false, "Password must be at least 6 characters long");
        return;
    }

    // Generate username from first and last name
    $username = generateUsername($conn, $firstname, $lastname);

    // Check if email already exists
    if (emailExists($conn, $email)) {
        sendResponse(false, "Email already registered");
        return;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database using prepared statement
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstname, $lastname, $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        // Get the new user ID
        $user_id = $stmt->insert_id;

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['firstname'] = $firstname;

        sendResponse(true, "Registration successful! Welcome to FoodFusion", "index.php");
    } else {
        sendResponse(false, "Registration failed: " . $stmt->error);
    }

    $stmt->close();
}

// Login handler
function handleLogin($conn) {
    // Validate required fields
    if (empty($_POST['email']) || empty($_POST['password'])) {
        sendResponse(false, "Email and password are required");
        return;
    }

    // Sanitize input data
    $email = sanitizeInput($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Invalid email format");
        return;
    }

    // Find user by email using prepared statement
    $stmt = $conn->prepare("SELECT id, firstname, username, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstname'] = $user['firstname'];

            sendResponse(true, "Login successful! Welcome back " . $user['firstname'], "index.php");
        } else {
            sendResponse(false, "Invalid password");
        }
    } else {
        sendResponse(false, "No account found with this email");
    }

    $stmt->close();
}

// Helper function to generate unique username
function generateUsername($conn, $firstname, $lastname) {
    $baseUsername = strtolower($firstname . $lastname);
    $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);

    $username = $baseUsername;
    $counter = 1;

    // Check if username exists and generate unique one
    while (usernameExists($conn, $username)) {
        $username = $baseUsername . $counter;
        $counter++;
    }

    return $username;
}

// Check if email exists
function emailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    return $exists;
}

// Check if username exists
function usernameExists($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    return $exists;
}

// Sanitize input data
function sanitizeInput($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Send JSON response
function sendResponse($success, $message, $redirect = null, $additionalData = []) {
    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($redirect) {
        $response['redirect'] = $redirect;
    }

    if (!empty($additionalData)) {
        $response = array_merge($response, $additionalData);
    }

    echo json_encode($response);
    exit();
}

