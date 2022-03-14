<pre>
<?php
function sDir($root, $path) {
    $result=[];
    $files = scandir($root.$path);
    foreach($files as $file) {
        if ($file=='.' || $file=='..' || $file=='.quarantine' || $file=='.tmb') continue;
        if (is_dir($root.$path.$file)) {

            $result[]=[
                'name'=>'',
                'url'=>'',
                'path'=>'files/'.$path.$file,
                'type'=>'dir'
            ];

            $result=array_merge($result,sDir($root,$path.$file.'/'));
        }
        else {
            $result[]=[
                'name'=>$file,
                'url'=>$root.$path.$file,
                'path'=>'files/'.$path.$file,
                'type'=>'file'
            ];
        }
    }
    return $result;
}

$dir    = '../../elfinder/files/';
$users = scandir($dir);



foreach($users as $userId) {
    if (is_numeric($userId)) {
        $result[$userId]=sDir($dir.$userId.'/files/','');
    }

    $result[$userId][]=[
        'name'=>'',
        'url'=>'',
        'path'=>'files',
        'type'=>'dir'
    ];
}

foreach($result as $userId=>$files) {
    mkdir('../../upload/user/'.$userId.'/files/',0777,true);
    foreach($files as $file) {
        $sql="REPLACE INTO files (`path`, `type`, `size`, `mimetype`, `timestamp`, `realname`, `user`)
              VALUES ('{$file['path']}', '{$file['type']}',0,'',now(),'{$file['name']}','{$userId}');";
        echo $sql.'<br/>';

        file_put_contents('../../upload/user/'.$userId.'/files/'.$file['name'],file_get_contents($file['url']));
    }
}

//print_r($result);
