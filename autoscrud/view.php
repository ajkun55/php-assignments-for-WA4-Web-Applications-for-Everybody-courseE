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
        <title>John A 00063ece's Autos</title>
    </head>
    <body>
        <div class="container">
            <h1>Tracking Autos for <?php echo $name; ?></h1>
            <?php
            if ( isset($_SESSION['success']) ) {
                echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }
            ?>


            <table >
<?php
echo('<table border="1">'."\n");
$stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(htmlentities($row['make']));
    echo("</td><td>");
    echo(htmlentities($row['model']));
    echo("</td><td>");
    echo(htmlentities($row['year']));
    echo("</td><td>");
    echo(htmlentities($row['mileage']));
    echo("</td><td>");
    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
    echo("</td></tr>\n");
}
?>
</table>

            <a href="./add.php">Add New Entry</a>
            <a href="./logout.php">Logout</a>

        </div>
    </body>
</html>