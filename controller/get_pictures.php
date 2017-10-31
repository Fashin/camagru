<?php

  if (!(isset($db)))
    require_once '../config/database.php';

  if (isset($_GET['picture']) && isset($_GET['range_start']) && isset($_GET['range_end']))
    $page = array($_GET['range_start'], $_GET['range_end']);

  if (isset($_GET['user_space']))
    require_once("../config/database.php");

  if (isset($_SESSION['id']) && isset($_GET['user_space']))
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
  else if (isset($page) && is_array($page))
  {
    $ret = $db->query('SELECT * FROM picture ORDER BY time DESC limit ' . $page[0] . ', ' . $page[1]);
    $pic = $ret->fetchAll();
    foreach ($pic as $k => $v)
    {
      echo "<div id='picture-container' id_picture='". $v['id'] . "'>";
        echo "<img src=" . $v['path'] . "><br>";
        if (isset($_SESSION['id']))
        {
          $req = $db->prepare("SELECT id FROM interract WHERE id_user=:id_user AND id_picture=:id_picture AND type='like'");
          $bool = $req->execute(array(
            ':id_user' => $_SESSION['id'],
            ':id_picture' => $v['id']
          ));
          $id = 0;
          if (!empty($ret = $req->fetchAll()))
            $id = $ret[0]['id'];
          if (!empty($id))
            echo "<img id_interract='" . $id . "' src='public/pictures/fill_hearth.png' class='hearth'>";
          else
            echo "<img id_interract='-1' src='public/pictures/empty_hearth.png' class='hearth'>";
          $req->closeCursor();
        }
        else
          echo "<img src='public/pictures/empty_hearth.png' class='hearth'>";
        $req = $db->query("
          SELECT user.login, interract.*
          FROM user INNER JOIN interract
          WHERE interract.id_picture=" . $v['id'] . " AND interract.type='comment' AND user.id=interract.id_user
          ");
        if ($req && !empty($req))
        {
          echo "<div class='interract-container'>";
          while ($ret = $req->fetchAll())
          {
            foreach ($ret as $k => $v)
            {
              echo "<div class='interract'>";
                echo "<span class='pseudo'>" . $v['login'] . "</span>";
                echo "<span class='value'>" . $v['value'] . "</span><br>";
                if (isset($_SESSION['id']) && $v['id_user'] == $_SESSION['id'])
                {
                  echo "<a href='#'>modifier</a>";
                  echo "<a href='#'>supprimer</a>";
                }
              echo "</div>";
            }
          }
          echo "</div>";
        }
        echo "<input type='text' placeholder='Votre commentaire...' class='comment'/>";
        echo "<img class='send' src='public/pictures/arrow.png'/>";
      echo "</div>";
    }
  }
  else
    echo "-1";

?>
