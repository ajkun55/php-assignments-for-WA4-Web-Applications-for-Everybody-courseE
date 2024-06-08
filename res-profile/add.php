<?php
require_once "pdo.php";
session_start();

if ( !isset($_SESSION['name'])) {
    die('ACCESS DENIED');
  } else {
    $name = $_SESSION['name'];
  }

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
      || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    } 
    $email = htmlentities($_POST['email']);
    if( strpos($email, '@') === false) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: add.php");
        return;
    }

      

    $sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
              VALUES (:uid, :first_name, :last_name, :email, :headline, :summary)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],));
    $_SESSION['success'] = 'Record Added';
    header( 'Location: index.php' ) ;
    return;
}


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>John A 00063ece's Profile Add</title>
    </head>
    <body>
        <div class="container">
            <h1>Adding Profile for<?php echo $name; ?></h1>
        <?php
            // Flash pattern
        if ( isset($_SESSION['error']) ) {
             echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }?>
<p>Add A New Auto</p>
<form method="post">
<p>First Name:
<input type="text" name="first_name"></p>
<p>Last Name:
<input type="text" name="last_name"></p>
<p>Email:
<input type="text" name="email"></p>
<p>Headline:
<input type="text" name="headline"></p>
<p>Summary:
<textarea type="text" name="summary"></textarea></p>
<p><input type="submit" value="Add New"/>
<a href="index.php">Cancel</a></p>
</form>
</div>
</body>
</html>
