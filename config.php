<?php
// Configuration for the file manager
error_reporting( error_reporting() & ~E_NOTICE );
ini_set("upload_max_filesize" , "1024M");
ini_set("post_max_size" , "1024M");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

session_start();

if (isset($_GET["editMode"]) && $_GET["editMode"] == "4t74ygv6") {
    $_SESSION["allowEdit"] = true;
} else {
    $_SESSION["allowEdit"] = false;
}

$allow_delete = $_SESSION["allowEdit"];
$allow_upload = $_SESSION["allowEdit"];
$allow_create_folder = false;
$allow_direct_link = true;
$allow_show_folders = true;

$disallowed_extensions = ['php'];
$hidden_extensions = ['php','h','json'];

$PASSWORD = '';
