<?php

class Teamlab_Batch_Importer implements Teamlab_Batch_Importer_Interface
{
    /** @var  Teamlab_Batch_Importer_Reader_Interface */
    protected $_reader;

    public function __construct() {
    }

    public function setReader(Teamlab_Batch_Importer_Reader_Interface $reader) {
        $this->_reader = $reader;
        return $this;
    }

    public function import()
    {
        return $this->_reader->read();
    }
}