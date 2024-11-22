<header class="header">
  <div class="logo">
    <div class="image">
      <?php echo file_get_contents("assets/logo/uscm-blip-logo-animate.svg") ?>
    </div>
    <div class="p-10 center">
      Roleplaying game partially based on the Alien movies. The players are members of one of the platoons in the 4th US Colonial Marine brigade.
    </div>
  </div>

  <nav class="nav">
    <ul>
      <li>
        <a href="index.php?url=news/news.php">News</a>
      </li>
      <li>
        <a href="index.php?url=about/about.php">About</a>
      </li>
      <li>
        <a href="index.php?url=characters/list.php">Characters</a>
      </li>
      <li>
        <a href="index.php?url=missions/list.php">Missions</a>
      </li>
      <li>
        <a href="index.php?url=fame/list.php">Hall of Fame</a>
      </li>
      <li>
        <a href="https://uscm.swedishforum.net/" target="_blank">Forum</a>
      </li>
      <li>
        <a href="https://discord.gg/nEp7kwd4h7" target="_blank">Discord</a>
      </li>
      <?php if ($_SESSION['level']>=2): ?>
        <li>
          <a href="index.php?url=characters/create.php">Create character</a>
        </li>
        <li>
          <a href="index.php?url=missions/create.php">Create mission</a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']>=3): ?>
        <li>
          <a href="index.php?url=player/create.php">Create player</a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']==3): ?>
        <li>
          <a href="index.php?url=player/edit.php">Modify player</a>
        </li>
      <?php elseif ($_SESSION['level']>=1): ?>
        <li>
          <a href="index.php?url=player/edit.php&player=<?php echo $_SESSION['user_id']; ?>">Modify player</a>
        </li>
      <?php endif ?>
      <li>
        <?php if ($_SESSION['inloggad']==1): ?>
          <a href="pages/auth/auth.php?alt=logout">Log Out</a>
        <?php else: ?>
          <a href="index.php?url=auth/login.php&alt=login">Log In</a>
        <?php endif ?>
      </li>
    </ul>
  </nav>
</header>
