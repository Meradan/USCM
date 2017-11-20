<?php
class Player extends DbEntity {
  private $level = 0;
  private $playerPlatoon = 0;
  private $db = NULL;
  private $givenName = NULL;
  private $nickname = NULL;
  private $surname = NULL;
  private $emailaddress = NULL;
  private $password = NULL;
  private $use_nickname = NULL;
  private $platoon_id = NULL;
  private $logintime = NULL;
  private $lastlogintime = NULL;
  private $gm = NULL;
  private $gmRpgId = NULL;
  private $gmActive = NULL;
  private $admin = NULL;
  private $playerActive = NULL;

  function __construct($playerId = NULL) {
    $this->level = $_SESSION ['level'];
    if (array_key_exists('platoon_id', $_SESSION)) {
      $this->playerPlatoon = $_SESSION ['platoon_id'];
    }
    $this->db = getDatabaseConnection();
    if ($playerId == NULL) {
      if (array_key_exists('user_id', $_SESSION)) {
        $this->id = $_SESSION ['user_id'];
      }
    } else {
      $this->id = $playerId;
    }
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getName() {
    return $this->givenName . ' ' . $this->surname;
  }

  public function getNameWithNickname() {
    $name = $this->givenName;
    if ($this->use_nickname) {
      $name .= " '" . $this->nickname . "'";
    }
    $name .= " " . $this->surname;
    return $name;
  }

    public function getGivenName() {
    return $this->givenName;
  }

  public function setGivenName($name) {
    $this->givenName = $name;
  }

  public function getSurname() {
    return $this->surname;
  }

  public function setSurname($name) {
    $this->surname = $name;
  }

  public function getUseNickname() {
    return $this->use_nickname;
  }

  public function setUseNickname($use) {
    $this->use_nickname = $use;
  }

  public function getNickname() {
    return $this->nickname;
  }

  public function setNickname($name) {
    $this->nickname = $name;
  }

  public function getEmailaddress() {
    return $this->emailaddress;
  }

  public function setEmailaddress($email) {
    $this->emailaddress = $email;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function getPlatoonId() {
    return $this->platoon_id;
  }

  public function setPlatoonId($id) {
    $this->platoon_id = $id;
  }

  public function getLoginTime() {
    return $this->logintime;
  }

  public function setLoginTime($time) {
    $this->logintime = $time;
  }

  public function getLastLoginTime() {
    return $this->lastlogintime;
  }

  public function setLastLoginTime($time) {
    $this->lastlogintime = $time;
  }

  public function getGmRpgId() {
    return $this->gmRpgId;
  }

  public function setGmRpgId($rpgId) {
    $this->gmRpgId = $rpgId;
  }

  public function getGmActive() {
    return $this->gmActive;
  }

  public function setGmActive($active) {
    $this->gmActive = $active;
  }
  
  public function getPlayerActive() {
    return $this->playerActive;
  }

  public function setPlayerActive($active) {
    $this->playerActive = $active;
  }

  public function isAdmin() {
    if ($this->admin != NULL) {
      return $this->admin;
    } else {
      return ($this->level == 3) ? (TRUE) : (FALSE);
    }
  }

  public function setAdmin($value) {
    $this->admin = $value;
  }

  public function isGm() {
  if ($this->gm != NULL) {
      return $this->gm;
    } else {
      return ($this->level == 2) ? (TRUE) : (FALSE);
    }
  }

  public function setGm($value) {
    $this->gm = $value;
  }
}
