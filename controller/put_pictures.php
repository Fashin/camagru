<?php

$img_background = (isset($_POST['img_background'])) ? htmlentities($_POST['img_background']) : NULL;
$filtre_background = (isset($_POST['filtre_background'])) ? htmlentities($_POST['filtre_background']) : NULL;
$filtre_bg_background = (isset($_POST['filtre_bg_background'])) ? htmlentities($_POST['filtre_bg_background']) : NULL;


if ($img_background && $filtre_background && $filtre_bg_background)
{


  require_once('../config/database.php');
  require_once("User.class.php");

  $user = new User($db, $_SESSION['id']);
  $user->add_picture();
  $img_background = str_replace('data:image/png;base64,', '', $img_background);
  $img_background = str_replace(' ', '+', $img_background);
  $img_background = base64_decode($img_background);
  $img_background = imagecreatefromstring($img_background);

  $filtre_background = str_replace('data:image/png;base64,', '', $filtre_background);
  $filtre_background = str_replace(' ', '+', $filtre_background);
  $filtre_background = base64_decode($filtre_background);
  $filtre_background = imagecreatefromstring($filtre_background);

  $filtre_bg_background = str_replace('data:image/png;base64,', '', $filtre_bg_background);
  $filtre_bg_background = str_replace(' ', '+', $filtre_bg_background);
  $filtre_bg_background = base64_decode($filtre_bg_background);
  $filtre_bg_background = imagecreatefromstring($filtre_bg_background);

  $image_filtre_largeur = imagesx($filtre_background);
  $image_filtre_hauteur = imagesy($filtre_background);

  $image_filtre_bg_largeur = imagesx($filtre_bg_background);
  $image_filtre_bg_hauteur = imagesy($filtre_bg_background);

  imagecopy($img_background, $filtre_background, 0, 0, 0, 0, $image_filtre_largeur, $image_filtre_hauteur);
  imagecopy($img_background, $filtre_bg_background, 0, 0, 0, 0, $image_filtre_bg_largeur, $image_filtre_bg_hauteur);
  header('Content-Type: image/png');
  imagepng($img_background, '../public/pictures/user_pictures/' . $user->last_saved);
  echo $user->last_saved;
}
else
  echo "-1";

?>
