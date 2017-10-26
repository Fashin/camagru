<?php

$img = (isset($_POST['img'])) ? htmlentities($_POST['img']) : NULL;

if ($img)
{
  require_once('../config/database.php');
  require_once("User.class.php");

  $img = str_replace('data:image/png;base64,', '', $img);
  $img = str_replace(' ', '+', $img);
  $img = base64_decode($img);
  $user = new User($db, $_SESSION['id']);
  $user->add_picture();
  file_put_contents('../public/pictures/user_pictures/' . $user->last_saved, $data);
  echo $user->last_saved;
}
else
  echo "-1";

?>
