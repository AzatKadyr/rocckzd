<?php

/**
 * Класс для работы с изображениями
 */
class IMG{
    var $validGraphics = ["image/gif","image/jpg","image/jpeg","imagpe/jpeg","image/pjpg","image/pjepg","image/png"];//правильные заголовки графических файлов



    /**
     * Функция создания нового изображения
     * @param $fileName string имя исходного файла из которого будет создано изображения в памяти
     * @param $extension string расширение
     * @return resource|string $img resource идентификатор изображения, полученного из данного файла filename.
     */
    function create($fileName, $extension){
        $img = "";
        switch($extension){
            case "gif":
                $img = imagecreatefromgif($fileName);
                break;
            case "jpg":
            case "jpeg":
                $img = imagecreatefromjpeg($fileName);
                break;
            case "png":
                $img = imagecreatefrompng($fileName);
                break;
        }
        return $img;
    }

    /**
     * Функция сохранения изображения в файл
     * @param $extension string расширение файла
     * @param $img resource идентификатор изображения
     * @param $fileName string путь к файлу
     * @param $quality int качаство изображения [0..100]
     * @return bool|int $code: 1 - удачное сохранение, 0 - ошибка
     */
    function saveToFile($extension, $img, $fileName, $quality = 100){

        switch($extension){
            case "gif":
                return imagegif($img, $fileName);
                break;
            case "jpg":
            case "jpeg":
                return imagejpeg($img, $fileName, $quality);
                break;
            case "png":
                return imagepng($img, $fileName);
                break;
        }
        return 0;
    }

    /**
     * Функция пропорционального изменения размера графического файла(создается новый файл но старый не удаляется)
     * @param $oldPath string путь к исходному файлу
     * @param $newPath string путь к новому файлу
     * @param $newWidth int ширина нового файла
     * @param $newHeight int высота нового файла
     * @param $quality int качаство изображения [0..100]
     * @return bool|int $code: 1 - удачное сохранение, 0 - ошибка
     */
    function resizeProportional($oldPath, $newPath, $newWidth, $newHeight, $quality){
        $width = $newWidth;
        $height = $newHeight;

        $extension = $this->getExtension($oldPath);
        $img = $this->create($oldPath, $extension);
        list($width_orig, $height_orig) = getimagesize($oldPath);
        $ratio_orig = $width_orig / $height_orig;
        if ($width / $height > $ratio_orig){
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }

        $image_p = imagecreatetruecolor($width, $height);

        imageinterlace($image_p, true);
        imagealphablending($image_p, false);
        imagesavealpha($image_p, true);
        $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
        imagefilledrectangle($image_p, 0, 0, $width, $height, $transparent);
        imagecopyresampled($image_p, $img, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

        return $this->saveToFile($extension, $image_p, $newPath, $quality);
    }

    /**
     * Функция определения расширения файла
     *
     * @param $fileName string имя файла
     *
     * @return  $fileName string расширения файла
     */
    function getExtension ($fileName){
        $fileName = strtolower($fileName) ;
        $exts = explode(".", $fileName) ;
        $n = count($exts) - 1;
        return $exts[$n];
    }
}