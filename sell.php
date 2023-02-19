<button><a href="index.php">Accueil</a></button>
<?php
session_start();
require('config.php');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $price = floatval($_POST["price"]);
    $user_id = $_SESSION["user_id"];
    $image_link = mysqli_real_escape_string($conn, $_POST["image_link"]);
    $stock = intval($_POST["stock"]);

    // Insert new article into database
    $sql = "INSERT INTO article (name, description, price, date, user_id, image_link)
            VALUES ('$name', '$description', $price, NOW(), $user_id, '$image_link')";

    if ($conn->query($sql) === TRUE) {
        $article_id = $conn->insert_id; // Get the ID of the newly created article

        // Insert stock quantity into stock table
        $sql = "INSERT INTO stock (article_id, number_stock)
                VALUES ($article_id, $stock)";

        if ($conn->query($sql) === TRUE) {
            echo "New article created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell an Article</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
</head>
<body>
    <h1>Sell an Article</h1>

    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required><br>

        <label for="stock">Stock Quantity:</label>
        <input type="number" id="stock" name="stock" min="1" required><br>

        <label for="image_link">Image Link:</label>
        <input type="text" id="image_link" name="image_link" required><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
