<?php

class Teamlab_Batch_ProcessManager {
    protected $_manager;
    protected $_logger;

    public function __construct(Teamlab_Batch_ProcessManager_Interface $manager, Hmv_Log_Logger $logger) {
        $this->_manager = $manager;
        $this->_logger = $logger;
    }

    public function start() {
        if($this->_manager->lock() === false) {
            $this->_logger->error('重複起動のため実行を中断します');
            throw new Exception();
        }
    }

    public function end() {
        if(!$this->_manager->release()) {
            $this->_logger->error('ロックファイルのリリースに失敗');
            throw new Exception('');
        }
    }
}