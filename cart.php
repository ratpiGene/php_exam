<?php
session_start();
include 'config.php';

// check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
}

// get user's cart items from database
$user_id = $_SESSION['user_id'];
$sql = "SELECT article.article_id, article.name, article.price, cart.quantity FROM cart 
        INNER JOIN article ON cart.article_id = article.article_id WHERE cart.user_id = $user_id";
$result = mysqli_query($conn, $sql);

// place order if balance is sufficient
if (isset($_POST['place_order'])) {
  $total_price = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    $total_price += $row['price'] * $row['quantity'];
  }
  $user_balance = $_SESSION['balance'];
  if ($user_balance >= $total_price) {
    // deduct total price from user's balance
    $new_balance = $user_balance - $total_price;
    $sql = "UPDATE users SET balance = $new_balance WHERE user_id = $user_id";
    mysqli_query($conn, $sql);
    // clear user's cart
    $sql = "DELETE FROM cart WHERE user_id = $user_id";
    mysqli_query($conn, $sql);
    $_SESSION['balance'] = $new_balance;
    $message = "Order placed successfully!";
  } else {
    $message = "Insufficient balance to place order!";
  }
}

// update item quantity in cart
if (isset($_POST['update_cart'])) {
  $article_id = $_POST['article_id'];
  $quantity = $_POST['quantity'];
  $sql = "UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND article_id = $article_id";
  mysqli_query($conn, $sql);
}

// delete item from cart
if (isset($_POST['delete_cart'])) {
  $article_id = $_POST['article_id'];
  $sql = "DELETE FROM cart WHERE user_id = $user_id AND article_id = $article_id";
  mysqli_query($conn, $sql);
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Cart</title>
</head>
<body>
  <h1>Cart</h1>
  <table>
    <tr>
      <th>Article</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?php echo $row['name']; ?></td>
      <td><?php echo $row['price']; ?></td>
      <td>
        <form method="POST">
          <input type="hidden" name="article_id" value="<?php echo $row['article_id']; ?>">
          <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>">
          <button type="submit" name="update_cart">Update</button>
        </form>
      </td>
      <td>
        <form method="POST">
          <input type="hidden" name="article_id" value="<?php echo $row['article_id']; ?>">
          <button type="submit" name="delete_cart">Delete</button>
        </form>
      </td>
    </tr>
    <?php } ?>
 
