<?php
require_once "pdo.php";
require_once "util.php";
session_start();

//Retrieve the profiles from data base
$stmt = $pdo->query("SELECT * FROM profile");
$profiles = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
<title>Resume Registry John A 00063ece</title>
<?php require_once "head.php"; ?>
<script src="js/handlebars.js"></script>
</head><body>
    <div class="container">
    <h1>Welcome to the Resume Registry</h1>
    
<?php
flashMessages();

if ( !isset($_SESSION['user_id'])) {
    echo '<a href="login.php">Please log in</a>';
  } else {
    echo '<a href="form.php">Add Entry</a>';
    echo '<a href="logout.php">Log out</a>';
}
  
?>



<div id="list-area"><img src="spinner.gif" alt=''></div>

<script>
$(document).ready(function(){
    $.getJSON('profiles.php', function(profiles) {
        window.console && console.log(profiles);
        var source  = $("#list-template").html();
        var template = Handlebars.compile(source);
        var context = {};
        context.loggedin = 
            <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
        context.profiles = profiles;
        $('#list-area').replaceWith(template(context));
    }).fail( function() { alert('getJSON fail'); } );
});
</script>

<script id="list-template" type="text/x-handlebars-template">
  {{#if profiles.length}}
    <p><table border="1">
      <tr><th>Name</th><th>Headline</th>
      {{#if loggedin}}<th>Action</th>{{/if}}</tr>
      {{#each profiles}}
        <tr><td><a href="view.php?profile_id={{profile_id}}">
        {{first_name}} {{last_name}}</a>
        </td><td>{{headline}}</td>
        {{#if ../loggedin}}
          <td>
          <a href="form.php?profile_id={{profile_id}}">Edit</a> 
          <a href="delete.php?profile_id={{profile_id}}">Delete</a>
          </td>
        {{/if}}
        </tr>
      {{/each}}
    </table></p>
  {{/if}}
</script>
</div>
</body>
</html>
