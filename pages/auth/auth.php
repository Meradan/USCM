<?php
    session_start();
    include("../../functions.php");
    if (login(1)) {
      header("Location: {$url_root}/index.php");
    } else {
      header("Location: {$url_root}/index.php");
    }
?>
