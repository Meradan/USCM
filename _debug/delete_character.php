<?php
session_start();
$admin=($_SESSION['level']==3)?(TRUE):(FALSE);

if ($admin) { 
	include("functions.php");
	myconnect(); 
	mysql_select_db("skynet");
	if ($_POST['character'] =="") {?>
<html>
<head>
</head>
<body>
<form action="delete_character.php" method="post">
<input type="text" name="character"><input type="submit">
</form>
</body>
</html>
<?php }
	else { 
		$skillssql="DELETE FROM uscm_skills WHERE character_id='{$_POST['character']}'";
		$attributes="DELETE FROM uscm_attributes WHERE character_id='{$_POST['character']}'";
		$missions="DELETE FROM uscm_missions WHERE character_id='{$_POST['character']}'";
		$ranks="DELETE FROM uscm_ranks WHERE character_id='{$_POST['character']}'";
		$specialty="DELETE FROM uscm_specialty WHERE character_id='{$_POST['character']}'";
		$character="DELETE FROM uscm_characters WHERE id='{$_POST['character']}'";
		mysql_query($skillssql);
		mysql_query($attributes);
		mysql_query($missions);
		mysql_query($ranks);
		mysql_query($specialty);
		mysql_query($character);
	}
}
