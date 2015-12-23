<?php

class Teamlab_Batch_FileSelecter
{
    private $_filelist;
    private $_filefilter_collection = array();

    /**
     * @param mixed $param
     */
    public function __construct($param) {
        if (!is_string($param) && !is_array($param)) {
            throw new Teamlab_Batch_Exception( 'パラメータが不正です。' );
        }

        $this->_filelist = (is_array($param)) ? $param : $this->makeFilelist($param);
    }

    /**
     * @param strings $param
     * @return mixed
     */
    private function makeFileList($param) {
        if (!file_exists($param) || empty($param)) {
            throw new Teamlab_Batch_Exception(sprintf("ファイルまたはディレクトリが存在しません。%s", $param));
        }

        $dir = opendir($param);
        if (!$dir) return null;

        $filelist = array();
        while (($file = readdir($dir)) !== false) {
            if ($file == "." || $file == "..") continue;

            $filelist[] = $file;
        }

        closedir($dir);

        return $filelist;
    }

    /**
     * @param strings $str
     * @return mixed
     */
    public function getFilelist($str = null) {
        if (is_null($str)) return $this->_filelist;

        return isset($this->_filefilter_collection[$str]) ? $this->_filefilter_collection[$str] : null;
    }

    /**
     * @param strings $str
     * @return array
     */
    public function getPrefixFilelist($str) {
        if (isset($this->_filefilter_collection[$str]) && !empty($this->_filefilter_collection[$str])) {
            return $this->_filefilter_collection[$str];
        }

        $filterlist = array();
        foreach ($this->_filelist as $key => $filepath) {
            $fileinfo = pathinfo($filepath);
            preg_match( '/^'. $str . '/', $fileinfo['basename'], $matches);
            if (!$matches) continue;

            $filterlist[$key] = $filepath;
        }
        
        if (empty($filterlist)) {
            throw new Teamlab_Batch_Exception(sprintf("指定の文字列から始まるファイル名が見つかりませんでした。%s", $str));
        }
        asort($filterlist);
        $this->setFileFilterCollection($str, $filterlist);

        return $filterlist;
    }

    /**
     * @param strings $param
     * @param array   $filelist
     * @return void
     */
    public function setFileFilterCollection($param, array $filelist) {
        $this->_filefileter_collection[$param] = $filelist;
    }

}