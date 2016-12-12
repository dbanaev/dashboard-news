<?php

namespace app\helpers;

use yii\web\UploadedFile;
use Yii;

class UploadedImage extends UploadedFile
{
    /**
     * @var string
     */
    private static $_localFile;

    /**
     * @inheritdoc
     * @return UploadedImage
     */
    public static function getInstance($model, $attribute, $localFile = null)
    {
        self::$_localFile = $localFile;
        return parent::getInstance($model, $attribute);
    }

    /**
     * @inheritdoc
     * @return UploadedImage[]
     */
    public static function getInstances($model, $attribute)
    {
        return parent::getInstances($model, $attribute);
    }

    /**
     * @inheritdoc
     * @return UploadedImage
     */
    public static function getInstanceByName($name)
    {
        if (self::$_localFile) {

            return new static([
                'name' =>  basename(self::$_localFile),
                'tempName' => self::$_localFile,
                'type' => image_type_to_mime_type(exif_imagetype(self::$_localFile)),
                'size' => filesize(self::$_localFile),
                'error' => '',
            ]);
        }

        return parent::getInstanceByName($name);
    }

    /**
     * @inheritdoc
     * @return UploadedImage[]
     */
    public static function getInstancesByName($name)
    {
        return parent::getInstancesByName($name);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if (self::$_localFile) {
            return is_file(self::$_localFile);
        }
        return is_uploaded_file($this->tempName);
    }

    /**
     * Saves image to upload folder
     * @return bool
     */
    public function save()
    {
        if (self::$_localFile) {
            $hash = md5_file(self::$_localFile);
        }
        else {
            $hash = md5_file($this->tempName);
        }

        //$uploadDir = 'dimg';

        $fileName = mb_substr($hash, 0, 20) . '-' . time() . '.' . $this->extension;

        // default umask = 0022
        umask(0);

        $realDir = '/var/www/oreshki/data/www/oreshki-news.net/dimg/';

        if ($this->saveAs("{$realDir}/{$fileName}")) {
            $this->name = "{$fileName}";
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function saveAs($file, $deleteTempFile = true)
    {
        if ($this->error == UPLOAD_ERR_OK) {
            if (self::$_localFile) {
                return rename(self::$_localFile, $file);
            }
            if ($deleteTempFile) {
                return move_uploaded_file($this->tempName, $file);
            } elseif (is_uploaded_file($this->tempName)) {
                return copy($this->tempName, $file);
            }
        }
        return false;
    }

}