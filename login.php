<button><a href="register.php">S'inscrire</a></button>
<?php
session_start();

require('config.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!empty($_POST["username"]) && !empty($_POST["password"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    $sql = "SELECT * FROM user WHERE name='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $hashed_password = $row["pwd"];
      if (password_verify($password, $hashed_password)) {
        // Password is correct, set session variables and redirect to home page
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["username"] = $row["name"];
        setcookie("user_id", $row["id"], time() + (86400 * 30), "/"); // 30 days
        setcookie("username", $row["name"], time() + (86400 * 30), "/"); // 30 days
        header("Location: index.php");
      } else {
        // Password is incorrect
        echo "Incorrect username or password. Please try again.";
      }
    } else {
      // User not found
      echo "User not found. Please <a href=\"register.php\">create an account</a>.";
    }
  } else {
    // Missing username or password
    echo "Please enter a username and password.";
  }
}

mysqli_close($conn);

?>

<form action="" method="post">
  <input type="text" name="username" placeholder="Username">
  <input type="password" name="password" placeholder="Password">
  <input type="submit" value="Login">
</form>
<button><a href="register.php">Pas de compte ?</a></button>
