<?php

namespace app\models;

use app\components\acResizeImage;
use Yii;

class News extends \yii\db\ActiveRecord {

    const SCENARIO_CREATE = 'create';

    public $uploaded;

    public static function tableName() {
        return 'news';
    }

    public function rules() {
        return [
            [['capt', 'txt_full', 'txt_short', 'url_full', 'url_short', 'source_name', 'source_link'], 'filter', 'filter' => 'trim'],
            [['id_category', 'lang_code', 'capt', 'txt_short', 'txt_full', 'source_name', 'source_link'], 'required'],
            [['source_link'], 'checkAddress'],
            [['uploaded'], 'required', 'on' => [self::SCENARIO_CREATE], 'message' => 'Необходимо прикрепить изображение'],
            [['uploaded'], 'saveUploaded']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'capt' => 'Заголовок',
            'id_category' => 'Категория',
            'lang_code' => 'Язык',
            'txt_full' => 'Полный текст',
            'txt_short' => 'Краткий текст',
            'url_full' => 'Адрес полной новости',
            'url_short' => 'Адрес краткой новости',
            'img_file' => 'Изображение',
            'uploaded' => 'Прикрепить изображение',
            'source_name' => 'Название источника',
            'source_link' => 'Ссылка на источник'
        ];
    }

    public static function checkDuplicates($shortUrl)
    {
        $rows = Yii::$app->db->createCommand("
          SELECT id FROM
              (SELECT id, url_short FROM news ORDER BY id DESC LIMIT 10) AS last_news
          WHERE url_short LIKE :shortUrl LIMIT 1
        ")
        ->bindValue(':shortUrl', '%' . $shortUrl)
        ->queryColumn();

        return ($rows) ? true : false;
    }

    public function checkAddress($attribute) {
        if (!preg_match('#^(http|https):\/\/\w+#', $this->source_link)) {
            $this->addError($attribute, 'Неверный адрес');
            return false;
        }
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    public function getLastUser() {
        return $this->hasOne(User::className(), ['id' => 'lastUserId']);
    }

    public static function generateUrl($title, $postfix = '') {

        $arr_replace = [
            'ч' => 'ch', 'щ' => 'shch', 'ш' => 'sh', 'ю' => 'yu',
            'я' => 'ya', 'ё' => 'yo', 'ж' => 'zh', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'з' => 'z', 'и' => 'i', 'й' => 'i',
            'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'x', 'ц' => 'c' , 'ъ' => '_',
            'ы' => 'y', 'ь' => '_', 'э' => 'e'
        ];

        $url = strtr(mb_strtolower( $title, 'UTF-8' ), $arr_replace);

        $url = preg_replace('/[^a-z ]/', '', $url);
        $url = preg_replace('/\s/', '-', $url);
        $url = mb_substr($url, 0, 108);
        $url .= $postfix . '.html';

        return $url;
    }

    public function saveUploaded() {
        if (!$this->hasErrors()) {
            if ($this->uploaded->exists() && $this->uploaded->save()) {
                $filename = $this->uploaded->name;
                $ext = end(explode('.', $filename));

                //$fullPath = Yii::getAlias("@app/web/dimg/") . $filename;
                $fullPath = '/var/www/oreshki/data/www/oreshki-news.net/dimg/' . $filename;

                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->addError('uploaded', 'Неверный формат файла');
                    @unlink($fullPath);
                    return false;
                }


                //$resizeName = substr(md5(uniqid()), 0, 27);


                //$image = new acResizeImage($fullPath);
                //$imageBig = new acResizeImage($fullPath);
                //$imageFilename = basename($image->cropSquare()->resize(300, 300, true)->save(dirname($fullPath).'/', $resizeName, false, true, 78));
                //$imageFilename800 = basename($imageBig->resize(800, 800)->save(dirname($fullPath).'/', $resizeName . '_800', false, true, 78));
                //
                //$this->img_file = $imageFilename;

                $this->img_file = $filename;


                //
                //$image = new acResizeImage($fullPath);
                //$image->cropSquare()
                //->resize(530, 530)
                //->crop(0, 116, 530, 298)
                //->save(dirname($fullPath).'/', $resizeName . '_16x9', false, true, 88);
                //
                //@unlink($fullPath);
            }
        } else {
            if ($this->scenario == self::SCENARIO_CREATE) {
                $this->addError('uploaded', 'Необходимо добавить изображение');
            }
        }
    }

    public function resizeImage() {

        $dimgPath = '/var/www/oreshki/data/www/oreshki-news.net/dimg/';
        $fullPath = $dimgPath . $this->img_file;

        if (!is_dir($dimgPath . $this->id)) {

            if (!mkdir($dimgPath . $this->id)) {
                $this->addError('uploaded', 'Ошибка создания директории');
                return false;
            }

            chmod($dimgPath . $this->id, 0777);
        }


        $image = new acResizeImage($fullPath);
        $imageBig = new acResizeImage($fullPath);
        $imageFilename = basename($image->cropSquare()->resize(300, 300, true)->save($dimgPath, 'id' . $this->id, false, true, 78));
        $imageFilename800 = basename($imageBig->resize(800, 800)->save($dimgPath, 'id' . $this->id . '_800', false, true, 78));

        $this->img_file = $imageFilename;

        $image = new acResizeImage($fullPath);
        $image->cropSquare()
            ->resize(530, 530)
            ->crop(0, 116, 530, 298)
            ->save($dimgPath . $this->id . '/', 'id' . $this->id . '_16x9', false, true, 88);

        @unlink($fullPath);
    }

}