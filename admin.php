<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
  </head>
  <body>
    <?php
      // Check if the user is logged in as an admin
      session_start();
      if (!isset($_SESSION['user_id']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
        header('Location: /login.php');
        die();
      }

      // Connect to the database and get all the articles and users
      $db = new PDO('mysql:host=localhost;dbname=your_database_name;charset=utf8', 'your_username', 'your_password');
      $posts_stmt = $db->query('SELECT * FROM article');
      $users_stmt = $db->query('SELECT * FROM user');

      // Display the article table
      echo '<h2>Articles</h2>';
      echo '<table>';
      echo '<tr><th>ID</th><th>Title</th><th>Content</th><th>Desc</th><th>Price</th></tr>';
      while ($post = $posts_stmt->fetch()) {
        echo '<tr>';
        echo '<td>'.$post['article_id'].'</td>';
        echo '<td>'.$post['name'].'</td>';
        echo '<td>'.$post['description'].'</td>';
        echo '<td>'.$post['price'].'</td>';
        echo '<td><a href="/edit.php?id='.$post['id'].'">Edit</a> <a href="/delete_post.php?id='.$post['id'].'">Delete</a></td>';
        echo '</tr>';
      }
      echo '</table>';

      // Display the users table
      echo '<h2>Users</h2>';
      echo '<table>';
      echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Balance</th><th>Is Admin</th><th>Actions</th></tr>';
      while ($user = $users_stmt->fetch()) {
        echo '<tr>';
        echo '<td>'.$user['user_id'].'</td>';
        echo '<td>'.$user['name'].'</td>';
        echo '<td>'.$user['mail'].'</td>';
        echo '<td>'.$user['sold'].'</td>';
        echo '<td>'.$user['isadmin'].'</td>';
        echo '<td><a href="/edit_user.php?id='.$user['user_id'].'">Edit</a> <a href="/delete_user.php?id='.$user['user_id'].'">Delete</a></td>';
        echo '</tr>';
      }
      echo '</table>';

      // Close the database connection
      $db = null;
    ?>
  </body>
</html>
