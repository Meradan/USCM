<h1 class="heading heading-h1">Log in</h1>

<form class="form" action="login.php?alt=login" method="post">
  <label for="anvandarnamn">
    Username
    <input type="text" id="anvandarnamn" name="anvandarnamn" autocomplete="username">
  </label>

  <label for="losenord">
    Password
    <input type="password" id="losenord" name="losenord" autocomplete="current-password">
  </label>

  <label for="rpg">
    Game
    <select id="rpg" name="rpg">
        <?php
        $db = getDatabaseConnection();
        $rpgsql="select rpg_name_short,table_prefix from RPG order by rpg_name";

        $stmt = $db->query($rpgsql);

         while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           echo "<option value='{$row['table_prefix']}'>{$row['rpg_name_short']}</option>";
             }

      ?>
    </select>
  </label>

  <input class="button" type="submit" value="Log in">

  <?php if(isset($_GET['error'])){?>
    <div class="colorfont">
      <?php echo $_GET['error'];?>
    </div>
  <?php } ?>
</form>
