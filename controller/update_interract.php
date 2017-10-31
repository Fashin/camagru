<?php

  if (!(isset($db)))
    require_once '../config/database.php';

  function insert_interract(Array $params, PDO $db, $ret = false)
  {
    $req = $db->prepare("INSERT INTO interract (id_picture, id_user, type, value) VALUES (:id_picture, :id_user, :type, :value)");
    $bool = $req->execute($params);
    if ($ret)
    {
      $login = $db->query("SELECT login FROM user WHERE id=" . $_SESSION['id'])->fetchAll()[0]['login'];
      $return = "<span class='pseudo'>" . $login . "</span>";
      $return .= "<span class='value'>" . $ret . "</span>";
      $return .= "<a href='#'>modifier</a>";
      $return .= "<a href='#'>supprimer</a>";
      return ($return);
    }
    else
      return ($db->lastInsertId());
  }

  if (isset($_POST['id']) && isset($_POST['state']) && isset($_POST['type']))
  {
    $id = htmlspecialchars($_POST['id']);
    $state = htmlspecialchars($_POST['state']);
    $type = htmlspecialchars($_POST['type']);
    $id_interract = htmlspecialchars($_POST['id_interract']);

    if ($type == "like")
    {
      if ($state)
        echo $db->exec("DELETE FROM interract WHERE id=" . $id_interract);
      else
      {
        $params = array(
          ':id_picture' => $id,
          ':id_user' => $_SESSION['id'],
          ':type' => $type,
          ':value' => '0'
        );
        echo insert_interract($params, $db);
      }
    }
    else if ($type == "comment")
    {
      $val = htmlspecialchars($_POST['value']);
      $params = array(
        ':id_picture' => $id,
        ':id_user' => $_SESSION['id'],
        ':type' => $type,
        ':value' => $val
      );
      echo insert_interract($params, $db, $val);
    }
  }
  else
    echo "bad request send";

?>
