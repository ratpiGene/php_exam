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
    $image_link = mysqli_real_escape_string($conn, $_POST["image_link"]);
    $article_id = $_POST["article_id"];

    // Check if user is authorized to edit the article
    $sql = "SELECT user_id FROM article WHERE article_id=$article_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row["user_id"];
    } else {
        echo "Article not found";
        exit();
    }

    if ($user_id != $_SESSION["user_id"] && $_SESSION["isadmin"] != 1) {
        echo "You are not authorized to edit this article";
        exit();
    }

    // Update the article in the database
    $sql = "UPDATE article SET name='$name', description='$description', price=$price, image_link='$image_link' WHERE article_id=$article_id";

    if ($conn->query($sql) === TRUE) {
        echo "Article updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // Check if user is authorized to access the edit page
    $article_id = $_GET["article_id"];

    $sql = "SELECT * FROM article WHERE article_id=$article_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row["user_id"];

        if ($user_id != $_SESSION["user_id"] || $_SESSION["isadmin"] != 1) {
            echo "You are not authorized to edit this article";
            exit();
        }

        // Show the edit form
        $name = $row["name"];
        $description = $row["description"];
        $price = $row["price"];
        $image_link = $row["image_link"];
        ?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Article</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
</head>
<body>
    <h1>Edit Article</h1>

    <form action="edit.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required value="Article Name"><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required>Article Description</textarea><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required value="10.99"><br>

        <label for="image_link">Image Link:</label>
        <input type="text" id="image_link" name="image_link" required value="https://example.com/image.png"><br>

        <label for="number_stock">Number in Stock:</label>
        <input type="number" id="number_stock" name="number_stock" required value="10"><br>

        <input type="hidden" name="article_id" value="1">

        <input type="submit" value="Save Changes">
    </form>
</body>
</html>
