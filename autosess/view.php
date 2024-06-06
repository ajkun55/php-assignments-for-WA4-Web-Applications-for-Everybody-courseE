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


$autos = [];
$all_autos = $pdo->query("SELECT * FROM autos");

while ( $row = $all_autos->fetch(PDO::FETCH_OBJ) ){
    $autos[] = $row;
}

if ( isset($_POST['delete']) && isset($_POST['auto_id']) ) {
    $sql = "DELETE FROM autos WHERE auto_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['auto_id']));
    header('Location: view.php');
}

$stmt = $pdo->query("SELECT make, year, mileage, auto_id FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            <?php if(!empty($autos)) : ?>
                <h2>Automobiles</h2>
                <ul>
                    <?php foreach($autos as $auto) : ?>
                        <li>
                            <?php echo $auto->make; ?> <?php echo $auto->year; ?> <?php echo $auto->mileage; ?> 
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <table >
<?php
echo "<th>make</th><th>year</th><th>mileage</th>";
foreach ( $rows as $row ) {
    
    echo "<tr><td>";
    echo $row['make'];
    echo "</td><td>";
    echo $row['year'];
    echo "</td><td>";
    echo $row['mileage'];
    echo "</td><td>";
    echo '<form method="post"><input type="hidden" ';
    echo 'name="auto_id" value="'.$row['auto_id'].'">'."\n";
    echo '<input type="submit" value="Del" name="delete">';
    echo "\n</form>\n";
    echo "</td></tr>\n";
}
?>
</table>

            <a href="./add.php">Add New</a>
            <a href="./logout.php">Logout</a>

        </div>
    </body>
</html>