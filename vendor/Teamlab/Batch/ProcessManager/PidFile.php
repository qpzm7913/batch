<?php

class Teamlab_Batch_ProcessManager_PidFile implements Teamlab_Batch_ProcessManager_Interface
{
    protected $_pid_file_path;
    protected $_logger;

    public function __construct($pid_file_path, Hmv_Log $logger) {
        $this->_pid_file_path = $pid_file_path;
        $this->_logger = $logger;
    }

    public function isRunnable() {
        // PID ファイルの存在チェック
        if (file_exists($this->_pid_file_path)) {
            $running_pid = file_get_contents($this->_pid_file_path);

            $logger = $this->_logger;
            $logger::debug("PIDファイルの内容: " . $running_pid);

            // 同一バッチのプロセスが実行中か確認する
            exec(
                "tasklist /svc /fi \"PID eq $running_pid\" | findstr $running_pid",
                $process_check_result);

            if (!empty($process_check_result)) {
                // バッチ実行中の場合は処理を終了する
                return false;
            } else {
                // PIDファイルが存在するが、実際は実行されている
                // プロセスが存在しなければ実行してよし
                return true;
            }
        } else {
            // PID ファイルが存在しなければ実行してよし
            return true;
        }
    }

    public function lock() {
        // 現在実行中の自分自身のPIDを取得する
        $pid = getmypid();

        $logger = $this->_logger;
        $logger::debug('PID: ' . $pid);

        // PIDを書き込み
        file_put_contents($this->_pid_file_path, $pid);
    }

    public function release() {

    }
}