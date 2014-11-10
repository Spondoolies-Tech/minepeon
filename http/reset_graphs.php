<?php

if (isset($_POST['ip']) && isset($_POST['reset_graphs'])) {
    $dir = './rrd/';
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") { // strip the current and previous directory items
                unlink($dir . $file); // you can add some filters here, aswell, to filter datatypes, file, prefixes, suffixes, etc
            }
        }
        closedir($handle);
    }
}
header("Location: /");
?>	