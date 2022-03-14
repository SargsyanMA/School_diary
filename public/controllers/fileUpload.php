<?
require_once ("../app/init.php");

function transliterate($string) {
    $replace=array(
        "'"=>"",
        "`"=>"",
        " "=>"_",
        "."=>"",
        "."=>"",
        "/"=>"",
        "а"=>"a","А"=>"a",
        "б"=>"b","Б"=>"b",
        "в"=>"v","В"=>"v",
        "г"=>"g","Г"=>"g",
        "д"=>"d","Д"=>"d",
        "е"=>"e","Е"=>"e",
        "ж"=>"zh","Ж"=>"zh",
        "з"=>"z","З"=>"z",
        "и"=>"i","И"=>"i",
        "й"=>"y","Й"=>"y",
        "к"=>"k","К"=>"k",
        "л"=>"l","Л"=>"l",
        "м"=>"m","М"=>"m",
        "н"=>"n","Н"=>"n",
        "о"=>"o","О"=>"o",
        "п"=>"p","П"=>"p",
        "р"=>"r","Р"=>"r",
        "с"=>"s","С"=>"s",
        "т"=>"t","Т"=>"t",
        "у"=>"u","У"=>"u",
        "ф"=>"f","Ф"=>"f",
        "х"=>"h","Х"=>"h",
        "ц"=>"c","Ц"=>"c",
        "ч"=>"ch","Ч"=>"ch",
        "ш"=>"sh","Ш"=>"sh",
        "щ"=>"sch","Щ"=>"sch",
        "ъ"=>"","Ъ"=>"",
        "ы"=>"y","Ы"=>"y",
        "ь"=>"","Ь"=>"",
        "э"=>"e","Э"=>"e",
        "ю"=>"yu","Ю"=>"yu",
        "я"=>"ya","Я"=>"ya",
        "і"=>"i","І"=>"i",
        "ї"=>"yi","Ї"=>"yi",
        "є"=>"e","Є"=>"e"
    );
    return $str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
}

$data = array();

if(isset($_FILES)) {
    //print_r($_FILES);
    $error = false;
    $files = array();

    if (!file_exists('../elfinder/files/'.User::getInstance()->getUserId().'/files')) {
        mkdir('../elfinder/files/' . User::getInstance()->getUserId().'/files', 0777, true);
    }

    $uploadDir = '../elfinder/files/'.User::getInstance()->getUserId().'/files/';
    foreach($_FILES as $file) {

        $info = pathinfo($file['name']);
        $fileName = transliterate($info['filename']).'.'.$info['extension'];


        if(move_uploaded_file($file['tmp_name'], $uploadDir .$fileName)) {
            $fileUrl = $uploadDir .$fileName;
            DB::getInstance()->exec("INSERT INTO file (url, name) VALUES ('{$fileUrl}', '{$file['name']}')");
            $fileId=DB::getInstance()->lastInsertId();

            $files[] = $fileId;
        }
        else {
            $error = true;
        }
    }
    $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
}
else {
    $data = array('success' => 'Form was submitted', 'formData' => $_POST);
}

echo json_encode($data);