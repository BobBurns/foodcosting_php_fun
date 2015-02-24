<?php // sqltest.php
  session_start();
  if (isset($_SESSION['sr_num']))
    $_SESSION['sr_num'] = NULL;
  echo <<<_END
  <head>
  <title>Recipe Index</title><link rel='stylesheet' href='styles.css' type='text/css'>
  </head>
  <body>
  <div id="header">
  <h1>Food Tools</h1>
  </div>

  <div id="nav">
  <ul> 
    <li><a href="index.php">Home</a></li>
    <li><a href="ingredient_update.php">Ingredient</a></li>
    <li><a href="recipe_index.php">Recipe</a></li>
    <li><a href="index.php">Menu</a></li>
  </ul>
  </div>
  <h1 style="text-align:center">Recipe List</h1>
  <div id="anew"><a href="recipe_new.php">Add New</a></div>
_END;

  require_once 'login.php';
  $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

  $query = "SELECT * FROM recipe_yield";
  $result = $connection->query($query);

  if (!$result) die ("Database access failed: " . $connection->error);
  $rows = $result->num_rows;
  echo <<<_END
  <div id="data">
  <p>
  <table>
    <tr>
      <th>Recipe No.</th>
      <th>Name</th>
      <th>Description</th>
      <th></th>
      <th></th>
    </tr>
_END;

  for ($i = 0; $i < $rows; ++$i)
  {
    $row = $result->fetch_array(MYSQLI_NUM);
    $desc_q = "SELECT method FROM recipe_text WHERE id='$row[0]'";
    $desc_r = $connection->query($desc_q);
    if (!$desc_r) die ("Second Query Failed: ". $connection->error);
    $desc_r->data_seek(0);
    $desc = substr($desc_r->fetch_assoc()['method'], 0, 50);
    echo <<<_END

  <tr>
    <td>$row[0]</td>
    <td>$row[1]</td>
    <td>$desc</td>
    <td style="text-align:center">
    <form action="recipe_new.php" method="post">
    <input type="hidden" name="r_num" value="$row[0]">
    <input type="hidden" name="first" value=true>
    <input type="hidden" name="edit" value=true>
    <input type="submit" value="edit" name="edit"></form>
    </td>
    <td style="text-align:center">
    <form action="recipe_index.php" method="post">
    <input type="hidden" name="id" value="$row[0]">
    <input type="submit" value="view" name="view"></form>
    </td>
  </tr>
_END;
  }
  echo "</table></div>";
  echo "</body>";

  $result->close();
  $desc_r->close();
?> 
