<?
include './init.php';
echo "hi";
$unsaferawdata = file_get_contents($datastore);
$unsaferows = explode("\n", $unsaferawdata);
$originalcolnames = array(
    'face1_id',
    'face2_id',
    'face1_url',
    'face2_url',
    'similarity',
    'confirmation_code',
    'ip_address',
    'timestamp'
);
$safecolumns = array(
    'face1_id',
    'face2_id',
    'face1_url',
    'face2_url',
    'similarity'
);
$data = array();
foreach($unsaferows as $unsaferow) {
    $rawvalues = explode("\t", $unsaferow);
    if(sizeof($rawvalues) > 1) {
        foreach($originalcolnames as $index => $column) {
            if(in_array($column, $safecolumns)) {
                $safecomparison[$column] = trim($rawvalues[$index]);
            }
        }
        $safedata[] = $safecomparison;
    }
}
echo json_encode($safedata);
