<?php // sqltest.php
  echo <<<_END
  <head>
  <title>Update Ingredients</title><link rel='stylesheet' href='styles.css' type='text/css'>
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
  $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
  $do_update = false;

  if ($connection->connect_error) die($connection->connect_error);

  if (isset($_POST['delete']) && isset($_POST['name']))
  {
    $name = get_post($connection, 'name');
    $query = "DELETE FROM ingredient_table WHERE name='$name'";
    $result = $connection->query($query);

    if (!$result) 
      echo "DELETE failed: $query<br>" .
      mysql_error() . "<br><br>";
  }

  if (isset($_POST['edit']) && isset($_POST['name'])) {
    $do_update = true;
    $name = get_post($connection, 'name');
    $query = "SELECT * FROM ingredient_table WHERE name='$name'";
    $result = $connection->query($query);
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_NUM);
    for ($i = 0; $i < count($row); $i++) 
      $values[] = $row[$i];
  }
  print_r($values);

  if (isset($_POST['update'])) printf("update pressed\n");
  elseif (isset($_POST['add'])) printf("add pressed\n");
  /* if (isset($_POST['name']) &&
      isset($_POST['desc']) &&
      isset($_POST['c_size']) &&
      isset($_POST['unit']) &&
      isset($_POST['p_size']) &&
      isset($_POST['price']) &&
      isset($_POST['v_name']) &&
      isset($_POST['v_itemid']) &&
      isset($_POST['upc'])) */
  if (isset($_POST['update']) || isset($_POST['add']))
  {
    $name	= get_post($connection, 'name');
    $desc	= get_post($connection, 'desc');
    $c_size	= get_post($connection, 'c_size');
    $unit	= get_post($connection, 'unit');
    $p_size	= get_post($connection, 'p_size');
    $price	= get_post($connection, 'price');
    $v_name	= get_post($connection, 'v_name');
    $v_itemid	= get_post($connection, 'v_itemid');
    $upc	= get_post($connection, 'upc');

    if (isset($_POST['update'])) {
      $query = "UPDATE ingredient_table SET description='" . $desc .
               "', case_size=" . $c_size . 
               ", measure='" . $unit .
               "', pack_size=" . $p_size .
               ", price=" . $price .
               ", vendor_name='" . $v_name .
               "', v_itemid='" . $v_itemid .
               "', upc='" . $upc .
               "' WHERE name='" . $name . "'";
      $result = $connection->query($query);
      if (!$result) echo "UPDATE Failed: $query<br>". $connection->error . "<br><br>";
    } else {
    $query = "INSERT INTO ingredient_table VALUES" .
      "('$name', '$desc', '$c_size', '$unit', '$p_size', '$price', '$v_name', '$v_itemid', '$upc')";

    $result = $connection->query($query);

    if (!$result) echo "INSERT failed: $query<br>" .
      $connection->error . "<br><br>";
  }
  }
  if (!$values)  {
    for ($i = 0; $i < 9; ++$i) {
      $values[$i] = '';
    }
  }

  echo '<h1 style="text-align:center">Ingredients</h1>';
  if ($do_update) echo "<h2>Update " . $name . "</h2>";
  echo'  <form action="ingredient_update.php" method="post"><pre>';
  if (!$do_update)
    echo  
"       Name <input type='text' name='name'><br>";
  echo <<<_END
Description <input type="text" name="desc" value="$values[1]">
  Case Size <input type="number" name="c_size" value="$values[2]">
    Measure <input type="text" name="unit" value="$values[3]">
  Pack Size <input type="number" name="p_size" value="$values[4]">
      Price <input type="number" name="price" value="$values[5]">
     Vendor <input type="text" name="v_name" value="$values[6]">
Vend Item#  <input type="text" name="v_itemid" value="$values[7]">
        UPC <input type="text" name="upc" value="$values[8]">
            <input type="submit" value="Save" name="add">
_END;
  if ($do_update) {
    echo '<input type="hidden" name="update" value=true>';
    echo '<input type="hidden" name="name" value="' . $values[0] . '">';
  }
echo '</pre></form>';

  $query = "SELECT * FROM ingredient_table";
  $result = $connection->query($query);

  if (!$result) die ("Database access failed: " . $connection->error);
  $rows = $result->num_rows;
  echo <<<_END
  <div id="data">
  <table>
    <tr>
      <th>Name</th>
      <th>Case Size</th>
      <th>Measure</th>
      <th>Pack Size</th>
      <th>Price</th>
      <th>Vendor</th>
      <th>Vendor Item#</th>
      <th>UPC</th>
      <th></th>
    </tr>
_END;

  for ($j = 0; $j < $rows ; ++$j)
  {
    $row = $result->fetch_array(MYSQLI_NUM); 
    $special = sprintf("$%.2f", $row[5]);
    echo <<<_END

  <tr>
    <td>$row[0]</td>
    <td>$row[2]</td>
    <td>$row[3]</td>
    <td>$row[4]</td>
    <td>$special</td>
    <td>$row[6]</td>
    <td>$row[7]</td>
    <td>$row[8]</td>
  <td style="text-align:center">
  <form action="ingredient_update.php" method="post">  
  <input type="hidden" name="name" value="$row[0]">
  <input type="submit" value="EDIT RECORD" name="edit"></form>
  </td>
 </tr>
_END;
  }
  echo "</table></div>";
  echo "</body>";

  $result->close();
  $connection->close();

  function get_post($connection, $var)
  {
    return $connection->real_escape_string($_POST[$var]);
  }
?>
