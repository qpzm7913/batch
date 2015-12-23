<?php

class Teamlab_Batch_Exporter_Writer_Csv implements Teamlab_Batch_Exporter_Writer_Interface
{
    /** @var  Teamlab_Batch_Filter_Interface $_filter */
    protected $_filter;
    protected $_file_path;
    protected $_file_handle;

    public function __construct()
    {
    }

    public function setFilter(Teamlab_Batch_Filter_Interface $filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    public function setFilePath($file_path)
    {
        $this->_file_path = $file_path;
        return $this;
    }

    public function write($data)
    {
        if(!is_resource($this->_file_handle)) {
            $pathinfo = pathinfo($this->_file_path);

            if(!file_exists($pathinfo['dirname'])) {
                mkdir($pathinfo['dirname'], 0777, true);
            }

            if (!$this->_file_handle = fopen($this->_file_path, "w")) {
                throw new Teamlab_Batch_Exporter_Writer_Exception('ファイルが開けませんでした : '.$this->_file_path );
            }
        }

        if($this->_filter instanceof Teamlab_Batch_Filter_Interface) {
            $data = $this->replaceToFilter($data);
        }

        $data = $this->replaceToCsvFormat($data);
        if(fwrite($this->_file_handle, $data) === false) {
            throw new Teamlab_Batch_Exporter_Writer_Exception('ファイルに書き込めませんでした。 : '. $this->_file_path);
        }
    }
    
    public function close()
    {
        fclose($this->_file_handle);
    }
    
    private function replaceToFilter($data) {
        
        if (!is_array($data))
        {
            return $this->_filter->filter($data);
        }
        
        $res = array();       
        foreach($data as $key => $value) {
            $res[$key] = $this->_filter->filter($value);
        }
        
        return $res;
    }
    
    private function replaceToCsvFormat($data, $enclosure = '"', $delimiter = ',', $terminate = "\r\n") {
        $res = array();
        
        if (!is_array($data)) {
            $data = array($data);
        }
        
        foreach($data as $key => $value) {
            $res[$key] = $enclosure . str_replace($enclosure, str_repeat($enclosure, 2), $value) . $enclosure;
        } 
        
        return implode($delimiter, $res) . $terminate;
    }
}