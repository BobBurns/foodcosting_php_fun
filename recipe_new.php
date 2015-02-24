<?php // sqltest.php
  session_start();
  echo <<<_END
  <head>
  <title>Edit Recipe</title><link rel='stylesheet' href='styles.css' type='text/css'>
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
_END;
  require_once 'login.php';
  $first = false;
  $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
  if ($connection->connect_error) die($connection->connect_error);
  echo '<h1 style="text-align:center">New Recipe</h1>';

// first check post values and update tables accordingly

  if (isset($_POST['r_num']) && (!isset($_SESSION['sr_num']))) {
    $_SESSION['sr_num'] = get_post($connection, 'r_num');

    if (!isset($_POST['edit'])) {
      $query = "INSERT INTO recipe_yield(id) VALUES('" . $_SESSION['sr_num'] . "')";
      if (!($result = $connection->query($query))) echo $connection->error;
      $query = "INSERT INTO recipe_text(id) VALUES('" . $_SESSION['sr_num'] . "')";
      if (!($result = $connection->query($query))) echo $connection->error;
    }
  }
  if ((isset($_POST['add_r']) && (isset($_SESSION['sr_num']))) || (isset($_POST['save']) && (isset($_SESSION['sr_num'])))) {
// recipe_table variables
    $ingredient = get_post($connection, 'ingredient');
    $quantity = get_post($connection, 'qty');
    $i_meas = get_post($connection, 'i_meas');
// recipe_yield variables
    $name = get_post($connection, 'name'); 
    $yield = get_post($connection, 'yield');
    $p_size = get_post($connection, 'p_size');
    $measure = get_post($connection, 'measure');
// recipe_text variable
    $text = get_post($connection, 'text');
    print_r($text);
    $first = true;

    if (isset($_POST['add_r'])) {
      $query = "INSERT INTO recipe_table(id, ingredient, measure, amount) VALUES('" . $_SESSION['sr_num'] . "', '$ingredient', '$i_meas', '$quantity')";
    
      if (!($result = $connection->query($query))) echo $connection->error;
    }

    $query = "UPDATE recipe_yield SET name='$name', yield='$yield', portion_measure='$measure', portion_size='$p_size' WHERE id='" . $_SESSION['sr_num'] . "'";
    if (!($result = $connection->query($query))) echo $connection->error;

    $query = "UPDATE recipe_text SET method='" . $text . "' WHERE id='" . $_SESSION['sr_num'] . "'";
    if (!($result = $connection->query($query))) echo $connection->error;

  }          

  if (isset($_POST['first'])) {
    $query = "SELECT * FROM recipe_yield WHERE id='" . $_SESSION['sr_num'] . "'";
    if (!($result = $connection->query($query))) echo $connection->error;

    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    $name = $row['name'];
    $r_num = $row['id'];
    $yield = $row['yield'];
    $p_size = $row['portion_size'];
    $measure = $row['portion_measure'];
    $first = true;

    $query = "SELECT method FROM recipe_text WHERE id='" . $_SESSION['sr_num'] . "'";
    if (!($result = $connection->query($query))) echo $connection->error;

    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $text = $row['method'];
    printf("text from seek: %s", $text);
  } else {
    $name = $r_num = $yield = $p_size = $measure = $text = '';
    }

  if (isset($_POST['rm_ing'])) {
    $rm = get_post($connection, 'rec_to_rm');
    $query = "DELETE FROM recipe_table WHERE ingredient='$rm' AND id='" . $_SESSION['sr_num'] . "'";
    if (!($result = $connection->query($query))) echo $connection->error;
  }

// main html

  echo '<pre><form id="main" action="recipe_new.php" method="post">';		// use presentation format
  //$ses_num = $_SESSION['sr_num'];
  if (!$_SESSION['sr_num']) {
    echo 'Recipe Number <input type="text" name="r_num" size="7">';
  } else {
    echo "<b>" . $_SESSION['sr_num'] . "</b>";
  }
  echo <<<_END
  <input type="hidden" name="first" value=true>
Name <input type="text" name="name" value="$name"> Yield <input type="text" name="yield" size="5" value="$yield"> Portion Size <input type="text" name="p_size" size="4" value="$p_size">Unit <select name="measure" size="1" value="$measure">
  <option value="ea">Ea</option>
  <option value="lb">Lb</option>
  <option value="oz">oz</option>
  <option value="gal">Gal</option>
  <option value="qt">Qt</option>
  <option value="pnt">Pint</option>
  <option value="cup">Cup</option>
  <option value="tbl">Tbl</option>
  <option value="tsp">tsp</option>
</select></pre>

<hr><pre>
_END;
  if (!$first) echo '<input type="hidden" name="set_name" value=true>';
 
  addRecipe($connection);


// display ingredients
  if (isset($_SESSION['sr_num'])) {
  $query = "SELECT * FROM recipe_table WHERE id='" . $_SESSION['sr_num'] . "'";
  if (!($result = $connection->query($query))) echo $connection->error;
  
  $rows = $result->num_rows;
  echo <<<_END
  <div id="data">
  <table>
    <tr>
      <th>Qty</th>
      <th>Unit</th>
      <th>Ingredient</th>
      <th></th>
    </tr>
_END;

  for ($i = 0; $i < $rows; ++$i) {
    $result->data_seek($i);
    $row = $result->fetch_array(MYSQLI_NUM);
    echo <<<_END
    <tr>
      <td>$row[3]</td>
      <td>$row[2]</td>
      <td>$row[1]</td>
      <td style="text-align:center"><form action="recipe_new.php" method="post">
          <input type="hidden" name="rec_to_rm" value="$row[1]">
          <input type="hidden" name="first" value=true>
          <input type="submit" name="rm_ing" value="DELETE">
          </form></td>
    </tr>
_END;
  }
    echo "</table></div>";
  }
// get method textarea
  echo <<<_END
  <p><label for="m_text">Method</label></p>
  <textarea name="text" id="m_text" rows="15" cols="105" form="main">$text</textarea>
_END;

  function addRecipe($connection)
  {
  $query = "SELECT name FROM ingredient_table";
  $result = $connection->query($query);
  if (!$result) echo "ingredient query failed!" . $connection->error . "<br>";

    echo <<<_END
Qty <input type="text" name="qty" size="4"> Unit <select name="i_meas" size="1">
  <option value="ea">Ea</option>
  <option value="lb">Lb</option>
  <option value="oz">oz</option>
  <option value="gal">Gal</option>
  <option value="qt">Qt</option>
  <option value="pnt">Pint</option>
  <option value="cup">Cup</option>
  <option value="tbl">Tbl</option>
  <option value="tsp">tsp</option>
</select>Ingredient <select name="ingredient" size="1">
_END;
  $rows = $result->num_rows;
  for ($i = 0; $i < $rows; ++$i) {
    $result->data_seek($i);
    $row = $result->fetch_array(MYSQLI_NUM);
  echo "<option value='$row[0]'>$row[0]</option>";
  }
  echo '</select>';
  echo '<input type="submit" name="add_r" value="ADD">';
  echo '<input type="submit" name="save" value="SAVE">';
  echo '</form><br>';
  $result->close();
  } // addRecipe

  echo '</pre>';
  echo '</body>';
  $connection->close();

  function get_post($connection, $var)
  {
    return $connection->real_escape_string($_POST[$var]);
  }
?>
