<?php

// Demand a GET parameter
if ( !isset($_GET['name']) || strlen($_GET['name']) < 1 ) {
    die('Name parameter missing');
}

if ( strpos($_GET['name'], '@') === false ) {
    die('Name parameter is wrong');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

try{
    $pdo = new PDO("mysql:host=localhost;dbname=misc", 'fred', 'zap');
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
    die();
}

$name = htmlentities($_GET['name']);

$status = false;  // If we have no POST data
$status_color = 'red';

// Check to see if we have some POST data, if we do process it
if (isset($_POST['mileage']) && isset($_POST['year']) && isset($_POST['make'])){
    if ( !is_numeric($_POST['mileage']) || !is_numeric($_POST['year']) ){
        $status = "Mileage and year must be numeric";
    }else if (strlen($_POST['make']) < 1){
        $status = "Make is required";
    }else{
        $make = htmlentities($_POST['make']);
        $year = htmlentities($_POST['year']);
        $mileage = htmlentities($_POST['mileage']);

        $stmt = $pdo->prepare("
            INSERT INTO autos (make, year, mileage) 
            VALUES (:make, :year, :mileage)");

        $stmt->execute([
            ':make' => $make, 
            ':year' => $year,
            ':mileage' => $mileage,
        ]);

        $status = 'Record inserted';
        $status_color = 'green';
    }
}

$autos = [];
$all_autos = $pdo->query("SELECT * FROM autos");

while ( $row = $all_autos->fetch(PDO::FETCH_OBJ) ){
    $autos[] = $row;
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
                if ( $status !== false ){
                    // Look closely at the use of single and double quotes
                    echo('<p style="color: ' .$status_color. ';" >'.
                            htmlentities($status).
                        "</p>\n");
                }
            ?>
            <form method="post">
                <p>Make:
                <input type="text" name="make" size="60"/></p>
                <p>Year:
                <input type="text" name="year"/></p>
                <p>Mileage:
                <input type="text" name="mileage"/></p>
                <input type="submit" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form>

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

        </div>
    </body>
</html>