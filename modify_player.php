<?php
$admin = ($_SESSION['level'] == 3) ? (TRUE) : (FALSE);
if ($admin || $_SESSION['user_id'] == $_GET['player']) {
    myconnect();
    mysql_select_db("skynet");

    if (isset($_GET['player'])) {
        $playersql = "SELECT id,forname,nickname,lastname,emailadress,password,use_nickname,platoon_id FROM Users WHERE id='{$_GET['player']}'";
        $playerres = mysql_query($playersql);
        $player = mysql_fetch_array($playerres);
        ?>
        <form method="post" action="player.php?what=modify">
            <table width="50%"  border="0" cellspacing="1" cellpadding="1">
                <input type="hidden" name="id" value="<?php echo $player['id']; ?>">
                <input type="hidden" name="res" value="<?php echo $player['password']; ?>">
                <tr>
                    <td>Forname</td>
                    <td><input type="text" name="forname" value="<?php echo stripslashes($player['forname']); ?>"></td>
                </tr>
                <tr>
                    <td>Nickname</td>
                    <td><input type="text" name="nickname" value="<?php echo stripslashes($player['nickname']); ?>"></td>
                </tr>
                <tr>
                    <td>Use nickname instead of real name</td>
                    <td><input type="radio" name="use_nickname" value="1" <?php echo ($player['use_nickname'] == "1") ? ("checked") : (""); ?> >Yes 
                        <input type="radio" name="use_nickname" value="0" <?php echo ($player['use_nickname'] == "0") ? ("checked") : (""); ?> >No 
                    </td>
                </tr>
                <tr>
                    <td>Lastname</td>
                    <td><input type="text" name="lastname" value="<?php echo stripslashes($player['lastname']); ?>"></td>
                </tr>
                <tr>
                    <td>emailadress</td>
                    <td><input type="text" name="emailadress" value="<?php echo stripslashes($player['emailadress']); ?>"></td>
                </tr>
                <tr>
                    <td>Platoon</td>
                    <td><input type="text" name="platoon_id" value="<?php echo $player['platoon_id']; ?>" <?php echo ($admin) ? ("") : ("readonly"); ?> ></td>
                </tr>
                <tr>
                    <td>password</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Modify Player"></td>
                </tr>
            </table>
        </form>
    <?php
    } else {
        $playersql = "SELECT id,forname,nickname,lastname,emailadress FROM Users ORDER BY lastname,forname";
        $playerres = mysql_query($playersql);
        ?>
        <table width="50%"  border="0" cellspacing="1" cellpadding="1"> 
        <?php while ($player = mysql_fetch_array($playerres)) { ?>
                <tr>
                    <td><?php echo stripslashes($player['forname']) . " '" . stripslashes($player['nickname']) . "' " . stripslashes($player['lastname']); ?></td>
                    <td><a href="index.php?url=modify_player.php&player=<?php echo $player['id']; ?>">Edit</a></td>
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
