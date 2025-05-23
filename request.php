<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/functions.php';

function handle_request() {
    global $allow_show_folders, $hidden_extensions, $allow_delete, $allow_upload,
           $allow_create_folder, $disallowed_extensions, $allow_direct_link, $PASSWORD;

    if ($PASSWORD) {
        session_start();
        if (!$_SESSION['_sfm_allowed']) {
            $t = bin2hex(openssl_random_pseudo_bytes(10));
            if ($_POST['p'] && sha1($t.$_POST['p']) === sha1($t.$PASSWORD)) {
                $_SESSION['_sfm_allowed'] = true;
                header('Location: ?');
            }
            echo '<html><body><form action=? method=post>PASSWORD:<input type=password name=p /></form></body></html>';
            return true;
        }
    }

    if (isset($_POST['youtube_url'])) {
        shell_exec("yt-dlp '{$_POST['youtube_url']}'");
        echo "\nDownloaded";
        return true;
    }

    setlocale(LC_ALL,'en_US.UTF-8');

    $tmp_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    if (DIRECTORY_SEPARATOR==='\\') {
        $tmp_dir = str_replace('/',DIRECTORY_SEPARATOR,$tmp_dir);
    }
    $tmp = get_absolute_path($tmp_dir . '/' .$_REQUEST['file']);

    if ($tmp === false) {
        err(404,'File or Directory Not Found');
    }
    if (substr($tmp, 0,strlen($tmp_dir)) !== $tmp_dir) {
        err(403,'Forbidden');
    }
    if (strpos($_REQUEST['file'], DIRECTORY_SEPARATOR) === 0) {
        err(403,'Forbidden');
    }

    if (!$_COOKIE['_sfm_xsrf']) {
        setcookie('_sfm_xsrf',bin2hex(openssl_random_pseudo_bytes(16)));
    }
    if ($_POST) {
        if ($_COOKIE['_sfm_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf']) {
            err(403,'XSRF Failure');
        }
    }

    $file = $_REQUEST['file'] ?: '.';
    if ($_GET['do'] == 'list') {
        if (is_dir($file)) {
            $directory = $file;
            $result = [];
            $files = array_diff(scandir($directory), ['.','..']);
            foreach ($files as $entry) if (!is_entry_ignored($entry, $allow_show_folders, $hidden_extensions)) {
                $i = $directory . '/' . $entry;
                $stat = stat($i);
                $result[] = [
                    'mtime' => $stat['mtime'],
                    'size' => sprintf('%u', $stat['size']),
                    'name' => basename($i),
                    'path' => preg_replace('@^\./@', '', $i),
                    'is_dir' => is_dir($i),
                    'is_deleteable' => $allow_delete && ((!is_dir($i) && is_writable($directory)) ||
                                                         (is_dir($i) && is_writable($directory) && is_recursively_deleteable($i))),
                    'is_readable' => is_readable($i),
                    'is_writable' => is_writable($i),
                    'is_executable' => is_executable($i),
                ];
            }
        } else {
            err(412,'Not a Directory');
        }
        echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' =>$result]);
        return true;
    } elseif ($_POST['do'] == 'delete') {
        if ($allow_delete) {
            rmrf($file);
        }
        return true;
    } elseif ($_POST['do'] == 'mkdir' && $allow_create_folder) {
        $dir = $_POST['name'];
        $dir = str_replace('/', '', $dir);
        if (substr($dir, 0, 2) === '..') {
            return true;
        }
        chdir($file);
        @mkdir($_POST['name']);
        return true;
    } elseif ($_POST['do'] == 'upload' && $allow_upload) {
        foreach ($disallowed_extensions as $ext) {
            if (preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES['file_data']['name'])) {
                err(403,'Files of this type are not allowed.');
            }
        }
        move_uploaded_file($_FILES['file_data']['tmp_name'], $file.'/'.$_FILES['file_data']['name']);
        return true;
    } elseif ($_GET['do'] == 'download') {
        $filename = basename($file);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, $file));
        header('Content-Length: '. filesize($file));
        header(sprintf('Content-Disposition: attachment; filename=%s',
                strpos('MSIE',$_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\"" ));
        ob_flush();
        readfile($file);
        return true;
    }

    return false;
}
