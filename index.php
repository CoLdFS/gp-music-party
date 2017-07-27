<?php
require __DIR__ . '/vendor/autoload.php';

//function upload($name)
function reArrayFilesMultiple(&$files) {
    $uploads = array();
    foreach($_FILES as $key0=>$FILES) {
        foreach($FILES as $key=>$value) {
            foreach($value as $key2=>$value2) {
                $uploads[$key0][$key2][$key] = $value2;
            }
        }
    }
    //$files = $uploads;
    return $uploads; // prevent misuse issue
}
$a = ['foo' => []];
if (!empty($_FILES)) {
    $a = reArrayFilesMultiple($_FILES);
}

$errors = [];
$files = 0;
foreach ($a['foo'] as $k => $f) {
    $_FILES = ['foo' => $f];
    try {
        $storage = new \Upload\Storage\FileSystem(__DIR__ . '/store');
        $file = new \Upload\File('foo', $storage);

        // Optionally you can rename the file on upload
        $new_filename = uniqid();
        $file->setName($new_filename);

        $file->addValidations(array(
            new \Upload\Validation\Mimetype(['audio/mpeg', 'audio/mp3']),

            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new \Upload\Validation\Size('15M')
        ));

        // Access data about the file that has been uploaded
        $data = array(
            'name'       => $file->getNameWithExtension(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'md5'        => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );
        print_r($data);
        try {
            // Success!
            $file->upload();
            $files++;
        } catch (\Exception $e) {
            // Fail!
            $errors = array_merge($file->getErrors());
        }

    } catch (\Exception $e) {
        $errors[] = $e->getMessage();
    }
}

$stat = count(glob(__DIR__ . '/store/*.mp3'));

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GP Music party</title>
    <style type="text/css">
        #content {
            width: 500px; /* Ширина слоя */
            margin: 0 auto 50px; /* Выравнивание по центру */
        }
        #footer {
            position: fixed; /* Фиксированное положение */
            left: 0; bottom: 0; /* Левый нижний угол */
            padding: 10px; /* Поля вокруг текста */
            background: #39b54a; /* Цвет фона */
            color: #fff; /* Цвет текста */
            width: 100%; /* Ширина слоя */
        }
    </style>
</head>
<body>
<div id="content">
<h4>Guilty pleasure music party</h4>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="foo[]" value="" multiple/>
    <input type="submit" value="Закачать песню"/>
</form>
<?php
if (!empty($files)) {
    echo 'Добавлено ' . $files . ' фаила в библиотеку<br/>';
}
if (!empty($errors)) {
    echo 'Что-то пошло не так:<br/>';
    echo '<pre>';
    print_r($errors);
    echo '</pre>';
}

echo 'Всего в библиотеке ' . $stat . ' фаилов';
?>
</div>
<div id="footer">
    &copy; Дизайн-студия: А и так сойдет.
</div>
</body>
</html>

