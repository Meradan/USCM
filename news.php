<?php
if (isset($_GET['action'])!=true) {
        myconnect();
        mysql_select_db("skynet");
        $admin = ($_SESSION['level'] >= 3) ? (TRUE) : (FALSE);
        $gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
        ?>

        <div class="title">Brixton's Fightin' Fives - US Colonial Marine Corps 5th Platoon</div>
        <div align="justify">Website for a roleplaying campaign based on the Alien movies. The players are members of the 5th platoon "Brixton's Fightin' Fives" in the 4th US Colonial Marine brigade.<br/>
        </div>

        <br/><center><img src="images/line.jpg" width="449" heigth="1"></center><br/>

        <div class="title">News</div>
        <?php
        $lastyears = date("Y") - 1 . date("-m-d");
        $sql = "SELECT date,written_by,text FROM {$_SESSION['table_prefix']}news WHERE date > '$lastyears' ORDER BY date DESC, id DESC";
        $res = mysql_query($sql);
        while ($news = mysql_fetch_array($res)) {
            ?>
            <div> <font class="colorfont"><?php echo $news['date']; ?></font> <?php echo $news['written_by']; ?></div>
            <div><?php echo $news['text']; ?></div><br/>
        <?php } ?>
        <br/>
        <div align="center"><a href="index.php?url=news_old.php">Old News</a></div><br/>

        <?php if ($admin || $gm) {
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
    session_start();
    include("functions.php");
    myconnect();
    mysql_select_db("skynet");
    $admin = ($_SESSION['level'] >= 3) ? (TRUE) : (FALSE);
    $gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
    $sql = "INSERT INTO {$_SESSION['table_prefix']}news SET date='{$_POST['date']}',written_by='{$_POST['written_by']}',text='{$_POST['text']}'";
    mysql_query($sql);
    header("location:{$url_root}/index.php?url=news.php");
}
?>
