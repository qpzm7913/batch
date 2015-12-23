<?php

class Teamlab_Batch_Importer_ResultDataSet implements IteratorAggregate
{
    private $_data;

    public function __construct($data) {
        $this->_data = $data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        if($this->_data instanceof PDOStatement) {
            return $this->_data;
        }

        if($this->_data instanceof SplFileObject) {
            return $this->_data;
        }

        return new ArrayIterator($this->_data);
    }
}