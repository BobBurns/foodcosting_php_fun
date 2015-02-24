<?php //foodclass.php
  require_once 'login.php';

  $recipe_to_cost = '';
  $connect_data = array($db_hostname, $db_username, $db_password, $db_database);

  if (isset($_POST['rtc'])) $recipe_to_cost = sanitizeString($_POST['rtc']);

  if ($recipe_to_cost === '')
  {
   /* create a recipe object 
    * and output cost per portion */
    $recipe_to_cost = 'Pumpkin Spice Pancake';
  }
  $new_recipe = new RecipeCost($recipe_to_cost, $connect_data);
  $out = sprintf("<br>%s\nrecipe id: %d<br>portion size: %.2f %s<br>",$new_recipe->name,$new_recipe->id, $new_recipe->portion_size, $new_recipe->unit);
  # print_r($out);
  $out2 = $new_recipe->getCost();

echo <<<_END
<html>
  <head>
    <title>Recipe Cost</title>
  </head>
  <body>
    <pre>Enter the name of the recipe you want to cost
    <form method="post" action="foodclass.php">
Name: <input type="text" name="rtc"><input type="submit" value="Cost">
    </form>
    <b>$out<br>$out2</b>
    </pre>
  </body>
</html>
_END;

  function sanitizeString($var)
  {
    $var = stripslashes($var);
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
  }

  class RecipeCost
  {
    public $id, $name, $portion_size, $unit, $yield;
    private $connection;

    function __construct($in_name, $connect_data)
    {
      $this->connection =  new mysqli($connect_data[0], $connect_data[1], $connect_data[2], $connect_data[3]);
      if (!$in_name) die ('would you like to create a name?');

      $query = "SELECT * FROM recipe_yield WHERE name='$in_name'";

      if (!($result = $this->connection->query($query))) {
        $result->close();
        die('no recipe by that name');
      }
      $result->data_seek(0);  //only one row should return
      $record = $result->fetch_array(MYSQLI_ASSOC);

      if (!($this->id = $record['id'])) printf("Sorry, no record by that name!<br>");
      $this->portion_size = $record['portion_size'];
      $this->unit = $record['portion_measure'];
      $this->name = $in_name;
      $this->yield = $record['yield'];
 
      $result->close();
    }
  
    function __destruct()
    {
     $this->connection->close(); 
    }

    function getCost()
    {
      $i_cost = 0;
      // first get ingredients amounts, and measures from recipe
      $query = "SELECT * FROM recipe_table WHERE id=$this->id";
      if (!($result = $this->connection->query($query))) die("Error fetching recipe");
      $rows = $result->num_rows;
 
      for ($i = 0; $i < $rows; ++$i)
      {
        $result->data_seek($i);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $ing[] = $row['ingredient'];
        $amt[] = $row['amount'];
        $unit[] = $row['measure'];
      }
      print_r($ing);
      print_r($amt);
      print_r($unit);
      $result->close();
      // then get costs from ingredient table

      for ($i = 0; $i < count($ing); ++$i)
      {
        $ingr = $ing[$i];
        $query = "SELECT * FROM ingredient_table WHERE name='$ingr'";
        $result = $this->connection->query($query);
        if (!$result) die($this->connection->error);
        $result->data_seek(0);
        $record = $result->fetch_array(MYSQLI_ASSOC);
        $i_cost += (($record['price'] / $record['case_size'] / $record['pack_size']) * $amt[$i]);
        printf("%.2f\n", $i_cost);
      }
      printf("%f\n", $i_cost);
      return $i_cost;
    }

  }

?>
