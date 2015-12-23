<?php

class Teamlab_Batch_ProcessManager_WindowsProcessLockfile extends Teamlab_Batch_ProcessManager_Abstract
{
    private static $_pid;

    private static function isRunning() {
        // 同一バッチのプロセスが実行中か確認する
        exec(
            'tasklist /svc /fi "PID eq ' . self::$_pid .'" | findstr ' . self::$_pid,
            $process_check_result);

        if (empty($process_check_result)) {
            // バッチ実行中の場合は処理を終了する
            return false;
        }
        return true;
    }

    public function lock() {
        if(!file_exists($this->_lockfile_dir)) {
            mkdir($this->_lockfile_dir, 0777, true);
        }
        if(file_exists($this->getLockfilePath())) {
            // return false; // ロックされている
            self::$_pid = file_get_contents($this->getLockfilePath());
            if(self::isRunning()) {
                return false;
            }else{
                // 前回ロックを作ったプロセスがロックを残したまま死亡した場合
                // ここでなにもしなければ無視して新規に今回実行時のプロセスIDでロックを上書き
            }
        }

        self::$_pid = getmypid(); // 現在実行中の自分自身のPIDを取得する
        $ret = @file_put_contents($this->getLockfilePath(), self::$_pid); // PIDを書き込み
        if($ret === false) {
            throw new Exception('ロックファイルが生成できませんでした');
        }
        return self::$_pid;
    }

    public function release() {
        if (!file_exists($this->getLockfilePath())) {
            return true; // そもそもロックファイルが無かった場合
        }
        return unlink($this->getLockfilePath());
    }
}