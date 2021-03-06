<?php
    require "./init.php";
    //$alreadydone = preg_split('[\n]', file_get_contents($datastore));
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
        // Yes we do.
        // $comparisons[$a][$a] = 0;
    }
    /*
    foreach($alreadydone as $row) {
	$cols = preg_split("[\t]", $row);
	$comparisons[$cols[0]][$cols[1]] -= 1;
    }
    */
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
            if(/* $a !== $b and */ $comparisons[$a][$b] > 0) {
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
            <? require "./study.js" ?>
	</script>
    </head>
    <body>
        <span class="code"><? echo $confirmation; ?></span>.
	<div class = "container">
            <h1><? echo $pageTitle; ?></h1>
            <div>Please do not press the "Back" button at any time while participating in this study or your progress may be lost.</div>
            <div class = "faces">
            </div>
            <div class = "similaritybuttons">
                <p>Please rate these two faces in terms of how different versus similar they are to each other in terms of their physical features.</p>
                <span>Completely Different</span>
                <?            
                    for($i = 1; $i <= $num_scale_options; $i++) {
                        echo '<input type = "button" class = "similaritybutton" value = "'. $i .'" />';
                    }
                ?>
                <span>Identical</span>
	    </div>
            <p>Progress</p>
            <div class = "progressbar">
                <div class = "progressinner"></div>
            </div>
	</div>
    </body>
</html>
