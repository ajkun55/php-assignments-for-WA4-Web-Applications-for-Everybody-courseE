<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( !isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
    return;
} 

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

if ( ! $_REQUEST['profile_id'] ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :prof AND user_id = :uid");
$stmt->execute(array(":prof" => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $profile === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
}

  if ( isset($_POST['first_name']) && isset($_POST['last_name'])
  && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {

    // Data validation
    $msg = validateProfile();
    if( is_string($msg) ){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    $msg = validatePos();
    if( is_string($msg) ){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
             
    $stmt = $pdo->prepare("UPDATE Profile SET first_name = :first_name,
            last_name = :last_name, email = :email, headline = :headline, summary = :summary
            WHERE profile_id = :profile_id AND user_id = :user_id ");
    $stmt->execute(array(        
        ':profile_id' => $_REQUEST['profile_id'],
        ':user_id' => $_SESSION['user_id'],
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary']));

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    // Insert the position entries
    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }
    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
}

$positions = loadPos($pdo, $_REQUEST['profile_id']);
?>
<html>
    <head>
        <?php require_once "head.php"; ?>
        <title>John A 00063ece's Profile</title>
    </head>
    <body>
        <div class="container">
            <h1>Edit Profile for <?= htmlentities($_SESSION['name']); ?></h1>
        <?php flashMessages();  ?>
<p>Edit Profile</p>
<form method="post" action="edit.php">
<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']);  ?>">
<p>First Name:
<input type="text" name="first_name" value="<?= htmlentities($profile['first_name']); ?>"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= htmlentities($profile['last_name']); ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= htmlentities($profile['email']); ?>"></p>
<p>Headline:
<input type="text" name="headline" value="<?= htmlentities($profile['headline']); ?>"></p>
<p>Summary:
<textarea type="text" name="summary" ><?= htmlentities($profile['summary']); ?></textarea></p>

    <?php
        $pos = 0;
        echo('<p>Positions: <input type="submit" id="addPos" value="+">'."\n");
        echo('<div id="position_fields">'."\n");
        foreach ($positions as $position){
            $pos++;
            echo('<div id="position'.$pos.'">'."\n");
            echo('<p>Year: <input type="text" name="year'.$pos.'"');
            echo('value="'.$position['year'].'" />'."\n" );
            echo('<input type="button" value="-" ');
            echo('onclick="$(\'#position'.$pos.'\').remove();return false;" />'."\n");
            echo("</p>\n");
            echo('<textarea name="desc'.$pos.'" rows="8" clos="80">'."\n");
            echo(htmlentities($position['description'])."\n");
            echo("\n</textarea>\n</div>\n");
        }
    ?>
</div>
</p>
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</form>

</div>
<script>
    countPos = <?= $pos ?>;
    //https://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript

    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event){
            event.preventDefault();
            if( countPos >= 9){
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log('Adding position'+countPos);
            $('#position_fields').append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                <input type="button" value="-"   \
                        onclick="$(\'#position'+countPos+'\').remove();return false;" />    \
                </p>  \
                <textarea name="desc'+countPos+'" rows="8" clos="80"></textarea>  \
                </div>'
            );
        })
    })
</script>
</body>
</html>