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

  include("functions.php");
  include("security-headers.php");

  //if(validate(1)){?>
  <!DOCTYPE html>
  <html lang="sv">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1.0">
      <title>Skynet - USCM</title>
      <link rel="icon" href="assets/logo/uscm-blip-logo@32px.png" sizes="any">
      <link rel="icon" href="assets/logo/uscm-blip-logo.svg" type="image/svg+xml">
      <link rel="apple-touch-icon" href="assets/logo/uscm-blip-logo@180px.png">
      <link rel="manifest" href="assets/manifest.json">
      <link href="assets/style.css" rel="stylesheet">
    </head>

    <body <?php
    if (array_key_exists('uppdaterad', $_GET) && $_GET['uppdaterad']==1) {
      echo "onLoad=\"alert('uppgifterna har uppdaterats')\"";
    } ?>
    >
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

    <div class="wrapper">
      <?php
        include("menu.php");
      ?>

      <main class="main">
        <?php
        if(isset($_GET['url'])){
          // To make sure the file loaded is in the local file system and not a remote url
          $pages = recursive_dirlist('./');
          if( in_array("/".$_GET['url'], $pages) ) {
            include($_GET['url']);
          }
        }
        else {
          include('news.php');
        }
        ?>
      </main>

      <?php
        include("footer.php");
      ?>
    </div>
    </body>
  </html>
<?php
  /*}
  else{
    header("location:{$url_root}/login2.php");
  }*/
?>
