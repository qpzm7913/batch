<?php

class Teamlab_Batch_ProcessManager_UnixProcessLockfile extends Teamlab_Batch_ProcessManager_Abstract
{
    private static $_pid;

    private static function isRunning() {
        $pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
        if(in_array(self::$_pid, $pids)) return true; // プロセス内に$pidがいれば実行中
        return false;
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