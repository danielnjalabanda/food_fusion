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

