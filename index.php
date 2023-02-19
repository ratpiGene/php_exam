<button><a href="login.php">Se connecter</a></button>
<button><a href="register.php">S'inscrire</a></button>
<?php
// Database connection configuration
require('config.php');
// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve items from the database
$sql = "SELECT * FROM items ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Display items on the page
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div>';
        echo '<h2>' . $row['title'] . '</h2>';
        echo '<p>' . $row['description'] . '</p>';
        echo '<p>Price: ' . $row['price'] . '</p>';
        if (!isset($_SESSION['user_id'])) {
            echo '<a href="detail.php?article_id='.$article_id.'">Add to cart</a>';
        } else {
            echo '<a href="login.php">Login to buy</a>';
        }
        echo '</div>';
    }
} else {
    echo "No items available";
}

// Close database connection
mysqli_close($conn);
?>
