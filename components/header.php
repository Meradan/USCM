<header class="header">
  <div class="logo">
    <div class="image">
      <?php echo file_get_contents("{$_SERVER['DOCUMENT_ROOT']}{$url_root}/assets/logo/uscm-blip-logo-animate.svg") ?>
    </div>
    <div class="p-10 center">
      Roleplaying game partially based on the Alien movies. The players are members of one of the platoons in the 4th US Colonial Marine brigade.
    </div>
  </div>

  <nav class="nav">
    <?php
      $urlParam = $_GET["url"] ?? "";
    ?>
    <ul>
      <li>
        <a href="index.php?url=news/news.php" <?php echo !$urlParam || $urlParam == "news/news.php" ? "aria-current='page'" : ""?>>
          News
        </a>
      </li>
      <li>
        <a href="index.php?url=about/about.php" <?php echo $urlParam == "about/about.php" ? "aria-current='page'" : ""?>>
          About
        </a>
      </li>
      <li>
        <a href="index.php?url=characters/list.php" <?php echo $urlParam == "characters/list.php" ? "aria-current='page'" : ""?>>
          Characters
        </a>
      </li>
      <li>
        <a href="index.php?url=missions/list.php" <?php echo $urlParam == "missions/list.php" ? "aria-current='page'" : ""?>>
          Missions
        </a>
      </li>
      <li>
        <a href="index.php?url=fame/list.php" <?php echo $urlParam == "fame/list.php" ? "aria-current='page'" : ""?>>
          Hall of Fame
        </a>
      </li>
      <li>
        <a href="https://discord.gg/nEp7kwd4h7" target="_blank">Discord</a>
      </li>
      <?php if ($_SESSION['level']>=2): ?>
        <li>
          <a href="index.php?url=characters/create.php" <?php echo $urlParam == "characters/create.php" ? "aria-current='page'" : ""?>>
            Create character
          </a>
        </li>
        <li>
          <a href="index.php?url=missions/create.php" <?php echo $urlParam == "missions/create.php" ? "aria-current='page'" : ""?>>
            Create mission
          </a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']>=3): ?>
        <li>
          <a href="index.php?url=player/create.php" <?php echo $urlParam == "player/create.php" ? "aria-current='page'" : ""?>>
            Create player
          </a>
        </li>
      <?php endif ?>
      <?php if ($_SESSION['level']==3): ?>
        <li>
          <a href="index.php?url=player/edit.php" <?php echo $urlParam == "player/edit.php" ? "aria-current='page'" : ""?>>
            Modify player
          </a>
        </li>
      <?php elseif ($_SESSION['level']>=1): ?>
        <li>
          <a href="index.php?url=player/edit.php&player=<?php echo $_SESSION['user_id']; ?>" <?php echo $urlParam == "player/edit.php&player=" . $_SESSION['user_id'] ? "aria-current='page'" : ""?>>
            Modify player
          </a>
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
