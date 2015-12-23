<?php

abstract class Teamlab_Batch_ProcessManager_Abstract implements Teamlab_Batch_ProcessManager_Interface
{
    protected $_lockfile_dir = './'; //default value
    protected $_lockfile_name = 'batch'; //default value
    protected $_lockfile_suffix = '.lock'; //default value

    /** @return self */
    public static function getInstance($config) {
        static $instances = array();
        $called_class_name = get_called_class();
        if (!isset($instances[$called_class_name])) {
            $instances[$called_class_name] = new $called_class_name($config);
        }
        return $instances[$called_class_name];
    }

    protected function __construct($config) {
        if($config instanceof Teamlab_Config) {
            $config = $config->toArray();
        }

        if(!is_array($config) || empty($config)) {
            throw new Exception('Configuratin must be an array.');
        }

        if(isset($config['lockfile_dir'])) {
            $this->_lockfile_dir = $config['lockfile_dir'];
        }
        if(isset($config['lockfile_name'])) {
            $this->_lockfile_name = $config['lockfile_name'];
        }
        if(isset($config['lockfile_suffix'])) {
            $this->_lockfile_suffix = $config['lockfile_suffix'];
        }
    }

    private function __clone() {}  // singleton patternなのでclone演算子を潰す
    private function __wakeup() {} // singleton patternなのでunserializeを潰す

    public function setLockFileDir($lockfile_dir) {
        $this->_lockfile_dir = $lockfile_dir;
        return $this;
    }

    public function setLockFileName($lockfile_name) {
        $this->_lockfile_name = $lockfile_name;
        return $this;
    }

    public function setLockfileSuffix($lockfile_suffix) {
        $this->_lockfile_suffix = $lockfile_suffix;
        return $this;
    }

    protected function getLockfilePath() {
        return $this->_lockfile_dir . DIRECTORY_SEPARATOR . $this->_lockfile_name . $this->_lockfile_suffix;
    }
}