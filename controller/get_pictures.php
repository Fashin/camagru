<?php

  if (isset($_GET['user_space']))
    require_once("../config/database.php");

  if (isset($_SESSION['id']) && !isset($page))
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
          if (isset($_GET['user_space']))
            $ret = $ret . '<div class="delete" id_picture="' . $v['id'] . '"><img class="bean" src="public/pictures/bean.png"/></div>';
        $ret = $ret . '</div>';
      }
      echo $ret;
    }
    else if (isset($_GET['delete']))
      echo $user->delete_picture($_GET['delete']);
    else
      echo "-2";
  }
  else if (isset($page) && $page[0] == "homepage")
  {
    $ret = $db->query('SELECT * FROM picture ORDER BY time DESC limit ' . $page[1][0] . ', ' . $page[1][1]);
    $pic = $ret->fetchAll();
    foreach ($pic as $k => $v)
    {
      echo "<div id='picture-container'>";
        echo "<img src=" . $v['path'] . "><br>";
        echo "<img src='public/pictures/empty_hearth.png' class='hearth'>";
        echo "<textarea></textarea>";
      echo "</div>";
    }
  }
  else
    echo "-1";

?>
