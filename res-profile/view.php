<?php

session_start();

if ( !isset($_SESSION['name'])) {
    die('Not logged in');
  } else {
    $name = $_SESSION['name'];
  }
  require_once "pdo.php";

try{
    $pdo = new PDO("mysql:host=localhost;dbname=misc", 'fred', 'zap');
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
    die();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>John A 00063ece's Profile</title>
    </head>
    <body>
        <div class="container">
            <h1>Tracking Profile for <?php echo $name; ?></h1>
            <?php
            if ( isset($_SESSION['success']) ) {
                echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }
            ?>


            <table >
<?php
echo('<table border="1">'."\n");
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
echo "<tr><th>Name</th><th>Headline</th><th>Action</th><tr>";
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {    
    echo "<tr><td>";
    echo ('<a href="view.php?profile_id='.$row['profile_id'].'">');
    echo (htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
    echo ('</a></td><td>');
    echo(htmlentities($row['headline']));
    echo ("</td><td>");
    echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> 
        <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a></td></tr>';

    }
?>
</table>

            <a href="./add.php">Add New Entry</a>
            <a href="./logout.php">Logout</a>

        </div>
    </body>
</html>