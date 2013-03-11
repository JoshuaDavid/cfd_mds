<?php
    $alreadydone = preg_split("[\n]", fread(fopen('datastore.txt', 'r'), 0x07FF));
    $imagelocations = fopen('imagelocations.txt', 'r');
    $faceLocations = preg_split("[\n]", fread($imagelocations, pow(2, 16)));
    $num_images = sizeof($faceLocations);
    $num_comparisons = 2;
    $job_size = 50;

    for($i = 0; $i < $num_images; $i++) {
	for($j = 0; $j < $num_images; $j++) {
	    $comparisons[$i][$j] = $num_comparisons;
	}
    }
    for($i = 0; $i < $num_images; $i++) {
	// We don't want to compare a face to itself
	$comparisons[$i][$i] = 0;
    }
    foreach($alreadydone as $row) {
	$cols = preg_split("[\t]", $row);
	$comparisons[$cols[0]][$cols[1]] -= 1;
    }
    $ct = 0;
    for($i = 0; $i < $num_images; $i++) {
	for($j = 0; $j < $num_images; $j++) {
	    for($k = 0; $k < $comparisons[$i][$j]; $k++) {
		$bigcomp[$ct] = array($i, $j);
		$ct++;
	    }
	}
    }
    shuffle($bigcomp);
    $num_pairs = 0;
    $pairs = '[';
    foreach($bigcomp as $pair) {
	if($num_pairs < $job_size + 10) {
	    //Build in a little padding if the server drops a response
	    $pairs .= "[{$pair[0]}, {$pair[1]}], ";
	    $num_pairs++;
	}
    }
    $pairs .= ']';
    // Face1 Face2 Face1URL Face2URL Similarity IP Timestamp
