<?php
include "mysql.php";
$token = $_GET['t'];
if (! isset($token) || ! check_valid_token($token))
    die(json_encode(array(
        'error' => 'no valid token provided',
        'debug' => ''
    )));
$filename = $_GET['fn'];
$CONFIG = include "config.php";

if (! is_file_owner($token, $filename))
    die(json_encode(array(
        'error' => 'no valid filename provided',
        'debug' => ''
    )));

$target = $CONFIG['filestore_dir'] . "/" . $filename;
if (! unlink($target))
    die(json_encode(array(
        'error' => 'no valid filename provided',
        'debug' => 'file either already deleted or never existed'
    )));

$result = delete_file($filename);
die(json_encode(array(
    'success' => '1',
    'debug' => $result
)));
?>