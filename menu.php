<?php /*<div class="colorfont" style="text-align:center">
<p> */ ?>
<TABLE BGCOLOR="#000000" ALIGN="center" WIDTH="627" CELLSPACING="0" CELLPADDING="0" BORDER="0">
<TR><TD HEIGHT="10"></TD></TR><TR><TD>

<TABLE BACKGROUND="images/logo_top_index.jpg" ALIGN="center" WIDTH="627" HEIGHT="129" >
<TR><TD WIDTH="10"></TD><TD WIDTH="607"></TD><TD WIDTH="10"></TD></TR></TABLE>

<TABLE BACKGROUND="images/logo_menu_index.jpg" ALIGN="center" WIDTH="627" HEIGHT="20">
<TR><TD WIDTH="607" ALIGN="center" class="colorfont" style="text-align:center">

<a href="index.php?url=news.php">News</a> |
<a href="index.php?url=uscm_rpg.php">USCM RPG</a> |
<a href="index.php?url=list_characters.php">Characters</a> |
<a href="index.php?url=list_missions.php">Missions</a> |
<a href="index.php?url=list_hall_of_fame.php">Hall of Fame</a> |
<a href="https://uscm.swedishforum.net/" target="_blank">Forum</a> |
<a href="https://discord.gg/nEp7kwd4h7" target="_blank">Discord</a>
<?php echo ($_SESSION['level']>=2)?(' | <a href="index.php?url=create_character.php">Create character</a>'):(""); ?>
<?php echo ($_SESSION['level']>=2)?(' | <a href="index.php?url=create_mission.php">Create mission</a>'):(""); ?>
<?php if ($_SESSION['level']==3) {
	echo ' | <a href="index.php?url=modify_player.php">Modify player</a>';
} elseif ($_SESSION['level']>=1) {
	echo " | <a href=\"index.php?url=modify_player.php&player={$_SESSION['user_id']}\">Modify player</a>";
}	?>
<?php echo ($_SESSION['level']>=3)?(' | <a href="index.php?url=create_player.php">Create player</a>'):(""); ?>
<?php echo ($_SESSION['inloggad']==1)?(' | <a href="login.php?alt=logout">Log Out</a>'):('| <a href="login2.php?alt=login">Log In</a>'); ?>
</TD>
</TR></TABLE>

<BR><CENTER><IMG SRC="images/line.jpg" WIDTH="449" HEIGHT="1"></CENTER><BR>
<div>

<?php /* </p>
</div> */ ?>
