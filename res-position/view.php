<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( !isset($_SESSION['name'])) {
    die('Not logged in');
  } 

  $profile = $pdo->query("SELECT * FROM profile WHERE profile_id=" . $_GET['profile_id']);
  $row = $profile->fetchAll(PDO::FETCH_ASSOC);
  $positions = $pdo->query("SELECT * FROM position WHERE profile_id=" . $_GET['profile_id']);
  $rows = $positions->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "head.php"; ?>
        <title>John A 00063ece's Profile</title>
    </head>
    <body>
        <div class="container">
            <h1>Tracking Profile for <?= htmlentities($_SESSION['name']); ?></h1>
            <?php flashMessages(); ?>

<?php
echo("<p>First name: " . htmlentities($row[0]['first_name']) . "</p>");
echo("<p>Last name: " . htmlentities($row[0]['last_name']) . "</p>");
echo("<p>Email: " . htmlentities($row[0]['email']) . "</p>");
echo("<p>Headline:<br>" . htmlentities($row[0]['headline']) . "</p>");
echo("<p>Summary:<br>" . htmlentities($row[0]['summary']) . "</p>");
echo "<p>Positions:<br>";
if (count($rows) > 0) {
    echo "<ul>";
    foreach ($rows as $row) {
        echo "<li>" . htmlentities($row['year']) . " - " . htmlentities($row['description']);
    }
    echo "</ul>";
} else {
    echo "<p>No positions</p>";
}
echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ';
?>

            <a href="./add.php">Add New Entry</a>
            <a href="./logout.php">Logout</a>

        </div>
    </body>
</html>