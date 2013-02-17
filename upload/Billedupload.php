<?php
namespace app\model\upload;
use \Exception;
use \FilesystemIterator;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Uplaods pictures into a gallery
 *
 * @author Baagoe
 */

class Billedupload extends FilUpload {

    public function __construct($mappe = null) {
        if(!is_string($mappe)) return false;
        parent::__construct($mappe);
        parent::setFiltyper(array("jpeg", "jpg", "png"));
    }

    /**
     * Returns an array of all pictures uploaded in a specific folder
     * @return \FilesystemIterator|boolean
     */
    public function getPics() {
        try {
            $path = new FilesystemIterator($this->getMappe());
            $path = $path->getPath();
            $pictures = new FilesystemIterator($path);
        } catch(Exception $e) {
            echo $e->getMessage();
        }

        if(iterator_count($pictures) <= 0) return false;

        $out = array();
        foreach($pictures as $file) {
            $ext = $file->getFilename();
            $ext = explode(".", $ext);

            if($file->isDir() == true && ($ext[1] != "png" || $ext[1] != "jpg"))  continue;

            $name = $file->getBasename(".". $ext[1]);
            $path = $this->getMappe(). $file->getBasename();
            $out[] = array("name" => $name, "path" => $path);
        }

        return $out;
    }


    /**
     * Resize image
     *
     * @param int $width
     * @param int $height
     * @param int $picture
     * @return true/false
     */
    public function resizePic($thumbWidth, $picturePath) {
        if(!is_numeric($thumbWidth) || empty($picturePath)) return false;

        $picType = mime_content_type($picturePath);
        $picType = explode("/", $picType);
        $picType = $picType[1];
        $size = getimagesize($picturePath);

        $ratio = $thumbWidth/$size[0];
        $height = $size[1]*$ratio;
        $width = $thumbWidth;

        $thumbnail = imagecreatetruecolor($width, $height);
        if($picType == "jpeg" || $picType == "jpg") {
            $picture = imagecreatefromjpeg($picturePath);
        } elseif($picType == "png") {
            $picture = imagecreatefrompng($picturePath);
        } else {
            return false;
        }

        imagecopyresized($thumbnail, $picture, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
        imagejpeg($thumbnail, $picturePath);
        return true;
    }
}

?>