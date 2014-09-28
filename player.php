<?php 
/*
 *	Functions handling players should go in here
 *
 */
session_start();
include("functions.php");
myconnect();
mysql_select_db("skynet");
$forname=quote_smart($_POST['forname']);
$lastname=quote_smart($_POST['lastname']);
$nickname=quote_smart($_POST['nickname']);
$emailadress=quote_smart($_POST['emailadress']);
$password=quote_smart($_POST['password']);
$use_nickname=quote_smart($_POST['use_nickname']);
$platoon_id=quote_smart($_POST['platoon_id']);

if ($_GET['what']=="create") {
	$sql="INSERT INTO Users SET forname='{$forname}',nickname='{$nickname}',lastname='{$lastname}',
	emailadress='{$emailadress}',password=PASSWORD('{$password}'),use_nickname='{$use_nickname}'
	,platoon_id='{$platoon_id}'";
	mysql_query($sql);
}
elseif ($_GET['what']=="modify") {
	if ($_POST['password']) {
		mysql_query("UPDATE Users SET password=PASSWORD('{$password}') where id='{$_POST['id']}' LIMIT 1");
	}		
	$sql="UPDATE Users SET forname='{$forname}',nickname='{$nickname}',lastname='{$lastname}',
	emailadress='{$emailadress}',use_nickname='{$use_nickname}',platoon_id='{$platoon_id}' WHERE id='{$_POST['id']}' LIMIT 1";
	mysql_query($sql); 
} 

header("location:{$url_root}/index.php?url=modify_player.php");

?>
