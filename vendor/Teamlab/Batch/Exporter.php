<?php

class Teamlab_Batch_Exporter implements Teamlab_Batch_Exporter_Interface
{
    /** @var  Teamlab_Batch_Exporter_Writer_Interface */
    protected $_writer;

    public function __construct() {
    }

    public function setWriter(Teamlab_Batch_Exporter_Writer_Interface $writer) {
        $this->_writer = $writer;
        return $this;
    }

    public function export($data) {
        return $this->_writer->write($data);
    }
    
    public function shutdown() {
        $this->_writer->close();
    }
}