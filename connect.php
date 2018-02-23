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
    if (isset($error['error']) && !($error))
    {
      if ($error['mail'])
        header('Location:connect.php?error=mail_delivery');
      else
        header('Location:connect.php?error=register');
    }
    else
    {
      if (is_int($error))
      {
        switch ($error)
        {
          case 1:
            header('Location:connect.php?error=fill');
            break;
          case 2:
            header('Location:connect.php?error=psswd');
            break;
          case 3:
            header('Location:connect.php?error=login_picked');
            break;
          case 4:
            header('Location:connect.php?error=psswd_n_conform');
            break;
          case 5:
            header('Location:connect.php?error=email_picked');
            break;
          default:
            header('Location:connect.php?error=activated');
            break;
        }
      }
      else {
        header('Location:connect.php?success=true');
      }
    }
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
    require_once("controller/User.class.php");
    $ret = new User($db);
    $error = $ret->confirmation_user(htmlspecialchars($_GET['id']));
    if ($error)
      header('Location:connect.php?page=log_in');
    else
      header('Location:connect.php?page=sign_in&error=confirmation');
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
      if (isset($_GET['error']))
      {
        $error = htmlspecialchars($_GET['error']);
        switch ($error)
        {
          case 'activated':
            echo "<div class='activation'>Account created, please activate it !</div>";
            break;
          case 'fill':
            echo "<div class='activation'>Please fill all the field</div>";
            break;
          case 'psswd':
            echo "<div class='activation'>Password not the same</div>";
            break;
          case 'login_picked':
            echo "<div class='activation'>Login already exists</div>";
            break;
          case 'psswd_n_conform':
            echo "<div class='activation'>Password must be with a minus and maj letter, a number and special caractere</div>";
            break;
          case 'email_picked':
            echo "<div class='activation'>Email already exists</div>";
            break;
          case 'confirmation':
            echo "<div class='activation'>Error from confirmation request update :(</div>";
            break;
        }
      }
      else if (isset($_GET['success']))
        echo "<div class='activation'>Vous avez bien etait enregistrer, veuillez confirmer votre email</div>";
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
