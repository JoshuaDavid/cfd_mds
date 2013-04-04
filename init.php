<?
    $pageTitle = "Face Similarity Survey";
    // Whether to say that the demographic information is optional.
    $demographicInformationIsOptional = false;
    // The number of options on the scale (for example, if the number is 7,
    // then there will be a scale from 1 to 7 where 1 is completely 
    // different and 7 is identical.
    $num_scale_options = 7;
    // Where the datastore is located.
    $datastore = "../../cfd_mds_data/datastore.txt";
    // Where the participants log is located.
    $participants = "../../cfd_mds_data/participants.txt";
    // How many face pairs participants will compare in a single session.
    $job_size = 50;
    // The number of times each pair of faces will be compared. This is
    // 15 comparisons of face A to face B and also 15 comparisons of 
    // face B to face A.
    $ip_limit = 10000;
    // How many comparisons (not how many jobs: this is important) can
    // come from a single IP address.
    $num_comparisons = 15;
    // The cooldown period between faces, in milliseconds.
    $cooldown = 5000;
    // The confirmation code
    if(strlen($_REQUEST['confirmation']) > 1) {
        $confirmation = $_REQUEST['confirmation'];
    }
    else {
        // Confirmation code. The code is 10 random digits that will add 
        // to an integer multiple of 10. Since the code looks randomly 
        // generated, it's likely that people will not attempt to crack 
        // it, since it's really not worth their time. Codes can be 
        // verified easily by adding the digits and verifying that the 
        // sum is divisible by 10.
        $s = 0;
        $confirmation = "";
        for($i = 0; $i < 9; $i++) {
            $n = mt_rand(0, 9);
            $s = ($s + $n) %10;
            $confirmation .= $n;
        }
        $n = 10 - $s;
        $confirmation .= $n;
    }
