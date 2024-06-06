<?php

session_start();

if ( !isset($_SESSION['name'])) {
    die('Not logged in');
  } else {
    $name = $_SESSION['name'];
  }

  require_once "pdo.php";

  // If the user requested logout go back to index.php
  if ( isset($_POST['logout']) ) {
    session_destroy();
    header('Location: index.php');
    return;
  }
  
  // Cancel entry.
  if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
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


// Check to see if we have some POST data, if we do process it
if (isset($_POST['mileage']) && isset($_POST['year']) && isset($_POST['make'])){
    if ( !is_numeric($_POST['mileage']) || !is_numeric($_POST['year']) ){
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    }else if (strlen($_POST['make']) < 1){
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
        return;
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

        $_SESSION['success'] = "Record inserted";
        header("Location: view.php");
        return;
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
                if (isset($_SESSION['error']) ) {
                    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
                    unset($_SESSION['error']);
                }
                if ( isset($_SESSION["success"]) ) {
                    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
                     unset($_SESSION["success"]);
                }
            ?>

            <form method="POST">
                <p>Make:
                <input type="text" name="make" size="60"/></p>
                <p>Year:
                <input type="text" name="year"/></p>
                <p>Mileage:
                <input type="text" name="mileage"/></p>
                <input type="submit" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form>

           

        </div>
    </body>
</html>