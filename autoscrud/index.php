<?php
require_once "pdo.php";
session_start();
?>
<html>
<head>
<title>Autos Database CRUD John A 00063ece</title>
<?php require_once "bootstrap.php"; ?>
</head><body>
    <div class="container">
    <h1>Welcome to the Automobiles Database</h1>
    
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
if ( !isset($_SESSION['name'])) {
    echo '<a href="login.php">Please log in</a>';
  } else {
    $name = $_SESSION['name'];
    echo '<a href="logout.php">Log out</a>';
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
  }
?>
</table>
<p>Attempt to go to</p>
<a href="add.php">Add New Entry</a>
<a href="edit.php">Edit</a>
</div>