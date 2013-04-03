<? 
require "./init.php";
$age = $_POST['age'];
$gender = $_POST['gender'];
$zipcode = $_POST['zipcode'];
$confirmation = $_REQUEST['confirmation'];
?>
<html>
    <head>
	<meta charset="utf-8" />
	<title><? echo $pageTitle; ?></title>
    </head>
    <body>
	<h1><? echo $pageTitle; ?></h1>
	<h2>Demographic Information</h2>
	<form method="post">
	    <p style="font-weight: bold;">Please take a moment to answer these brief<? echo $demographicInformationIsOptional ? " optional " : " "; ?>questions. This information is for research purposes only.</p>
	    <label for="age">What is your age in years?</label>
	    <br />
	    <input type="text" name="age" id="age" />
	    <br />
	    <label for="gender">What is your gender?</label>
	    <br />
	    <input type="radio" name="gender" id="male" />
	    <label for="male">Male</label>
	    <br />
	    <input type="radio" name="gender" id="female" />
	    <label for="female">Female</label>
	    <br />
	    <label for="zipcode">What is your current zipcode?</label>
	    <br />
	    <input type="text" name="zipcode" id="zipcode" />
	    <br />
	    <button type="submit">Get confirmation code</button>
	</form>
    </body>
</html>
