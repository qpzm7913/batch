<?php

class Teamlab_Batch_Importer_Reader_Db implements Teamlab_Batch_Importer_Reader_Interface
{
    protected $_dao;
    protected $_sql = '';
    protected $_prepared_values = array();
    protected $_is_write_log = false;

    protected $_error_handler;

    public function __construct(Hmv_Dao_PdoBase $dao, Teamlab_Batch_ErrorHandler_Db $handler, Teamlab_Config $config) {
        $this->_dao = $dao;
        $this->_error_handler = $handler;
    }

    public function setSql($sql) {
        $this->_sql = $sql;
        return $this;
    }

    public function setPreparedValues(array $values) {
        $this->_prepared_values = $values;
        return $this;
    }

    public function setWriteLog($is_write) {
        $this->_is_write_log = $is_write;
        return $this;
    }


    public function read()
    {
        if(!file_exists($this->_sql)) {
            $stmt = $this->_readFromQueryString();
        } else {
            $stmt = $this->_readFromSqlfile();
        }
        return new Teamlab_Batch_Importer_ResultDataSet($stmt);
    }

    protected function _readFromSqlfile() {
        $stmt = $this->_dao->getSth($this->_sql, $this->_prepared_values, $this->_is_write_log);
        $rows = $stmt->fetchAll();
        if (empty($rows))
        {
            throw new Teamlab_Batch_Importer_Reader_Exception("検索結果を取得できませんでした。");
        }
        
        return $rows;
    }

    protected function _readFromQueryString() {
        $result_set = $this->_dao->query($this->_sql, $this->_is_write_log);
        if(empty($result_set)) {
            $this->_error_handler->_errorDbResultSetEmpty($this->_sql);
        }
        return $result_set;
    }
}