<?php
    require "./init.php";
    $alreadydone = preg_split('[\n]', file_get_contents('datastore.txt'));
    $imagelocations = file_get_contents('imagelocations.txt');
    $faceLocations = preg_split('[\n]', $imagelocations);
    $num_images = sizeof($faceLocations) - 1;

    for($a = 0; $a < $num_images; $a++) {
	for($b = 0; $b < $num_images; $b++) {
	    $comparisons[$a][$b] = $num_comparisons;
	}
    }
    for($a = 0; $a < $num_images; $a++) {
	// We don't want to compare a face to itself
	$comparisons[$a][$a] = 0;
    }
    foreach($alreadydone as $row) {
	$cols = preg_split("[\t]", $row);
	$comparisons[$cols[0]][$cols[1]] -= 1;
    }
    $pairs = '[';
    $num_pairs = 0;
    // More padding means the server will be (slightly) slower, but also
    // means that it will be a bit more robust against dropped responses.
    // Padding of 50 is more than we will realistically need.
    $padding = 50;
    while ($num_pairs < $job_size + $padding) {
	// Generate a pair.
	for($i = 0; $i < 50; $i++) {
	    // Performance hack to make it so that if it doesn't succeed in
	    // generating a valid pair in 50 tries, it just goes with what it has.
	    // This should cause fairly minimal problems, with some face pairs
	    // getting 17 comparisons and some only getting 13 or 14, but it's
	    // still better than true random generation and faster than a purely
	    // deterministic strategy.
	    $a = mt_rand(0, $num_images - 1);
	    $b = mt_rand(0, $num_images - 1);
	    if($a !== $b and $comparisons[$a][$b] > 0) {
		$comparisons[$a][$b]--;
		$pairs .= '[' . $a . ',' . $b . '],'; 
		break;
	    }

	}
	$num_pairs++;
    }
    $pairs .= ']';
    // Face1 Face2 Face1URL Face2URL Similarity IP Timestamp
    /**/
    $fl = '{';
    foreach($faceLocations as $idx => $val) {
	$val = preg_replace('/[^A-Za-z0-9\_\.\/\:\~\-]/', '', $val);
	$fl .= "$idx: '$val',";
    }
    $fl .= '}';
?>
<html>
    <head>
	<title><? echo $pageTitle; ?></title>
        <link rel="stylesheet" type"text/css" href="./cfd_mds.css" />
	<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type = "text/javascript">
	</script>
    </head>
    <body>
	<div class = "container">
            <h1><? echo $pageTitle; ?></h1>
            <div>Please do not press the "Back" button at any time while participating in this study or your progress may be lost.</div>
	<div class = "faces">
	    <?
		foreach($faceLocations as $idx => $fl) echo "<img src = '{$fl}' id = 'f2_{$idx}' class = 'f2' />";
	    ?>
	</div>
	<div class = "similaritybuttons">
	    <p>Please rate these two faces in terms of how different versus similar they are to each other.</p>
	    <span>Completely Different</span>
            <?            
                for($i = 1; $i <= $num_scale_options; $i++) {
                    echo '<input type = "button" class = "similaritybutton" value = "'. $i .'" />';
                }
            ?>
	    <span>Identical</span>
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
