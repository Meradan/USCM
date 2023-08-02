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
  //if(validate(1)){?>
    <html>
    <head>
    <title>Skynet</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Expires" CONTENT="-1">
    <LINK REL="stylesheet" HREF="style.css" TYPE="text/css"></LINK>
    <script type="text/javascript" src="overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
    </head>

    <body <?php
    if (array_key_exists('uppdaterad', $_GET) && $_GET['uppdaterad']==1) {
      echo "onLoad=\"alert('uppgifterna har uppdaterats')\"";
    } ?>
    >
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
      <?php include("menu.php");
        if(isset($_GET['url'])){
          // To make sure the file loaded is in the local file system and not a remote url
          $pages = recursive_dirlist('/var/www/html/skynet/');
          if( in_array("/".$_GET['url'], $pages) ) {
            include($_GET['url']);
          }
        }
        else {
          include('news.php');
        }
        include("footer.php");
      ?>

      <script data-respect-dnt data-no-cookie async src="https://cdn.splitbee.io/sb.js"></script>
    </body>
    </html>
<?php
  /*}
  else{
    header("location:{$url_root}/login2.php");
  }*/
?>
