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
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    } 
    $email = htmlentities($_POST['email']);
    if( strpos($email, '@') === false) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

      

    $sql = "UPDATE Profile SET first_name = :first_name,
            last_name = :last_name, email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! $_SESSION['user_id'] ) {
  $_SESSION['error'] = "Missing user_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}



$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>John A 00063ece's Profile</title>
    </head>
    <body>
        <div class="container">
            <h1>Edit Profile for <?php echo $name; ?></h1>
        <?php
            // Flash pattern
        if ( isset($_SESSION['error']) ) {
             echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }?>
<p>Edit Profile</p>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $fn ?>"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $ln ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= $e ?>"></p>
<p>Headline:
<input type="text" name="headline" value="<?= $h ?>"></p>
<p>Summary:
<textarea type="text" name="summary" ><?= $s ?></textarea></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</form>

</div>
</body>
</html>