?>
<html>
    <head>
	<title>Face Similarity Survey</title>
	<style type = "text/css">
	    h1, h2, h3, h4 {
		text-align: center;
		margin: 0 auto;
	    }
	    .container {
		width: 80%;
		margin: 0 auto;
		text-align: center;
		position: relative;
	    }
	    .consent {
		text-align: left;
	    }
	    .consent p {
		text-indent: 4em;
	    }
	    .faces {
		display: block;
		min-height: 400px;
	    }
	    .center {
		text-align: center;
	    }
	    img {
		width: 48%;
		box-shadow: 0px 0px 5px 0px #000;
		display: none;
	    }
	    img.f1 {
		position: absolute;
		display: block;
		left: 1%;
	    }
	    img.f2 {
		position: absolute;
		display: block;
		right: 1%;
	    }
	    .progressbar {
		width: 100%;
		height: 20px;
		background-color: #ccc;
		border: 1px solid #000;
	    }
	    .progressinner {
		width: 0%;
		height: 20px;
		float: left;
		background-color: #7f7;
	    }
	    .spacer {
		width: 200px;
		background-color: #000;
	    }
	</style>
	<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type = "text/javascript">
	    imagePairs = <?php echo $pairs; ?>;
	    currentPair = -1;
	    $(document).ready(function() {
		$('.faces').hide();
		$('.similarityButtons').hide();
		$('.debriefing').hide();
		nextOk = false;
		$('input#proceed').click(function() {
		    if($('#consent-checkbox').attr('checked')) {
			$('.consent').hide();
			$('.faces').show();
			$('.similarityButtons').show();
			nextOk = true;
		    }
		});
		showFaces();
		$(document).on('keyup', function(e) {
		    $('input[value="'+(e.keyCode - 48)+'"]').click();
		});
		$('input.similarityButton').click(function() {
		    if(nextOk) {
			str  = '';
			console.log(imagePairs[currentPair][0]);
			str += 'f1='+imagePairs[currentPair][0];
			str += '&f2='+imagePairs[currentPair][1];
			str += '&f1url='+$('#f1_'+imagePairs[currentPair][0]).attr('src').split('sampletargets/')[1];
			str += '&f2url='+$('#f2_'+imagePairs[currentPair][1]).attr('src').split('sampletargets/')[1];
			str += '&similarity='+$(this).val();
			str += '&confirmation='+$('.code').text();
			console.log("Sending request");
			$.post('saveData.php', str, function(response) {
			    console.log(response);
			    nextOk = false;
			    //Makes sure they should still be submitting responses
			    if(response.match(/Success/g)) {
				showFaces();
				setTimeout("nextOk = true;", 000); //Enforce a 500ms delay between comparisons
			    }
			    else {
				$('.similaritybuttons').html('<p>Congratulations! You finished your <? echo $job_size; ?> allotted problems! </p>');
				console.log('done');
				$('.faces').hide();
				$('.similarityButtons').hide();
				$('.debriefing').show();
			    }
			});
		    }
		    else {
			console.log("Not ready yet.");
		    }
		});
	    });

	    function showFaces() {
		currentPair++;
		if(currentPair < <? echo $job_size ?>) {
		    $('img').hide();
		    $f1 = $('#f1_'+imagePairs[currentPair][0]);
		    $f2 = $('#f2_'+imagePairs[currentPair][1]);
		    $f1.show();
		    $f2.show();
		    $('.faces').height(0.75 * $('#f1_'+imagePairs[currentPair][0]).width());
		    //Progress bar: not strictly necessary, but who cares.
		    $('.progressinner').width(Math.floor(100 * currentPair / <? echo $job_size ?>)+'%');
		}
		else {
		    $('.similaritybuttons').html('<p>Congratulations! You finished your <? echo $job_size; ?> allotted problems! </p>');
		}

		function debrief() {
		    $('.faces').hide();
		    $('.similarityButtons').hide();
		    $('.debriefing').show();
		}
	    }
	</script>
    </head>
    <body>
	<div class = "container">
	    <h1>Face Similarity Survey</h1>
	    <div class="consent">
		<h2>California State University, Northridge</h2>
		<h4>RUI: The Role of Facial Physiognomy in Stereotypic Trait Inference</h4>
		<h4>Information Letter and Consent Form – Online Raters page 1 of 1</h4>
		<i>Introduction and Description</i>
		<p>You are invited to participate in a research project on face perception. The goal of this project is to create a database of pictures featuring different people making different facial expressions of emotions. This database of images will be used in future investigations by researchers at University of Chicago and California State University at Northridge, and will be made available to other researchers at other universities for research purposes. This research is funded by the National Science Foundation and is being supervised by Debbie Ma PhD. </p>
		<i>Voluntary Participation</i>
		<p>Although your participation is greatly valued, you have the right to withdraw your consent and leave the study at any time without prejudice.</p>
		<i>Participant Information and Payment</i>
		<p>If you choose to participate, you will be shown pictures of individuals and be asked to rate those individuals on a number of dimensions. Your participation should last 5 minutes and will involve approximately 5,000 other participants. In exchange for your complete participation, you will receive $0.25. </p>
		<i>Possible Risks and Withdrawing Consent</i>
		<p>There are no anticipated risks or benefits of participating in the project.</p>
		<i>Release and Use</i>
		<p>Your responses will remain anonymous and will not be traceable to you. Survey data will be maintained in a locked file cabinet in a locked room and only be accessible to the principal investigator and her collaborators on this project.</p>
		<i>Questions and Concerns</i>
		<p>If you have any questions or concerns about this project, you may contact the principal investigator, Debbie Ma, California State University - Northridge, Department of Psychology, 18111 Nordhoff Street, Northridge, CA 91330-8255. You may also contact the principal investigator by phone or email at (818) 677-2901 or Debbie.Ma@csun.edu.</p>
		<p>If you have any questions about research subjects' rights, or in the event of a research-related injury or concern, you may contact the office of Office of Research and Sponsored Projects, 18111 Nordhoff Street, Northridge, CA 91330-8255. You can also call the Office of Research and Sponsored Projects at (818) 677-2901.</p>
		<h3>Please check the box and click "proceed" if you agree to participate.</h3>
		<span class="center">
		    <label for="consent-checkbox">I agree to the terms of this study</label>
		    <input type="checkbox" id="consent-checkbox" />
		</span>
	    </p>
	    <p>
		<span class="center">
		    <input type="button" id="proceed" value="Proceed to Study" />
		</span>
	    </p>
	</div>
	<div class = "faces">
	    <?
		foreach($faceLocations as $idx => $fl) echo "<img src = '{$fl}' id = 'f1_{$idx}' class = 'f1' />";
		foreach($faceLocations as $idx => $fl) echo "<img src = '{$fl}' id = 'f2_{$idx}' class = 'f2' />";
	    ?>
	</div>
	<div class = "similaritybuttons">
	    <p>Please rate these two faces in terms of how different versus similar they are to each other.</p>
	    <span>Different</span>
	    <input type = "button" class = "similaritybutton" value = "1" />
	    <input type = "button" class = "similaritybutton" value = "2" />
	    <input type = "button" class = "similaritybutton" value = "3" />
	    <input type = "button" class = "similaritybutton" value = "4" />
	    <input type = "button" class = "similaritybutton" value = "5" />
	    <input type = "button" class = "similaritybutton" value = "6" />
	    <input type = "button" class = "similaritybutton" value = "7" />
	    <span>Similar</span>
	    <p>Progress</p>
	    <div class = "progressbar">
		<div class = "progressinner"></div>
	    </div>
	</div>
	<div class="debriefing">
	    <h2>California State University, Northridge</h2>
	    <h4>RUI: The Role of Facial Physiognomy in Stereotypic Trait Inference</h4>
	    <i>Debriefing</i>
	    <p>The study in which you just participated seeks to examine face perception. We are interested in assessing what features and characteristics make faces appear more similar and more dissimilar. Ultimately, the goal of this project is to create a database of pictures featuring different people making different facial expressions of emotions. This database of images will be used in future investigations by researchers at University of Chicago and California State University at Northridge, and will be made available to other researchers at other universities for research purposes. This research is funded by the National Science Foundation and is being supervised by Debbie Ma PhD.</p>
	    <p>If you have any questions or concerns about this project, you may contact the principal investigator, Debbie Ma, California State University - Northridge, Department of Psychology, 18111 Nordhoff Street, Northridge, CA 91330-8255. You may also contact the principal investigator by phone or email at (818) 677-2901 or Debbie.Ma@csun.edu.</p>
	    <p>If you have any questions about research subjects' rights, or in the event of a research-related injury or concern, you may contact the office of Office of Research and Sponsored Projects, 18111 Nordhoff Street, Northridge, CA 91330-8255. You can also call the Office of Research and Sponsored Projects at (818) 677-2901.</p>
	    <i>Your verification code is:</i>
	    <span class="code"><?
		// Confirmation code. The code is 10 random digits that will add to an integer multiple of 10.
		// Since the code looks randomly generated, it's likely that people will not attempt to crack
		// it, since it's really not worth their time. Codes can be verified easily by adding the digits
		// and verifying that the sum is divisible by 10.
		$s = 0;
		for($i = 0; $i < 9; $i++) {
		    $n = mt_rand(0, 9);
			$s = ($s + $n) %10;
			echo $n;
		    }
		    $n = 10 - $s;
		    echo $n;
		?></span>.
		<i>Please enter this code into the mTurk site to receive payment for your participation.</i>
	    </div>
	</div>
    </body>
</html>
