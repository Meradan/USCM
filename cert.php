<?php
session_start();
include("functions.php");
myconnect();
mysql_select_db("skynet");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<form method="post" action="database.php?what=cert">
<table width="50%"  border="0" cellspacing="1" cellpadding="1">
	<tr>
		<td colspan="4"><select name="certname"><?
$certsql="SELECT name, id FROM {$_SESSION['table_prefix']}certificate_names";
$certres=mysql_query($certsql);
while ($cert = mysql_fetch_assoc($certres) ) { ?>
	<option value="<?php echo $cert['id']; ?>"><?php echo $cert['name']?></option>
<?php } ?></select></td>
	</tr>
	<tr>
		<td><select name="req1"><?
$attribsql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
$skillsql="SELECT id, skill_name FROM  {$_SESSION['table_prefix']}skill_names ORDER BY skill_name";
$attribres=mysql_query($attribsql);
$skillres=mysql_query($skillsql);
while ($attrib=mysql_fetch_assoc($attribres)) { ?>
	<option value="<?php echo $attrib['id']; ?>"><?php echo $attrib['attribute_name']?></option>
<?php }
while ($skill=mysql_fetch_assoc($skillres)) { ?>
	<option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']?></option>
<?php } ?></select></td>
		<td><input name="value1" type="text"></td>
		<td><select name="from1"><option value="skill_names">skill_names</option>
	<option value="attribute_names">attribute_names</option></select></td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
		<td><select name="req2"><?
$attribsql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
$skillsql="SELECT id, skill_name FROM  {$_SESSION['table_prefix']}skill_names ORDER BY skill_name";
$attribres=mysql_query($attribsql);
$skillres=mysql_query($skillsql);
while ($attrib=mysql_fetch_assoc($attribres)) { ?>
	<option value="<?php echo $attrib['id']; ?>"><?php echo $attrib['attribute_name']?></option>
<?php }
while ($skill=mysql_fetch_assoc($skillres)) { ?>
	<option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']?></option>
<?php } ?></select></td>
		<td><input name="value2" type="text"></td>
		<td><select name="from2"><option value="skill_names">skill_names</option>
	<option value="attribute_names">attribute_names</option></select></td>	
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><select name="req3"><?
$attribsql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
$skillsql="SELECT id, skill_name FROM  {$_SESSION['table_prefix']}skill_names ORDER BY skill_name";
$attribres=mysql_query($attribsql);
$skillres=mysql_query($skillsql);
while ($attrib=mysql_fetch_assoc($attribres)) { ?>
	<option value="<?php echo $attrib['id']; ?>"><?php echo $attrib['attribute_name']?></option>
<?php }
while ($skill=mysql_fetch_assoc($skillres)) { ?>
	<option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']?></option>
<?php } ?></select></td>
		<td><input name="value3" type="text"></td>
		<td><select name="from3"><option value="skill_names">skill_names</option>
	<option value="attribute_names">attribute_names</option></select></td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
		<td><select name="req4"><?
$attribsql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
$skillsql="SELECT id, skill_name FROM  {$_SESSION['table_prefix']}skill_names ORDER BY skill_name";
$attribres=mysql_query($attribsql);
$skillres=mysql_query($skillsql);
while ($attrib=mysql_fetch_assoc($attribres)) { ?>
	<option value="<?php echo $attrib['id']; ?>"><?php echo $attrib['attribute_name']?></option>
<?php }
while ($skill=mysql_fetch_assoc($skillres)) { ?>
	<option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']?></option>
<?php } ?></select></td>
		<td><input name="value4" type="text"></td>
		<td><select name="from4"><option value="skill_names">skill_names</option>
	<option value="attribute_names">attribute_names</option></select></td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
		<td><select name="req5"><?
$attribsql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
$skillsql="SELECT id, skill_name FROM  {$_SESSION['table_prefix']}skill_names ORDER BY skill_name";
$attribres=mysql_query($attribsql);
$skillres=mysql_query($skillsql);
while ($attrib=mysql_fetch_assoc($attribres)) { ?>
	<option value="<?php echo $attrib['id']; ?>"><?php echo $attrib['attribute_name']?></option>
<?php }
while ($skill=mysql_fetch_assoc($skillres)) { ?>
	<option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']?></option>
<?php } ?></select></td>
		<td><input name="value5" type="text"></td>
		<td><select name="from5"><option value="skill_names">skill_names</option>
	<option value="attribute_names">attribute_names</option></select></td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit"></td>
	</tr>
</table>
</form>

</body>
</html>
