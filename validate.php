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

// Get current user's balance
$user_id = $_SESSION["user_id"];
$sql = "SELECT sold FROM user WHERE user_id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$current_balance = $user['sold'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $billing_info = mysqli_real_escape_string($conn, $_POST["billing_info"]);

    // Check if user's balance is sufficient
    $total_price = 0;
    $cart = $_SESSION['cart'];
    foreach ($cart as $article_id => $quantity) {
        $sql = "SELECT price FROM article WHERE article_id = $article_id";
        $result = $conn->query($sql);
        $article = $result->fetch_assoc();
        $total_price += $quantity * $article['price'];
    }
    if ($total_price > $current_balance) {
        echo "Error: Insufficient balance";
    } else {
        // Update user's balance and create invoice
        $new_balance = $current_balance - $total_price;
        $sql = "UPDATE user SET sold = $new_balance WHERE user_id = $user_id";
        $conn->query($sql);
        $sql = "INSERT INTO invoice (user_id, total_price, billing_info) VALUES ($user_id, $total_price, '$billing_info')";
        $conn->query($sql);
        // Empty cart
        $_SESSION['cart'] = array();
        echo "Order validated. Thank you for your purchase!";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirmation</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
</head>
<body>
    <h1>Confirmation</h1>

    <p>Total price: <?php echo $total_price; ?></p>
    <p>Current balance: <?php echo $current_balance; ?></p>

    <form action="" method="post">
        <label for="billing_info">Billing Information:</label>
        <textarea id="billing_info" name="billing_info" required></textarea><br>

        <input type="submit" value="Validate Order">
    </form>
</body>
</html>
