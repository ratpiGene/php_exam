<?php
$hello = "Créez votre compte";
?>
<button><a href="index.php">Accueil</a></button>
<button><a href="login.php">Connexion</a></button>
<head>
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
</head>
<h1> 
    <?php echo $hello 
    ?> ! 
</h1> 
<?php

require('config.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["email"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "SELECT * FROM user WHERE name='$username' OR mail='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      echo "Username or email already exists. Please try a different one.";
    } else {
      $sql = "INSERT INTO user (name, pwd, mail)
            VALUES ('$username', '$hashed_password', '$email')";
      if (mysqli_query($conn, $sql)) {
        echo "Successfully registered.";
      } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
      }
    }
  } else {
    echo "Please fill out all the required fields.";
  }
}   

mysqli_close($conn);

?>
<form action="" method="post">
  <input type="text" name="username" placeholder="Username">
  <input type="password" name="password" placeholder="Password">
  <input type="email" name="email" placeholder="Email">
  <input type="submit" value="Register">
</form>

