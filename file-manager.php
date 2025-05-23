<?php
require_once __DIR__ . '/request.php';

$handled = handle_request();
if ($handled) {
    return;
}

$MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));
include __DIR__ . '/template.php';

