<?php

class User
{
  private $_pdo;
  private $_salt;

  function __construct(PDO $obj)
  {
    $this->_pdo = $obj;
    $this->salt = hash('whirlpool', "g1rst35g4065srtg4035rstg435");
  }

  public function get_user($selection = null, $condition = null)
  {
    $sql = "SELECT ";
    $sql .= ($selection) ? $selection : "*";
    $sql .= " FROM user";
    if ($condition)
    {
      $sql .= " WHERE ";
      foreach ($condition as $k => $v)
      {
        if ($k == 'psswd')
          $sql .= $k . "='" . hash('whirlpool', $this->salt . $v) . "' AND ";
        else
          $sql .= $k ."='".$v . "' AND ";
      }

      $sql = substr($sql, 0, -5);
    }
    return ($this->_pdo->query($sql)->fetchAll());
  }

  public function connect($id, $redirect)
  {
    session_start();
    $_SESSION['id'] = $id;
    header('Location:'.$redirect);
  }

  public function new_user(Array $data)
  {
    $require = array("log_in", "email", "psswd", "confirm_psswd", "sign_in-send");
    $pattern = '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{10,}$#';
    foreach ($data as $k => $v)
    {
      if (in_array($k, $require) && !empty($v))
        $data[$k] = htmlspecialchars($v);
      else
        return (-1);
    }
    if ($data['psswd'] != $data['confirm_psswd'])
      return (-2);
    if ($this->get_user("id", array("login"=>$data['log_in'])))
      return (-3);
    if (!preg_match($pattern, $data['psswd']))
      return (-4);
    $sql = "INSERT INTO user (login, email, psswd, is_confirmed) VALUES ";
    $sql .= "('" . $data['log_in'] . "', '" . $data['email'] . "', '" . hash('whirlpool', $this->salt . $data['psswd']) .  "', 0)";
    $text = "Salut a tous !";
    mail($data['email'], "subscribe to Cyprian's Camagru", $text);
    $ret['errror'] = ($this->_pdo->query($sql)) ? 0 : 1;
    $id = $this->get_user("id", array("login"=>$data['log_in']));
    return ($ret['id'] = $id[0]['id']);
  }
}

?>
