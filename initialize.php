<?
// These are all default variables that are used by the script.
global $defaults;
$defaults = array(
    // The location of the files. The ./ means "look in the current
    // directory, so if index.php is in public_html/cfd_mds, it will look for
    // a file named public_html/cfd_mds/datastore.txt.
    'datastore' =>  './datastore.txt',
    'consentform' =>  './consent.html',
    'imagelocations' => './imagelocations.txt',
    'colnames' => array(
        'ID_F1',
        'ID_F2',
        'URL_F1',
        'URL_F2',
        'SIMILARITY',
        'CONFIRMATION_CODE',
        'IP_ADDRESS',
        'TIMESTAMP'
    ),
    // $parseMalformedData says whether or not to attempt to parse data that
    // might be badly formed. If false, it will just skip rows with badly 
    // formed data.
    'parseMalformedData' => true,
    'pairsPerTrial' => 50,
    'comparisonsPerPair' => 15,
    'numberOfLevels' => 15
);
