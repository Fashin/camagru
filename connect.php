<?php

  require_once("config/database.php");

  if (isset($_POST['log_in-send']))
  {
    if (isset($_POST['log_in']) && !empty($_POST['log_in'])
      && isset($_POST['psswd']) && !empty($_POST['psswd']))
      {
        require_once("controller/User.class.php");
        $login = htmlspecialchars($_POST['log_in']);
        $psswd = htmlspecialchars($_POST['psswd']);
        $user = new User($db);
        $rep = $user->get_user(null, array("login"=>$login, "psswd"=>$psswd));
        if ($rep && isset($rep[0]['id']))
        {
          if ($rep[0]['is_confirmed'] == 1)
            $user->connect($rep[0]['id'], 'index.php');
          else
            header('Location:connect.php?error=activated');
        }
        else
          header('Location:connect.php?error=log_in');
      }
  }
  else if (isset($_POST['sign_in-send']))
  {
    require_once("controller/User.class.php");
    $user = new User($db);
    $error = $user->new_user($_POST);
    if (!($error))
    {
      if ($error['mail'])
        header('Location:connect.php?error=mail_delivery');
      else
        header('Location:connect.php?error=register');
    }
    else
      header('Location:connect.php?error=activated');
  }
  else if (isset($_GET['recovery_page']))
  {
    //reset password
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'logout')
  {
    session_start();
    session_destroy();
    header('Location: index.php?info=logout');
  }
  else if (isset($_GET['activation']) && isset($_GET['id']))
  {
    //activated a user
  }
  else
  {
    require_once("public/header.php");

    $page = (isset($_GET['page'])) ? htmlspecialchars($_GET['page']) : "sign_in";

    if ($page == "log_in")
    {
      ?>
      <form class="form-connection" action="connect.php" method="post">
        <label for="log_in">Pseudo :
          <input type="text" name="log_in">
        </label><br><br>
        <label for="psswd">Password :
          <input type="password" name="psswd">
        </label><br><br>
        <a href="connect.php?page=recovery_page">Mot de passe oublier ?</a><br><br>
        <input type="submit" name="log_in-send" value="Connexion">
      </form>
      <?php
    }
    else if ($page == "recovery_page")
    {
      ?>
      <form class="form-connection" action="connect.php" method="post">
        <label for="email">Mon email :
          <input type="email" name="email" value="">
        </label><br><br>
        <input type="submit" name="recovery_page" value="Envoyer moi mon mot de passe !">
      </form>
      <?php
    }
    else
    {
      if (isset($_GET['error']) && $_GET['error'] == "activated")
        echo "<div class='activation'>Account created, please activate it !</div>";
      ?>

      <form class="form-connection" action="connect.php" method="post">
        <label for="log_in">Pseudo :
          <input type="text" name="log_in">
        </label><br><br>
        <label for="email">Email :
          <input type="email" name="email">
        </label><br><br>
        <label for="password">Password :
          <input type="password" name="psswd">
        </label><br><br>
        <label for="confirm_passwd">Confirm password :
          <input type="password" name="confirm_psswd">
        </label><br><br>
        <input type="submit" name="sign_in-send" value="Register">
      </form>
      <?php
    }
  }

?>
