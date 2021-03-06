<?php

  require_once('setup.php');

  session_start();

  try {

    $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = ("CREATE DATABASE IF NOT EXISTS `$DB_NAME`");

    $db->exec($sql);

    $sql = "use `$DB_NAME`";

    $db->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS user (
            id int(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            login VARCHAR(255) NOT NULL,
            psswd VARCHAR(255) NOT NULL,
            is_confirmed INT);";
    $db->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS interract (
            id int(11) AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            id_picture INT NOT NULL,
            id_user INT NOT NULL,
            value VARCHAR(255) NOT NULL);";
    $db->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS picture (
            id int (11) AUTO_INCREMENT PRIMARY KEY,
            path VARCHAR(255) NOT NULL,
            id_user INT,
            time timestamp on update current_timestamp not null default current_timestamp);";

    $db->exec($sql);

  } catch (PDOException $e) {
    echo "DB error : " . $e->getMessage();
  }

?>
