<?php
$_SESSION['cgversion']="Character Generator v.5.00b";
myconnect();
mysql_select_db("skynet");

$query="select id from {$_SESSION['table_prefix']}characters where userid='{$_SESSION['user_id']}'";
$res=mysql_query($query);
if(mysql_num_rows($res)>0){
	$cid=mysql_fetch_array($res);
}

?>
<center><div class="title"><?echo $_SESSION['cgversion'];?></div></center><br/>
<table>
<tr><td WIDTH="607" ALIGN="center" style="text-align:center">
<?php
if (!$_SESSION['level']>=1) {
	echo "Du måste vara inloggad för att kunna skapa en karaktär";
	$_SESSION['cgstatus'] = 0;
} elseif($cid['id']>0) {
	echo "Du har redan en karaktär<br/>";
	if($_SESSION['user_id']==2||$_SESSION['user_id']==1)echo "<a href=\"index.php?url=cg_attr.php\">Skapa ny karaktär</a>";
	//cg_modify.php
	$_SESSION['cgstatus'] = 0;
} else {
	echo "<a href=\"index.php?url=cg_info.php\">Skapa ny karaktär</a>";
	$_SESSION['cgstatus'] = 1;
}

?>
<br /><br /><br />OBS!!! Under utveckling
</td></tr>
</table>
