<!DOCTYPE html>
<html>
  <head>
  <title>Login</title><link rel='stylesheet' href='styles.css' type='text/css'>
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
  <div class="signupbox">
  <form action="adduser.php" action="post" onsubmit="return validate(this)"><pre>
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
