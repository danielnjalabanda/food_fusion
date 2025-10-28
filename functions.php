<?php

include "config.php";

// Function to get recipe excerpt
function getExcerpt($text, $length = 100)
{
    if (strlen($text) <= $length) {
        return $text;
    }
    $excerpt = substr($text, 0, $length);
    $lastSpace = strrpos($excerpt, ' ');
    return substr($excerpt, 0, $lastSpace) . '...';
}

// Function to format cooking time
function formatTime($minutes)
{
    if ($minutes < 60) {
        return $minutes . ' mins';
    } else {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        if ($mins == 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        } else {
            return $hours . 'h ' . $mins . 'm';
        }
    }
}

// Helper function to check if recipe is bookmarked
function isBookmarked($conn, $user_id, $recipe_id) {
    $stmt = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookmarked = $result->num_rows > 0;
    $stmt->close();

    return $bookmarked;
}

// Single getRecipes function with bookmark functionality
function getRecipes($type)
{
    global $conn;

    $select = "SELECT * FROM recipes";
    $whereClause = "";
    $orderBy = " ORDER BY created_at DESC";

    switch ($type) {
        case 'featured':
            $whereClause = "WHERE is_featured = 1";
            break;
        case 'recent':
            $orderBy = "ORDER BY created_at DESC LIMIT 5";
            break;
        case 'all':
            // Handled later to retrieve all recipes
            break;
        default:
            break;
    }

    // Retrieve recipes from database
    $sql = $select . " " . $whereClause . " " . $orderBy;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $delay = 100; // Initial delay for animations

        while ($row = $result->fetch_assoc()) {
            // Calculate total time
            $total_time = $row['prep_time'] + $row['cook_time'];
            $formatted_time = formatTime($total_time);

            // Get excerpt from instructions
            $excerpt = getExcerpt($row['instructions']);

            // Determine image path
            $image_path = "assets/images/recipes/" . $row['image'];
            $default_image = "assets/images/recipes/default-recipe.jpg";

            // Check if image exists, use default if not
            $final_image = file_exists($image_path) ? $image_path : $default_image;

            // Check if recipe is bookmarked by current user
            $isBookmarked = false;
            $bookmarkTitle = 'Save Recipe';
            $bookmarkIcon = 'far fa-bookmark';

            if (isset($_SESSION['user_id'])) {
                $isBookmarked = isBookmarked($conn, $_SESSION['user_id'], $row['id']);
                if ($isBookmarked) {
                    $bookmarkTitle = 'Remove from Bookmarks';
                    $bookmarkIcon = 'fas fa-bookmark';
                }
            }

            echo '
        <div class="recipe-card" data-aos="fade-up" data-aos-delay="' . $delay . '">
            <div class="card-image-container">
                <img src="' . $final_image . '" alt="' . htmlspecialchars($row['title']) . '" class="recipe-image">
                <div class="image-overlay"></div>
                <span class="difficulty-badge ' . strtolower($row['difficulty']) . '">' . ucfirst($row['difficulty']) . '</span>
            </div>
            <div class="card-content">
                <h3 class="recipe-title">' . htmlspecialchars($row['title']) . '</h3>
                <div class="recipe-meta">
                    <span class="meta-item"><i class="fas fa-clock"></i> ' . $formatted_time . '</span>
                    <span class="meta-item"><i class="fas fa-utensils"></i> ' . $row['servings'] . ' serving' . ($row['servings'] > 1 ? 's' : '') . '</span>
                </div>';

            // Display cuisine type if available
            if (!empty($row['cuisine_type'])) {
                echo '<div class="recipe-tags">
                    <span class="tag cuisine-tag">' . htmlspecialchars($row['cuisine_type']) . '</span>
                  </div>';
            }

            echo '
                <p class="recipe-excerpt">' . htmlspecialchars($excerpt) . '</p>
                <div class="card-actions">
                    <a href="recipe-details.php?id=' . $row['id'] . '" class="view-recipe-btn">View Recipe</a>
                    <button class="save-recipe-btn" data-recipe-id="' . $row['id'] . '" title="' . $bookmarkTitle . '">
                        <i class="' . $bookmarkIcon . '"></i>
                    </button>
                </div>
            </div>
        </div>';

            $delay += 100; // Increment delay for next card
        }
    } else {
        echo '<div class="no-recipes">
            <i class="fas fa-utensils"></i>
            <h3>No Recipes Found</h3>
            <p>Be the first to add a recipe to our collection!</p>
            <a href="submit-recipe.php" class="cta-button">Submit Your Recipe</a>
          </div>';
    }
}

/**
 * Render recipe cards for the Tips/Download page with a Download PDF button.
 * This function assumes $conn is available (like other functions in functions.php).
 */
function downloadRecipes()
{
    global $conn;

    $sql = "SELECT * FROM recipes ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<p class="error">Unable to load recipes.</p>';
        return;
    }

    if ($result->num_rows > 0) {
        $delay = 100;
        while ($row = $result->fetch_assoc()) {
            // Calculate total time
            $total_time = intval($row['prep_time']) + intval($row['cook_time']);
            $formatted_time = formatTime($total_time);

            // Get excerpt from instructions
            $excerpt = getExcerpt($row['instructions']);

            // Determine image path
            $image_path = "assets/images/recipes/" . $row['image'];
            $default_image = "assets/images/recipes/default-recipe.jpg";
            $final_image = (isset($row['image']) && $row['image'] != '' && file_exists($image_path)) ? $image_path : $default_image;

            // Output card with Download button
            echo '
            <div class="recipe-card" data-aos="fade-up" data-aos-delay="' . $delay . '">
                <div class="card-image-container">
                    <img src="' . $final_image . '" alt="' . htmlspecialchars($row['title']) . '" class="recipe-image">
                    <div class="image-overlay"></div>
                </div>
                <div class="card-content">
                    <h3 class="recipe-title">' . htmlspecialchars($row['title']) . '</h3>
                    <p class="recipe-excerpt">' . htmlspecialchars($excerpt) . '</p>
                    <div class="recipe-meta">
                        <span class="meta-item"><i class="fas fa-clock"></i> ' . $formatted_time . '</span>
                        <span class="meta-item"><i class="fas fa-user"></i> ' . intval($row['servings']) . ' servings</span>
                    </div>
                    <div class="card-actions">
                        <a href="download_recipe.php?recipe_id=' . intval($row['id']) . '" class="cta-button small">Download PDF</a>
                        <a href="recipe.php?id=' . intval($row['id']) . '" class="cta-button small outline">View Recipe</a>
                    </div>
                </div>
            </div>';
            $delay += 100;
        }
    } else {
        echo '<div class="no-recipes">
                <i class="fas fa-utensils"></i>
                <h3>No Recipes Found</h3>
                <p>Be the first to add a recipe to our collection!</p>
                <a href="submit-recipe.php" class="cta-button">Submit Your Recipe</a>
              </div>';
    }
}

