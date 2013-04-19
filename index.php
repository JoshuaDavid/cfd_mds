<?
require "./init.php";
// Show the consent form, then when they've clicked the checkbox, show the instructions and a link to the study.
if($_POST['consent-checkbox'] !== "on") {
    // They have not clicked the checkbox to consent, possibly because they're arriving on the page
    // for the first time. Show the consent form.
?>
<html>
    <head>
	<meta charset="utf-8" />
        <link rel="stylesheet" type"text/css" href="./cfd_mds.css" />
	<title><? echo $pageTitle; ?></title>
    </head>
    <body>
	<form method="post" class="consent">
            <h1><? echo $pageTitle; ?></h1>
	    <h2>California State University, Northridge</h2>
	    <h4>RUI: The Role of Facial Physiognomy in Stereotypic Trait Inference</h4>
	    <h4>Information Letter and Consent Form – Online Raters page 1 of 1</h4>
	    <i>Introduction and Description</i>
	    <p>You are invited to participate in a research project on face perception. The goal of this project is to create a database of pictures featuring different people making different facial expressions of emotions. This database of images will be used in future investigations by researchers at University of Chicago and California State University at Northridge, and will be made available to other researchers at other universities for research purposes. This research is funded by the National Science Foundation and is being supervised by Debbie Ma PhD. </p>
	    <i>Voluntary Participation</i>
	    <p>Although your participation is greatly valued, you have the right to withdraw your consent and leave the study at any time without prejudice.</p>
	    <i>Participant Information and Payment</i>
	    <p>If you choose to participate, you will be shown pictures of individuals and be asked to rate those individuals on a number of dimensions. Your participation should last 5 minutes and will involve approximately 5,000 other participants. In exchange for your complete participation, you will receive $0.50. </p>
	    <i>Possible Risks and Withdrawing Consent</i>
	    <p>There are no anticipated risks or benefits of participating in the project.</p>
	    <i>Release and Use</i>
	    <p>Your responses will remain anonymous and will not be traceable to you. Survey data will be maintained in a locked file cabinet in a locked room and only be accessible to the principal investigator and her collaborators on this project.</p>
	    <i>Questions and Concerns</i>
	    <p>If you have any questions or concerns about this project, you may contact the principal investigator, Debbie Ma, California State University - Northridge, Department of Psychology, 18111 Nordhoff Street, Northridge, CA 91330-8255. You may also contact the principal investigator by phone or email at (818) 677-2901 or Debbie.Ma@csun.edu.</p>
	    <p>If you have any questions about research subjects' rights, or in the event of a research-related injury or concern, you may contact the office of Office of Research and Sponsored Projects, 18111 Nordhoff Street, Northridge, CA 91330-8255. You can also call the Office of Research and Sponsored Projects at (818) 677-2901.</p>
	    <h3>Please check the box and click "proceed" if you agree to participate.</h3>
	    <p>
		<span class="center">
		    <label for="consent-checkbox">I agree to the terms of this study</label>
		    <input type="checkbox" name="consent-checkbox" id="consent-checkbox" />
		</span>
	    </p>
	    <p>
		<span class="center">
		    <button type="submit" id="proceed" >Proceed to Study</button>
		</span>
	    </p>
	</form>
    </body>
</html>
<?
}
else {
?>
In order to complete this study, JavaScript must be enabled in your browser. If you are not redirected in the next 5 seconds, please make sure that you have JavaScript enabled.
<script type="text/javascript">
    window.location = "./study.php";
</script>
<?
}
