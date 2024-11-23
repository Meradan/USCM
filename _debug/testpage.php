<?
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

<?
echo "<pre>";
echo quote_smart("Jane's World's");
echo "</pre>";
?>
</body>
</html>

