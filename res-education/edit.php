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

    $msg = validateEdu();
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
    insertPositions($pdo, $_REQUEST['profile_id']);

    // Clear out the old education entries
    $stmt = $pdo->prepare('DELETE FROM Education
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    // Insert the education entries
    insertEducations($pdo, $_REQUEST['profile_id']);
    
    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
}
//load up the rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);

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
        $countEdu = 0;
        echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
        echo('<div id="edu_fields">'."\n");
        if ( count($schools)>0 ){
            foreach ($schools as $school){
                $countEdu++;
                 echo('<div id="edu'.$countEdu.'">');
                echo
            '<p>Year: <input type="text" name="edu_year'.$countEdu.'"value="'.$school['year'].'" />
            <input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>
            <p>School: <input type="text" name="edu_school'.$countEdu.'" size="80" class="school" 
                    value="'.htmlentities($school['name']).'" />';
                echo "\n</div>\n";
            }
        } echo("</div></p>\n");

        $countPos = 0;
        echo('<p>Positions: <input type="submit" id="addPos" value="+">'."\n");
        echo('<div id="position_fields">'."\n");
        if ( count($positions) > 0 ){
            foreach ($positions as $position){
                $countPos++;
                echo('<div id="position'.$countPos.'">');            
                echo
            '<p>Year: <input type="text" name="year'.$countPos.'" value="'.$position['year'].'" />
            <input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;" /><br>';
         
            echo '<textarea name="desc'.$countPos.'" rows="8" clos="80">'."\n";
            echo htmlentities($position['description'])."\n";
            echo "\n</textarea>\n</div>\n";
            }
        }
    ?>
</div>
</p>
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</form>

</div>
<script>
    countPos = <?= $countPos ?>;
    countEdu = <?= $countEdu ?>;
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
        });
        
        $('#addEdu').click(function(event){
            event.preventDefault();
            if( countEdu >= 9){
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log('Adding education'+countEdu);
            //grab some html and insert into DOM
            let source = $('#edu-template').html();
            $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));
            //add the event handler to the new ones
            $('.school').autocomplete({source: "school.php"});
        });
        $('.school').autocomplete({source: "school.php"});
    })
</script>
<script id='edu-template' type='text'>
    <div id="edu@COUNT@">
        <p>Year: <input type="text" name="edu_year@COUNT@" value="" /> 
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;" /><br/>
        <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" /></p> 
    </div>
</script>

</body>
</html>


<!--
countEdu = 0
        $('#addEdu').click(function(event){
            event.preventDefault();
            if( countEdu >= 9){
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log('Adding education'+countEdu);
            $('#education_fields').append(
                '<div id="education'+countEdu+'"> \
                <p>Year: <input type="text" name="year'+countEdu+'" value="" /> \
                <input type="button" value="-"   \
                        onclick="$(\'#education'+countEdu+'\').remove();return false;" />    \
                </p>  \
                <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" rows="8" clos="80" /></p>  \
                </div>'
            );
        });
        $('.school').autocomplete({source: "school.php"});
 */-->