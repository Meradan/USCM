<?php
require_once 'functions.php';
$newsController = new NewsController();
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$userController = new UserController();
$user = $userController->getCurrentUser();

if (isset($_GET['action'])!=true) {
//         myconnect();
//         mysql_select_db("skynet");
//         $admin = ($_SESSION['level'] >= 3) ? (TRUE) : (FALSE);
//         $gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
        ?>

        <div class="title">Brixton's Fightin' Fives - US Colonial Marine Corps 5th Platoon</div>
        <div align="justify">Website for a roleplaying campaign based on the Alien movies. The players are members of the 5th platoon "Brixton's Fightin' Fives" in the 4th US Colonial Marine brigade.<br/>
        </div>

        <br/><center><img src="images/line.jpg" width="449" heigth="1"></center><br/>

        <div class="title">News</div>
        <?php
//         $lastyears = date("Y") - 1 . date("-m-d");
//         $sql = "SELECT date,written_by,text FROM {$_SESSION['table_prefix']}news WHERE date > '$lastyears' ORDER BY date DESC, id DESC";
//         $res = mysql_query($sql);
//         while ($news = mysql_fetch_array($res)) {
          $listOfNews = $newsController->getLastYearsNews();
          foreach ($listOfNews as $news) {
            ?>
            <div> <font class="colorfont"><?php echo $news->getDate(); ?></font> <?php echo $news->getWrittenBy(); ?></div>
            <div><?php echo $news->getText(); ?></div><br/>
        <?php } ?>
        <br/>
        <div align="center"><a href="index.php?url=news_old.php">Old News</a></div><br/>

        <?php if ($user->isAdmin() || $user->isGm()) {
            ?>
            <form action="news.php?action=post" method="post">
                <table width="50%"  border="0" cellspacing="1" cellpadding="1">
                    <tr>
                        <td>Datum:</td>
                        <td><input name="date" type="text"></td>
                    </tr>
                    <tr>
                        <td>Skrivet av</td>
                        <td><input name="written_by" type="text"></td>
                    </tr>
                    <tr>
                        <td>Text (htmlkod)</td>
                        <td><textarea name="text" cols="70" rows="7"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit"></td>
                    </tr>
                </table>
            </form>

            <?php
        }
} elseif ($_GET['action'] == "post") {
    if ($user->isAdmin() || $user->isGm()) {
      $news = new News();
      $news->setDate($_POST['date']);
      $news->setWrittenBy($_POST['written_by']);
      $news->setText($_POST['text']);
      $newsController->save($news);
    }
    header("location:{$url_root}/index.php?url=news.php");
}
?>
