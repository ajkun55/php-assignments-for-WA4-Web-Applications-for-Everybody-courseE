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
  
  $stmt = $pdo->prepare("SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id
    WHERE profile_id = :prof ORDER BY rank");
    $stmt->execute(array(":prof" => $_GET['profile_id']));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
 /* //$institution = $pdo->query("SELECT * FROM institution WHERE institution_id=" . $_GET['profile_id']);
 // $rowedu = $institution->fetchAll(PDO::FETCH_ASSOC);$educations = $pdo->query("SELECT * FROM education, institution 
                WHERE education.profile_id=" . $_GET['profile_id']+"AND education.institution_id = institution.institution_id");
                $rowedu = $educations->fetchAll(PDO::FETCH_ASSOC); */


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
echo "<p>Educations:<br>";
if (count($educations) > 0) {
    echo "<ul>";
    foreach ($educations as $education) {
        echo "<li>" . htmlentities($education['year']) . " - " . htmlentities($education['name']);
    }
    echo "</ul>";
} else {
    echo "<p>No educations</p>";
}
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
echo '<a href="edit.php?profile_id='.$_GET['profile_id'].'">Edit</a> ';
?>

            <a href="./add.php">Add New Entry</a>
            <a href="./logout.php">Logout</a>

        </div>
    </body>
</html>