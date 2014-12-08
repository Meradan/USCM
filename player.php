<?php
/*
 *	Functions handling players should go in here
 *
 */
session_start();
include("functions.php");
$userController = new UserController();
$playerController = new PlayerController();
$user = $userController->getCurrentUser();

$forname=$_POST['forname'];
$lastname=$_POST['lastname'];
$nickname=$_POST['nickname'];
$emailadress=$_POST['emailadress'];
$password=$_POST['password'];
$use_nickname=$_POST['use_nickname'];
$platoon_id=$_POST['platoon_id'];

if ($_GET['what']=="create" && ($user->isAdmin() || $user->isGm())) {
  $player = new Player();
  $player->setGivenName($forname);
  $player->setSurname($lastname);
  $player->setNickname($nickname);
  $player->setEmailaddress($emailadress);
  $player->setPassword($password);
  $player->setUseNickname($use_nickname);
  $player->setPlatoonId($platoon_id);
  $playerController->save($player);
}
elseif ($_GET['what']=="modify" && ($user->isAdmin() || $user->isGm() || $user->getId() == $_POST['id'])) {
  $player = $playerController->getPlayer($_POST['id']);
  if ($_POST['password']) {
    $player->setPassword($_POST['password']);
    $playerController->updatePassword($player);
  }
  $player->setGivenName($forname);
  $player->setSurname($lastname);
  $player->setNickname($nickname);
  $player->setEmailaddress($emailadress);
  $player->setPassword($password);
  $player->setUseNickname($use_nickname);
  $player->setPlatoonId($platoon_id);
  $playerController->update($player);
}

header("location:{$url_root}/index.php?url=modify_player.php");

?>
