<?php
require_once('./functions.php');
$newsController = new NewsController();
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$userController = new UserController();
$user = $userController->getCurrentUser();

if (isset($_GET['action'])!=true) { ?>
  <h1 class="heading heading-h1">News</h1>

  <?php
    $listOfNews = $newsController->getLastYearsNews();
    foreach ($listOfNews as $news) { ?>
      <article class="news">
        <header>
          <h2 class="heading"><?php echo $news->getDate(); ?></h2>
          <?php echo $news->getWrittenBy(); ?>
        </header>
        <div>
          <?php echo $news->getText(); ?>
        </div>
      </article>
    <?php } ?>

  <div class="p-10 center"><a href="index.php?url=news/archive.php">News archive</a></div>

  <?php if ($user->isAdmin() || $user->isGm()): ?>
    <h2 class="heading heading-h2">Create news</h2>
    <form class="form" action="news.php?action=post" method="post">
      <label for="date">
        Datum
        <input type="text" id="date" name="date">
      </label>

      <label for="written_by">
        Skrivet av
        <input type="text" id="written_by" name="written_by">
      </label>

      <label for="text">
        Text (htmlkod)<br>
        Note, to put in links within the site do it like this
        <code>
          &lt;a href="index.php?url=missions/details.php&id=224"&gt;Mission 192&lt;/a&gt;
        </code>
        <textarea name="text" rows="7"></textarea>
      </label>

      <input class="button" type="submit" value="Create News">
    </form>

  <?php endif ?>

            <?php
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
