<?php
require_once ("../init.php");
echo 1;
$files=DB::getInstance()->getArray("select * from files where type='file' and size=0");
foreach ($files as $file) {
    $path='../../upload/user/'.$file['user'].'/files/'.$file['realname'];

    echo $path.'<br>';
    $size = filesize($path);
    $timestamp = filemtime($path);
    echo $size;

    $sql="update files set `timestamp`={$timestamp}, `size`={$size} where id={$file['id']} ";
    echo $sql.'<br>';
    //DB::getInstance()->exec($sql);
}

//d($files);