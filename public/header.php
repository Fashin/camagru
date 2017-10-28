<?php

  $src = explode('/', $_SERVER['REQUEST_URI']);

  $stylesheet = (empty($src[count($src) - 1])) ? "index" : explode('.', $src[count($src) - 1])[0];

  $is_connected = (isset($_SESSION['id'])) ? true : false;

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Camagru</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/<?= $stylesheet; ?>.css">
  </head>
  <body>
    <header>
      <a href="index.php"><span class="title">Camagru</span></a>
      <?php

        if ($is_connected)
        {
          ?>
            <span class="login"></span>
            <span class="avatar"></span>
            <a href="connect.php?page=logout"><span class="logout">Log Out</span></a>
            <a href="user_space.php"><span class="personal_space">Espace Personnel</span></a>
          <?php
        }
        else
        {
          ?>
          <a href="connect.php?page=log_in"><span class="register">Login</span></a>
          <a href="connect.php?page=sign_in"><span class="signin">Sign In</span></a>
          <?php
        }

      ?>
    </header>
