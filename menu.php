<header class="header">
  <div>
    <img src="assets/logo/uscm-blip-logo.svg" height="129" alt="USCM Logotype">
  </div>

  <nav class="nav">
    <ul>
      <li>
        <a href="index.php?url=news.php">News</a>
      </li>
      <li>
        <a href="index.php?url=uscm_rpg.php">About</a>
      </li>
      <li>
        <a href="index.php?url=list_characters.php">Characters</a>
      </li>
      <li>
        <a href="index.php?url=list_missions.php">Missions</a>
      </li>
      <li>
        <a href="index.php?url=list_hall_of_fame.php">Hall of Fame</a>
      </li>
      <li>
        <a href="https://uscm.swedishforum.net/" target="_blank">Forum</a>
      </li>
      <li>
        <a href="https://discord.gg/nEp7kwd4h7" target="_blank">Discord</a>
      </li>
      <?php if ($_SESSION['level']>=2): ?>
        <li>
          <a href="index.php?url=create_character.php">Create character</a>
        </li>
        <li>
          <a href="index.php?url=create_mission.php">Create mission</a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']>=3): ?>
        <li>
          <a href="index.php?url=create_player.php">Create player</a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']==3): ?>
        <li>
          <a href="index.php?url=modify_player.php">Modify player</a>
        </li>
      <?php elseif ($_SESSION['level']>=1): ?>
        <li>
          <a href="index.php?url=modify_player.php&player=<?php echo $_SESSION['user_id']; ?>">Modify player</a>
        </li>
      <?php endif ?>
      <li>
        <?php if ($_SESSION['inloggad']==1): ?>
          <a href="login.php?alt=logout">Log Out</a>
        <?php else: ?>
          <a href="index.php?url=login2.php&alt=login">Log In</a>
        <?php endif ?>
      </li>
    </ul>
  </nav>
</header>
