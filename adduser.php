<?php // add user

  $firstname = $lastname = $username = $password = $email = "";

  if (isset($_POST['firstname']))
    $firstname = fix_string($_POST['firstname']);
  if (isset($_POST['lastname'])) 
    $lastname = fix_string($_POST['lastname']);
  if (isset($_POST['username'])) 
    $username = fix_string($_POST['username']);
  if (isset($_POST['password'])) 
    $password = fix_string($_POST['password']);
  if (isset($_POST['email'])) 
    $email = fix_string($_POST['email']);

  $fail = validate_firstname($firstname);
  $fail .= validate_lastname($lastname);
  $fail .= validate_username($username);
  $fail .= validate_password($password);
  $fail .= validate_email($email);

  echo "<!DOCTYPE html>\n<html><head><title>Login</title><link rel='stylesheet' href='styles.css' type='text/css'>";

  if ($fail == "")
  {
    echo "</head><body>$username signup successful!";
    // write database code

  exit;
  }

  echo <<<_END

  <script>
    function checkName()
    {
      var n = document.getElementById("name_text").value
      if (n == "") {
        document.getElementById("name_val").innerHTML = "No First Name Entered!"
      } else {
        document.getElementById("name_val").innerHTML = ""
      }
    }
    function checkPass()
    {
      var p = document.getElementById("pass_text").value
      if (validatePassword(p) == true) {
        document.getElementById("pass_val").style.color = "green"
        document.getElementById("pass_val").innerHTML = "Password strength: Strong"
      } else {
        document.getElementById("pass_val").style.color = "red"
        document.getElementById("pass_val").innerHTML = "Password strength: Weak"
      }
    }

    function validate(form)
    {
      fail  = validateFirstname(form.firstname.value)
      fail += validateLastname(form.lastname.value)
      fail += validateUsername(form.username.value)
      fail += validateEmail(form.email.value)

      if (fail == "") return true
      else { alert('Some fields were entered incorrectly, dumbass.'); return false }
    }
    function validateFirstname(field)
    {
      return (field == "") ? "No First name entered.\n" : ""
    }

    function validateLastname(field)
    {
      return (field == "") ? "No Last name entered.\n" : ""
    }

    function validateUsername(field)
    {
      if (field == "") return "No Username was entered.\n"
      else if (field.length < 5)
        return "Username must be at least 5 characters.\n"
      else if (/[^a-zA-Z0-9_-]/.test(field))
        return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"
      return ""
    }

    function validatePassword(field)
    {
      if ((!/[a-z]/.test(field) || !/[A-Z]/.test(field) || !/[0-9]/.test(field)))
        return false
      else return true
    }

    function validateEmail(field)
    {
      if (field == "") return "No Email was entered.\n"
      else if (!((field.indexOf(".") > 0) && (field.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(field))
          return "The Email address is invalid.\n"
      return ""
    }
  </script> 
  </head>
  <body>
  <div id="header">
  <h1>Food Tools</h1>
  </div>

  <b><p style="font-size:20px">Welcome to Food Tools. Please signup to create a database.</p></b>
  <p>$fail Please try again</p>
  <div class="signupbox">
  <form action="index.php" action="post" onsubmit="return validate(this)"><pre>
First Name: <input type="text" id="name_text" maxlength="32" name="firstname">
 Last Name: <input type="text" maxlength="32" name="lastname">
  Username: <input type="text" maxlength="16" name="username">
  Password: <input type="password" id="pass_text" maxlength="12" name="password" oninput="checkPass()"></pre><p id="pass_val" style="text-align:center"></p><pre>
     Email: <input type="text" maxlength="32" name="email">
                  <input type="submit" value="Signup">
  </form></pre>
  </div>
 </body>
</html>

_END;

// PHP functions

  function validate_firstname($field) {
    return ($field == "") ? "No Firstname was entered<br>" : "";
  }

  function validate_lastname($field) {
    return ($field == "") ? "No Lastname was entered<br>" : "";
  }

  function validate_username($field) {
    return ($field == "") ? "No username was entered<br>" : "";
  }

  function validate_password($field) {
    if ($field == "") return "No Password was entered<br>";
    else if (strlen($field) < 0)
      return "Passwords must be at least 6 characters<br>";
    else if (!preg_match("/[a-z]/" , $field) ||
             !preg_match("/[A-Z]/", $field) ||
             !preg_match("/[0-9]/", $field))
      return "Passwords require 1 each of a-z, A-Z, and 0-9<br>";
    return "";
  }

  function validate_email($field) {
    if ($field == "") return "No Email was entered<br>";
    else if (!((strpos($field, ".") > 0) &&
               (strpos($field, "@") > 0)) ||
                preg_match("/[^a-zA-Z0-9_-]/", $field))
      return "The Email address is invalid";
    return "";
  }

  function fix_string($string) {
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return htmlentities($string);
  }
?>
