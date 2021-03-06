<?php
include "mysql.php";
$token = $_POST['t'];
if (! isset($token) || ! check_valid_token($token))
    die(json_encode(array(
        'error' => 'no valid token provided',
        'debug' => ''
    )));
$CONFIG = include "config.php";

$uploaded_file = $_FILES['ftu'];
$ending = explode(".", $uploaded_file['name']);
$ending = end($ending);

$valid_extension = true;
$blocked_extensions = array(
    "js",
    "php",
    "php4",
    "php3",
    "phtml",
    "rhtml",
    "html",
    "html",
    "xhtml",
    "jhtml"
);

foreach ($blocked_extensions as $blocked_extension) {
    if ($ending == $blocked_extension) {
        $valid_extension = false;
    }
}

if (! $valid_extension)
    die(json_encode(array(
        'error' => 'no valid file provided',
        'debug' => $ending
    )));

function random_string($length)
{
    $key = '';
    $keys = array_merge(range('A', 'Z'), range('a', 'z'));

    for ($i = 0; $i < $length; $i ++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

$length = 1;
$rand_name = random_string($length);
$newfilename = $rand_name . "." . $ending;
$target = $CONFIG['filestore_dir'] . "/" . $newfilename;
while (file_exists($target)) {
    $length = $length + 1;
    $rand_name = random_string($length);
    $newfilename = $rand_name . "." . $ending;
    $target = $CONFIG['filestore_dir'] . "/" . $newfilename;
}

if (! move_uploaded_file($uploaded_file['tmp_name'], $target))
    die(json_encode(array(
        'error' => 'server error moving file',
        'debug' => ''
    )));
// exif data stripping
try {
    $img = new \Imagick(realpath($target));
    $profiles = $img->getImageProfiles("icc", true);
    $img->stripImage();
    if (! empty($profiles))
        $img->profileImage("icc", $profiles['icc']);
} catch (Exception $ignored) {}
log_file($token, $newfilename);

$deletion_url = $CONFIG['webfront_url'] . "/scripts/delete.php";
die(json_encode(array(
    'url' => $CONFIG['webfront_url'] . '/' . $newfilename,
    'deletion_url' => $deletion_url . '?t=' . $token . "&fn=" . $newfilename
)));
?>