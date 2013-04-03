<? 
require "./init.php";
$age = $_POST['age'];
$gender = $_POST['gender'];
$zipcode = $_POST['zipcode'];

if(!strlen($_POST['submit'])) {
?>
<html>
    <head>
	<meta charset="utf-8" />
	<title><? echo $pageTitle; ?></title>
    </head>
    <body>
	<h1><? echo $pageTitle; ?></h1>
        <h2>Congratulations! You've finished!</h2>
	<form method="post">
	    <p style="font-weight: bold;">Please take a moment to answer these brief<? echo $demographicInformationIsOptional ? " optional " : " "; ?>questions. This information is for research purposes only.</p>
	    <label for="age">What is your age in years?</label>
	    <br />
	    <input type="text" name="age" id="age" />
	    <br />
	    <label for="gender">What is your gender?</label>
	    <br />
	    <input type="radio" name="gender" value="male" id="male" />
	    <label for="male">Male</label>
	    <br />
	    <input type="radio" name="gender" value="female" id="female" />
	    <label for="female">Female</label>
	    <br />
	    <label for="zipcode">What is your current postal code?</label>
	    <br />
	    <input type="text" name="zipcode" id="zipcode" />
	    <br />
	    <button type="submit" name="submit" value="yes" >Get confirmation code</button>
            <? 
                if ($demographicInformationIsOptional) {
            ?>
            <button type="submit" style="background: #fff; border: 1px solid #ccc;">Get confirmation code without answering</button>
            <?
                }
            ?>
	</form>
    </body>
</html>
<?
}
else {
    // Confirmation    Age    Gender    Postal Code
    file_put_contents(
        "./participants.txt",
        "{$confirmation}\t{$age}\t{$gender}\t{$zipcode}\n",
        FILE_APPEND
    );
?>
<script type="text/javascript" />
    window.location = "./debriefing.php?confirmation=<? echo $confirmation; ?>";
</script>
<?
}
