<?php


    function getex($filename) {
        return end(explode('.', mb_strtolower($filename)));
    }

    if($_FILES['upload']) {
        if (($_FILES['upload'] == "none") || (empty($_FILES['upload']['name'])) ) {
            $message = "Вы не выбрали файл";
        } elseif ($_FILES['upload']["size"] == 0 || $_FILES['upload']["size"] > 2050000) {
            $message = "Размер файла не соответствует нормам";
        } elseif (!in_array( getex($_FILES['upload']['name']), ['jpg', 'jpeg', 'png']) ) {
            $message = "Неверный тип картинки";
        } elseif (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
            $message = "Что-то пошло не так. Попытайтесь загрузить файл ещё раз";
        } else {
            $name = rand(1, 1000).'-'.md5($_FILES['upload']['name']) . '.' . getex($_FILES['upload']['name']);

            $path = '/var/www/oreshki/data/www/oreshki-news.net/dimg/fulltext/';

            move_uploaded_file($_FILES['upload']['tmp_name'], $path . $name);

            //$webPath = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/dimg/fulltext/' . $name;
            $webPath = '/dimg/fulltext/' . $name;
            $message = 'Файл ' . $_FILES['upload']['name'] . ' загружен';

            $size = @getimagesize($path . $name);

            if($size[0] < 50 || $size[1] < 50){
                unlink($path . $name);
                $message = 'Файл не является допустимым изображением';
                $webPath = '';
            }
        }
        $callback = $_REQUEST['CKEditorFuncNum'];
        $return = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("'.$callback.'", "'.$webPath.'", "'.$message.'" );</script>';

        die($return);
    }