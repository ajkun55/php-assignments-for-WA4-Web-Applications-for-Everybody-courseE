
<!DOCTYPE html>
<html>
<head>
<title>John A - Request/Response Cycle</title>
</head>
<body>
<h1>John A  Request / Response</h1>
<p>The SHA256 hash of "John A " is
<?php print hash('sha256', 'John A'); 
    ?>
</p>
<?php

$name = ' 
	************
	************
		  **
		  **
		  **
	      ******
	      ******';


echo "<pre>ASCII Art:";
echo $name;
echo "</pre>";

?>

<a href="check.php">Click here to check the error setting</a>
<br/>
<a href="fail.php">Click here to cause a traceback</a>
</body>
