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
	    <label for="zipcode">What is your current zipcode?</label>
	    <br />
	    <input type="text" name="zipcode" id="zipcode" />
	    <br />
            <label for="race-ethicity">What is your race/ethnicity (click all that apply)</label>
            <br />
            <input type="checkbox" name="race-white-european" id="white-european" value="white-european" />
            <label for="white-european">White (European descent)</label>
            <br />
            <input type="checkbox" name="race-white-middle-eastern" id="white-middle-eastern" value="white-middle-eastern" />
            <label for="white-middle-eastern">White (Middle-Eastern descent)</label>
            <br />
            <input type="checkbox" name="race-black" id="black" value="black" />
            <label for="black">Black</label>
            <br />
            <input type="checkbox" name="race-latino" id="latino" value="latino" />
            <label for="latino">Latino</label>
            <br />
            <input type="checkbox" name="race-asian" id="asian" value="asian" />
            <label for="asian">Asian</label>
            <br />
            <input type="checkbox" name="race-middle-eastern" id="middle-eastern" value="middle-eastern" />
            <label for="middle-eastern">Middle-Eastern</label>
            <br />
            <input type="checkbox" name="race-biracial" id="biracial" value="biracial" />
            <label for="biracial">Biracial</label>
            <br />
            <input type="checkbox" name="race-other" id="other" value="other" />
            <label for="other">Other (Please Specify)</label>
            <input type="text" name="other-race" />
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
    $race  = (strlen($_POST['race-white-european']) > 0 ? 'white-european,' : '');
    $race .= (strlen($_POST['race-white-middle-eastern']) > 0 ? 'white-middle-eastern,' : '');
    $race .= (strlen($_POST['race-black']) > 0 ? 'black,' : '');
    $race .= (strlen($_POST['race-latino']) > 0 ? 'latino,' : '');
    $race .= (strlen($_POST['race-asian']) > 0 ? 'asian,' : '');
    $race .= (strlen($_POST['race-middle-eastern']) > 0 ? 'middle-eastern,' : '');
    $race .= (strlen($_POST['race-biracial']) > 0 ? 'biracial,' : '');
    $race .= (strlen($_POST['race-other']) > 0 ? "{$_POST['other-race']}" : '');

    file_put_contents(
        $participants,
        "{$confirmation}\t{$age}\t{$gender}\t{$zipcode}\t{$race}\n",
        FILE_APPEND
    );
?>
<script type="text/javascript" />
    //window.location = "./debriefing.php?confirmation=<? echo $confirmation; ?>";
    window.location = window.location;
</script>
<?
}
