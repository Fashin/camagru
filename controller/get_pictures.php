<?php

  require_once("../config/database.php");

  if (isset($_SESSION['id']))
  {
    require_once("User.class.php");

    $user = new User($db, $_SESSION['id']);

    if (!isset($_GET['delete']))
    {
      $user_pic = $user->get_pictures();

      $ret = "";

      foreach ($user_pic as $k => $v)
      {
        $ret = $ret . '<div class="picture">';
          $ret = $ret . '<img src="' . $v['path'] . '"/>';
          $ret = $ret . '<div class="delete" id_picture="' . $v['id'] . '"><img class="bean" src="public/pictures/bean.png"/></div>';
        $ret = $ret . '</div>';
      }
      echo $ret;
    }
    else
      echo $user->delete_picture($_GET['delete']);
  }
  else
    echo "-1";

?>
