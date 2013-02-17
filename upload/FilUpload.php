<?php
namespace app\model\upload;
use \Exception;

/**
 * Uploads a file to  a specific folder
 * @author Baagoe
 */
class FilUpload  extends \app\model\Model {
    private $mappe;
    private $filtyper;
    private $navn;
    private $maxsize;

    /**
     * @param string $mappe fx. "images/"
     * @return true
     */
    public function __construct($mappe = null) {
        if(!is_string($mappe)) return false;
        $this->mappe = $mappe;
        $this->navn = "unknown";
        $this->filtyper = array("jpeg", "jpg", "png");
        $this->maxsize = 0;
        return true;
    }

    /**
     * Returns folder
     * @return type
     */
    public function getMappe() {
        return $this->mappe;
    }


    /**
     * Upload file to specific folder
     * @param array $files
     * @return string
     * @throws Exception
     */
    public function upload($files, $setTimestamp = true) {
        if(!is_array($files) || $files['error'] == 1) return false;
        $filetype = explode("/", $files['type']);
        $filetype = $filetype[1];
        if(!$this->checkFiletype($filetype) || !$this->checkSize((int) $files['size'])) return false;

        $time = ($setTimestamp) ? time() : "";
        $filename = $this->mappe. $time. "_". $this->navn. ".". $filetype;

        if(move_uploaded_file($files['tmp_name'], $filename)) {
            return $filename;
        }

        throw new Exception('Fejl i upload!');
    }

    /**
     * Sets name of file uploaded
     * @param string $navn
     */
    public function setNavn($navn) {
        $this->navn = $navn;
    }

    /**
     * Sets filtype permitted using an array
     * @param array $filtyper
     */
    public function setFiltyper($filtyper) {
        if(!is_array($filtyper)) return false;
        $this->filtyper = $filtyper;
    }

    /**
     * Returns true if filetype is permitted
     * @param sting $filetype
     * @return boolean true/false
     */
    public function checkFiletype($filetype) {
        foreach($this->filtyper as $type) {
            if($filetype == $type) return true;
        }

        return false;
    }

    /**
     * Sets maxSize in kb
     * @param type $maxsize
     */
    public function setMaxsize($maxsize) {
        $this->maxsize = $maxsize;
    }

    /**
     * Checks the given size against maxsize in KB.
     * If maxsize is set to 0, the function will return true
     *
     * @param int $size
     * @return boolean true/false
     */
    public function checkSize($size) {
        if($this->maxsize == 0) return true;
        if(is_numeric($size) && $size <= ($this->maxsize *= 1024)) return true;
        return false;
    }

}

?>