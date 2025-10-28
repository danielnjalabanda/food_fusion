<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_fusion";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: $conn->connect_error");
    }

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql)) {
        echo "Database created successfully or already exists.<br>";
    } else {
        throw new Exception("Error creating database: $conn->error");
    }

    // Select the database
    $conn->select_db($dbname);

    // Start transaction
    $conn->autocommit(FALSE);
    echo "Transaction started.<br>";

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (username),
        UNIQUE (email)
    )";

    if ($conn->query($sql)) {
        echo "Table 'users' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'users': $conn->error");
    }

    // Create recipes table
    $sql = "CREATE TABLE IF NOT EXISTS recipes (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        image VARCHAR(255),
        ingredients TEXT NOT NULL,
        instructions TEXT NOT NULL,
        prep_time INT(11),
        cook_time INT(11),
        servings INT(11),
        difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
        cuisine_type VARCHAR(50),
        dietary_tags VARCHAR(255),
        user_id INT(11),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";

    if ($conn->query($sql)) {
        echo "Table 'recipes' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'recipes': $conn->error");
    }

    // Create bookmarks table
    $sql = "CREATE TABLE IF NOT EXISTS bookmarks (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        recipe_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
        UNIQUE (user_id, recipe_id)
    )";

    if ($conn->query($sql)) {
        echo "Table 'bookmarks' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'bookmarks': $conn->error");
    }

    // Create comments table
    $sql = "CREATE TABLE IF NOT EXISTS comments (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        recipe_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    if ($conn->query($sql)) {
        echo "Table 'comments' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'comments': $conn->error");
    }

    // Create ratings table
    $sql = "CREATE TABLE IF NOT EXISTS ratings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        recipe_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        rating INT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE (user_id, recipe_id)
    )";

    if ($conn->query($sql)) {
        echo "Table 'ratings' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'ratings': $conn->error");
    }

    // Create contact_us table
    $sql = "CREATE TABLE IF NOT EXISTS contact_us (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'read', 'replied') DEFAULT 'pending'
    )";

    if ($conn->query($sql)) {
        echo "Table 'contact_us' created successfully.<br>";
    } else {
        throw new Exception("Error creating table 'contact_us': $conn->error");
    }

    // Insert sample recipes
    $sampleRecipes = [
        [
            "title" => "Classic Margherita Pizza",
            "image" => "margherita-pizza.jpg",
            "ingredients" => "Pizza dough, Tomato sauce, Fresh mozzarella, Basil leaves, Olive oil, Salt",
            "instructions" => "1. Preheat oven to 475°F (245°C). 2. Roll out the dough. 3. Spread tomato sauce. 4. Add mozzarella. 5. Bake for 10-12 minutes. 6. Add fresh basil and drizzle with olive oil.",
            "prep_time" => 20,
            "cook_time" => 12,
            "servings" => 4,
            "difficulty" => "medium",
            "cuisine_type" => "Italian"
        ],
        [
            "title" => "Avocado Toast",
            "image" => "avocado-toast.jpg",
            "ingredients" => "Bread, Avocado, Lemon juice, Salt, Pepper, Red pepper flakes, Olive oil",
            "instructions" => "1. Toast the bread. 2. Mash avocado with lemon juice, salt, and pepper. 3. Spread on toast. 4. Drizzle with olive oil and red pepper flakes.",
            "prep_time" => 5,
            "cook_time" => 5,
            "servings" => 1,
            "difficulty" => "easy",
            "cuisine_type" => "American"
        ],
        [
            "title" => "Chocolate Chip Cookies",
            "image" => "chocolate-chip-cookies.jpg",
            "ingredients" => "Flour, Butter, Sugar, Eggs, Vanilla extract, Baking soda, Salt, Chocolate chips",
            "instructions" => "1. Cream butter and sugars. 2. Add eggs and vanilla. 3. Mix dry ingredients. 4. Combine and add chocolate chips. 5. Bake at 375°F (190°C) for 9-11 minutes.",
            "prep_time" => 15,
            "cook_time" => 10,
            "servings" => 24,
            "difficulty" => "easy",
            "cuisine_type" => "American"
        ],
        [
            "title" => "Beef Tacos",
            "image" => "beef-tacos.jpg",
            "ingredients" => "Ground beef, Taco seasoning, Tortillas, Lettuce, Tomato, Cheese, Sour cream",
            "instructions" => "1. Brown the beef. 2. Add taco seasoning. 3. Warm tortillas. 4. Assemble tacos with toppings.",
            "prep_time" => 10,
            "cook_time" => 15,
            "servings" => 4,
            "difficulty" => "easy",
            "cuisine_type" => "Mexican"
        ],
        [
            "title" => "Chicken Curry",
            "image" => "chicken-curry.jpg",
            "ingredients" => "Chicken, Curry powder, Coconut milk, Onion, Garlic, Ginger, Vegetable oil",
            "instructions" => "1. Sauté onions, garlic, and ginger. 2. Add chicken and brown. 3. Add curry powder. 4. Pour in coconut milk and simmer for 20 minutes.",
            "prep_time" => 15,
            "cook_time" => 25,
            "servings" => 4,
            "difficulty" => "medium",
            "cuisine_type" => "Indian"
        ],
        [
            "title" => "Caesar Salad",
            "image" => "caesar-salad.jpg",
            "ingredients" => "Romaine lettuce, Croutons, Parmesan cheese, Caesar dressing, Lemon juice, Anchovies (optional)",
            "instructions" => "1. Chop lettuce. 2. Add croutons and parmesan. 3. Toss with dressing and lemon juice. 4. Garnish with anchovies if desired.",
            "prep_time" => 10,
            "cook_time" => 0,
            "servings" => 2,
            "difficulty" => "easy",
            "cuisine_type" => "American"
        ],
        [
            "title" => "Vegetable Stir Fry",
            "image" => "vegetable-stir-fry.jpg",
            "ingredients" => "Mixed vegetables, Soy sauce, Garlic, Ginger, Vegetable oil, Sesame oil, Rice",
            "instructions" => "1. Heat oils in wok. 2. Add garlic and ginger. 3. Stir fry vegetables. 4. Add soy sauce. 5. Serve over rice.",
            "prep_time" => 15,
            "cook_time" => 10,
            "servings" => 4,
            "difficulty" => "easy",
            "cuisine_type" => "Asian"
        ],
        [
            "title" => "Homemade Pasta",
            "image" => "homemade-pasta.jpg",
            "ingredients" => "Flour, Eggs, Olive oil, Salt",
            "instructions" => "1. Make a flour well. 2. Add eggs and mix. 3. Knead dough. 4. Rest for 30 minutes. 5. Roll out and cut. 6. Cook in boiling water for 2-3 minutes.",
            "prep_time" => 45,
            "cook_time" => 3,
            "servings" => 4,
            "difficulty" => "hard",
            "cuisine_type" => "Italian"
        ],
        [
            "title" => "Chocolate Lava Cake",
            "image" => "chocolate-lava-cake.jpg",
            "ingredients" => "Dark chocolate, Butter, Eggs, Sugar, Flour",
            "instructions" => "1. Melt chocolate and butter. 2. Whisk eggs and sugar. 3. Combine and add flour. 4. Bake at 425°F (220°C) for 12 minutes.",
            "prep_time" => 15,
            "cook_time" => 12,
            "servings" => 4,
            "difficulty" => "medium",
            "cuisine_type" => "French"
        ],
        [
            "title" => "Greek Salad",
            "image" => "greek-salad.jpg",
            "ingredients" => "Cucumber, Tomato, Red onion, Feta cheese, Kalamata olives, Olive oil, Oregano",
            "instructions" => "1. Chop vegetables. 2. Add olives and feta. 3. Drizzle with olive oil. 4. Sprinkle with oregano.",
            "prep_time" => 10,
            "cook_time" => 0,
            "servings" => 2,
            "difficulty" => "easy",
            "cuisine_type" => "Mediterranean"
        ]
    ];

    echo "Inserting sample recipes...<br>";
    foreach ($sampleRecipes as $recipe) {
        $title = $conn->real_escape_string($recipe['title']);
        $image = $conn->real_escape_string($recipe['image']);
        $ingredients = $conn->real_escape_string($recipe['ingredients']);
        $instructions = $conn->real_escape_string($recipe['instructions']);
        $prep_time = $recipe['prep_time'];
        $cook_time = $recipe['cook_time'];
        $servings = $recipe['servings'];
        $difficulty = $recipe['difficulty'];
        $cuisine_type = $conn->real_escape_string($recipe['cuisine_type']);

        $sql = "INSERT INTO recipes (title, image, ingredients, instructions, prep_time, cook_time, servings, difficulty, cuisine_type) 
                VALUES ('$title', '$image', '$ingredients', '$instructions', $prep_time, $cook_time, $servings, '$difficulty', '$cuisine_type')";

        if ($conn->query($sql)) {
            echo "Added recipe: {$recipe['title']} <br>";
        } else {
            throw new Exception("Error adding recipe {$recipe['title']} : $conn->error");
        }
    }

    // If we reach this point, commit the transaction
    $conn->commit();
    echo "<strong>Transaction committed successfully!</strong><br>";
    echo "Setup completed successfully. All operations were completed without errors.<br>";

} catch (Exception $e) {
    // An error occurred, rollback the transaction
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
        echo "<strong>Transaction rolled back due to error:</strong> {$e->getMessage()} <br>";
    } else {
        echo "<strong>Database connection error:</strong> {$e->getMessage()} <br>";
    }

    echo "Setup failed. Please check the error message above and try again.<br>";
    exit;
} finally {
    // Close connection if it exists
    if ($conn->ping()) {
        $conn->close();
    }
}

// Redirect to home page after 5 seconds
header("refresh:5;url=http://localhost/example/");
echo "Redirecting to home page in 5 seconds...";

