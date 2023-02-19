<?php
// Connect to the database
require('config.php');
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if an article_id is provided in the GET request
if (isset($_GET['article_id'])) {
  // Get the article information from the database
  $article_id = $_GET['article_id'];
  $query = "SELECT * FROM article WHERE article_id = $article_id";
  $result = mysqli_query($conn, $query);
  
  // If the article exists, display its details
  if ($result && mysqli_num_rows($result) > 0) {
    $article = mysqli_fetch_assoc($result);
    ?>
    <h1><?php echo $article['name']; ?></h1>
    <p><?php echo $article['description']; ?></p>
    <p>Price: <?php echo $article['price']; ?></p>
    <img src="<?php echo $article['image_link']; ?>" alt="<?php echo $article['name']; ?>">
    <form action="add_to_cart.php" method="POST">
      <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
      <input type="submit" value="Add to cart">
    </form>
    <?php
  } else {
    // If the article does not exist, display an error message
    echo "Article not found.";
  }
} else {
  // If no article_id is provided, display an error message
  echo "Article not specified.";
}
?>
