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

include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/functions.php");
include("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/components/security-headers.php");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Skynet - USCM</title>
  <link rel="icon" href="<?php echo $url_root ?>/assets/logo/uscm-blip-logo@32px.png" sizes="any">
  <link rel="icon" href="<?php echo $url_root ?>/assets/logo/uscm-blip-logo.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="<?php echo $url_root ?>/assets/logo/uscm-blip-logo@180px.png">
  <link rel="manifest" href="<?php echo $url_root ?>/assets/manifest.json">
  <link href="<?php echo $url_root ?>/assets/style.css" rel="stylesheet">
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
