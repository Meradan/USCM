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
  include("components/security-headers.php");
?>
  <!DOCTYPE html>
  <html lang="sv">
    <head>
      <?php
      include("components/meta.php");
      ?>
    </head>

    <body>

    <?php
      $urlParam = $_GET["url"] ?? "";
    ?>
    <div class="galaxy <?php echo str_contains($urlParam, "about") ? "animate" : "" ?>"></div>

    <div class="wrapper">
      <?php
        include("components/header.php");
      ?>

      <main class="main">
        <?php
        if(isset($_GET['url'])){
          // To make sure the file loaded is in the local file system and not a remote url
          $pages = recursive_dirlist('./pages');
          if(in_array('/'.$_GET['url'], $pages)) {
            include('pages/'.$_GET['url']);
          }
        }
        else {
          include('pages/news/news.php');
        }
        ?>
      </main>

      <?php
        include("components/footer.php");
      ?>
    </div>
    </body>
  </html>
