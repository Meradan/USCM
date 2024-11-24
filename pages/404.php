<?php
session_start();
if (!array_key_exists('level', $_SESSION)) {
  $_SESSION['level'] = 0;
}
if (!array_key_exists('user_id', $_SESSION)) {
  $_SESSION['user_id'] = -1;
}
if (!array_key_exists('inloggad', $_SESSION)) {
  $_SESSION['inloggad'] = -1;
}

if (!array_key_exists('table_prefix', $_SESSION)) {
  $_SESSION['table_prefix']="uscm_";
}

include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/security-headers.php");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <?php
  include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/meta.php");
  ?>
</head>
<body>
<div class="galaxy"></div>

<div class="wrapper">
  <?php
  include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/header.php");
  ?>

  <main class="main">
    <?php
    include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/404.php");
    ?>
  </main>

  <?php
  include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/footer.php");
  ?>
</div>
</body>
</html>
