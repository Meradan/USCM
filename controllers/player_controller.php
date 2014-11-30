<?php
Class PlayerController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  public function getPlayer($playerId) {
    if ($playerId == NULL) {
      return;
    }
    $player = new Player();
    $playersql = "SELECT Users.id, forname, nickname, lastname, emailadress, use_nickname, platoon_id,
        logintime, lastlogintime, GMs.userid as gm, GMs.RPG_id, GMs.active, ".
        "Admins.userid as admin, count(*) as howmany FROM Users " .
        "LEFT JOIN GMs on GMs.userid = Users.id " .
        "LEFT JOIN Admins on Admins.userid = Users.id WHERE Users.id = :userid";
    $stmt = $this->db->prepare($playersql);
    $stmt->bindValue(':userid', $playerId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row ['howmany'] == 1) {
//       $player->setId($playerId);
//       $player->setGivenName($row ['forname']);
//       $player->setNickname($row ['nickname']);
//       $player->setSurname($row ['lastname']);
//       $player->setEmailaddress($row ['emailadress']);
//       $player->setUseNickname($row ['use_nickname']);
//       $player->setPlatoonId($row ['platoon_id']);
//       $player->setLoginTime($row ['logintime']);
//       $player->setLastLoginTime($row ['lastlogintime']);
//       if ($row['gm']) {
//         $player->setGm(TRUE);
//       } else {
//         $player->setGm(FALSE);
//       }
//       $player->setGmRpgId($row['RPG_id']);
//       $player->setGmActive($row['active']);
//       if ($row['admin']) {
//         $player->setAdmin(TRUE);
//       } else {
//         $player->setAdmin(FALSE);
//       }
      $player = $this->assignPlayerData($row);
    }
    return $player;
  }

  public function getAllPlayers() {
    $playersql = "SELECT Users.id, forname, nickname, lastname, emailadress, use_nickname, platoon_id,
        logintime, lastlogintime, GMs.userid as gm, GMs.RPG_id, GMs.active, ".
        "Admins.userid as admin FROM Users " .
        "LEFT JOIN GMs on GMs.userid = Users.id " .
        "LEFT JOIN Admins on Admins.userid = Users.id ORDER BY lastname, forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->execute();
    $playerList = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $playerList[] = $this->assignPlayerData($row);
    }
    return $playerList;
  }

  private function  assignPlayerData($data) {
    $player = new Player();
    $player->setId($data ['id']);
    $player->setGivenName($data ['forname']);
    $player->setNickname($data ['nickname']);
    $player->setSurname($data ['lastname']);
    $player->setEmailaddress($data ['emailadress']);
    $player->setUseNickname($data ['use_nickname']);
    $player->setPlatoonId($data ['platoon_id']);
    $player->setLoginTime($data ['logintime']);
    $player->setLastLoginTime($data ['lastlogintime']);
    if ($data['gm']) {
      $player->setGm(TRUE);
    } else {
      $player->setGm(FALSE);
    }
    $player->setGmRpgId($data['RPG_id']);
    $player->setGmActive($data['active']);
    if ($data['admin']) {
      $player->setAdmin(TRUE);
    } else {
      $player->setAdmin(FALSE);
    }
    return $player;
  }

  public function getPlayersInPlatoon($platoonId) {
    $playersql = "SELECT Users.id,forname,lastname,name_short FROM Users
                  LEFT JOIN uscm_platoon_names pn ON pn.id=Users.platoon_id
                  WHERE platoon_id=:platoonid
                  ORDER BY platoon_id,lastname,forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->bindValue(':platoonid', $platoonId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getGms() {
    $gms = array();
    $gmsql = "SELECT Users.id,forname,lastname FROM Users LEFT JOIN GMs on GMs.userid=Users.id
                LEFT JOIN RPG on RPG.id=GMs.rpg_id
                WHERE table_prefix='{$_SESSION['table_prefix']}'";
    $stmt = $this->db->prepare($gmsql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $gm = $this->getPlayer($row['id']);
      $gms[] = $gm;
    }
    return $gms;

  }
}
