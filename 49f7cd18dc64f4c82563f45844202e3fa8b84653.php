<pre>
<?
$salt = "NaCl";
var_dump($_POST);
$password = $_POST['password'];
$hashedsaltedpassword = '$6$NaCl$LXCuXexRsGpteTk9m/ca5wKIKax4uidUuQSFzr.UWQKXJc0.YVKBOEg7b7F5FfxwSJt.EfiraENO/0dO9YD591';
$requestedpage = $_POST['page'];
if(crypt($password, '$6$'.$salt.'$') == $hashedsaltedpassword) {
    // Filename is the sha1 hash of "cfd_mds"
    // Since this code is publicly available on github, I think it's probably
    // a good idea to have some actual security so people can't view the data
    // without the password.
    echo file_get_contents("../../cfd_mds_data/{$requestedpage}.txt");
}
else {
    echo crypt($password, '$6$'.$salt.'$');
    echo "<br />";
    echo $hashedsaltedpassword;
    echo "<br />";
    echo "../../cfd_mds_data/{$requestedpage}.txt";
    echo "<br />";
    echo file_get_contents("../../cfd_mds_data/{$requestedpage}.txt");
    echo "<br />";
}
?>
</pre>
