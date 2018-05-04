<?php
session_start();
include("functions.php");

print_r($_POST) . '<br />';
echo $_POST["character"] . '<br />';
echo $_POST["simulation"];
?>