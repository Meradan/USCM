<?php
  session_start();
  include("functions.php");
?>
<html>
<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK REL="stylesheet" HREF="assets/style.css" TYPE="text/css"></LINK>
</head>
<body style="margin-top:40px">

<center>
<div style="border: 1px solid #222222; width:300; height:115; overflow:hidden" >
<form action="login.php?alt=login" method="post">
      <table width="100%" border="0">
        <tr>
          <td width="36%" style="width:35%">Username</td>
          <td width="64%" style="width:65%"><input type="text" name="anvandarnamn" style="width:100%"></td>
        </tr>
        <tr>
          <td>Password</td>
          <td><input type="password" name="losenord" style="width:100%"></td>
        </tr>
        <tr>
          <td>Game:</td>
          <td><select name="rpg" style="width:100%">
        <?php
        $db = new PDO('mysql:host=localhost;dbname=skynet;charset=utf8', 'skynet', 'Br1xt0n');
        $rpgsql="select rpg_name_short,table_prefix from RPG order by rpg_name";

        $stmt = $db->query($rpgsql);

         while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           echo "<option value='{$row['table_prefix']}'>{$row['rpg_name_short']}</option>";
             }

      ?>
            </select></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><div align="right"><font color="#CC0000">
              <input name="submit" type="submit" value="Logga in">
              </font> </div></td>
        </tr>
        <?php if(isset($_GET['error'])){?>
        <tr>
          <td colspan="2"><font color="#CC0000"><?php echo $_GET['error'];?></font></td>
        </tr>
        <?php } ?>
      </table>
</form>
</div>
</center>
</body>
</html>
