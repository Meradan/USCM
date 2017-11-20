<?php
$userController = new UserController();
$playerController = new PlayerController();
$user = $userController->getCurrentUser();
$platoonController = new PlatoonController();
$playerId = 0;
if (array_key_exists('player', $_GET)) {
  $playerId = $_GET['player'];
}
if ($user->isAdmin() || $user->getId() == $playerId) {

    if ($playerId > 0) {
        $player = $playerController->getPlayer($playerId)
        ?>
        <form method="post" action="player.php?what=modify">
            <table width="50%"  border="0" cellspacing="1" cellpadding="1">
                <input type="hidden" name="id" value="<?php echo $player->getId(); ?>">
                <input type="hidden" name="res" value="<?php //echo $player['password']; ?>">
                <tr>
                    <td>Forname</td>
                    <td><input type="text" name="forname" value="<?php echo stripslashes($player->getGivenName()); ?>"></td>
                </tr>
                <tr>
                    <td>Nickname</td>
                    <td><input type="text" name="nickname" value="<?php echo stripslashes($player->getNickname()); ?>"></td>
                </tr>
                <tr>
                    <td>Use nickname instead of real name</td>
                    <td><input type="radio" name="use_nickname" value="1" <?php echo ($player->getUseNickname() == "1") ? ("checked") : (""); ?> >Yes
                        <input type="radio" name="use_nickname" value="0" <?php echo ($player->getUseNickname() == "0") ? ("checked") : (""); ?> >No
                    </td>
                </tr>
                <tr>
                    <td>Lastname</td>
                    <td><input type="text" name="lastname" value="<?php echo stripslashes($player->getSurname()); ?>"></td>
                </tr>
                <tr>
                    <td>emailadress</td>
                    <td><input type="text" name="emailadress" value="<?php echo stripslashes($player->getEmailaddress()); ?>"></td>
                </tr>
                <tr>
                    <td>Platoon</td>
                    <td><?php
                    $platoons = $platoonController->getPlatoons(); ?>
                    <select name="platoon_id"><?php
                    foreach ($platoons as $platoon) {
                      $platoonId = $platoon->getId(); ?>
                      <option value="<?php echo $platoonId; ?>" <?php
                      if ($platoonId == $player->getPlatoonId()) {
                        echo "selected";
                      } elseif (!$user->isAdmin()) {
                        echo "disabled";
                      }
                      ?> ><?php echo $platoon->getName(); ?></option><?php
                    } ?>
                    </select>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password"></td>
                </tr>
				<tr>
                    <td>Active</td>
                    <td><input type="checkbox" name="active" <?php echo ($player->getPlayerActive()==1) ? ('checked="1"') : (""); ?>></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Modify Player"></td>
                </tr>
            </table>
        </form>
    <?php
    } else {
        $players = $playerController->getAllPlayers();
        ?>
        <table width="50%"  border="0" cellspacing="1" cellpadding="1">
        <?php
        foreach ($players as $player) { ?>
                <tr>
                    <td><?php if ($user->isAdmin() || $user->getId() == $player->getId()) { ?>
                      <a href="index.php?url=modify_player.php&player=<?php echo $player->getId(); ?>"><?php
                    }
                    echo stripslashes($player->getNameWithNickname());
                    if ($user->isAdmin() || $user->getId() == $player->getId()) { ?>
                      </a><?php
                    }
                    ?></td>
                </tr>
        <?php }
    }
    ?>
    </table>
<?php
} else {
    include("not_allowed.php");
}
?>
