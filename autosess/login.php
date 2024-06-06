<?php // Do not put any HTML above this line

session_start();

if ( isset($_POST['logout'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash= '1a52e17fa899cf40fb04cfc42e6352f1'; // Pw is php123

if ( isset($_POST['email']) && isset($_POST['pass']) ){
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ){
        $_SESSION["error"] = "Email and password are required";
    }else{
        $pass = htmlentities($_POST['pass']);
        $email = htmlentities($_POST['email']);

        if (strpos($email, '@') === false){
            $_SESSION["error"] = "Email must have an at-sign (@)";
        }else{
            $check = hash('md5', $salt.$pass);
            if ( $check == $stored_hash ){
                // Redirect the browser to view.php
                $_SESSION["name"] = $_POST["email"];
                $_SESSION["success"] = "Logged in.";
                header("Location: view.php?name=".urlencode($email));
                return;
            }else{
                $_SESSION["error"] = "Incorrect password";
            }
        }
    }
}
// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title> John A 00063ece's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}

?>
<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the programming language (all lower case) followed by 123. -->
</p>
</div>
</body>