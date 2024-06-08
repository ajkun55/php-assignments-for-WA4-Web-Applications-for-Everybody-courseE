<?php
require_once "pdo.php";
session_start();
?>
<html>
<head>
<title>Resume Registry John A 00063ece</title>
<?php require_once "bootstrap.php"; ?>
</head><body>
    <div class="container">
    <h1>Welcome to the Resume Registry</h1>
    
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
    echo '<a href="logout.php">Log out</a>';}

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
<p>Attempt to go to</p>
<a href="add.php">Add New Entry</a>
<a href="edit.php">Edit</a>
</div>