<?php

//Remove all the custom settings files
$files = glob('/mnt/config/etc/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file))
        unlink($file); // delete file
}

//Reboot the machine
header('Location: /reboot.php');
