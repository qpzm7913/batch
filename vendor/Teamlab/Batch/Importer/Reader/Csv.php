<?php

class Teamlab_Batch_Importer_Reader_Csv implements Teamlab_Batch_Importer_Reader_Interface
{
    protected $_file;

    public function __construct() {
    }

    public function setFile($path) {
        $this->_file = $path;
        return $this;
    }

    public function read()
    {
        $current_locale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $temp = tmpfile();

        $fileObject = new SplFileObject($this->_file);
        foreach ($fileObject as $data) {
            $data = mb_convert_encoding($data, 'UTF-8', 'sjis-win');            
            fwrite($temp, $data);
        }
        
        $meta = stream_get_meta_data($temp);

        $file = new SplFileObject($meta['uri']);
        $file->setFlags(SplFileObject::READ_CSV);

        return new Teamlab_Batch_Importer_ResultDataSet($file);
    }
}