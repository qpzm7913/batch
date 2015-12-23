<?php

class Teamlab_Batch_Task_Runner {
    protected $_di;
    public $_options = array();

    public function __construct($depInjector) {
        $this->_di = $depInjector;
        $this->_options['class'] = null;
        $this->_options['action'] = array();
    }

    public function run() {
        $this->handleArguments();

        if($this->_options['class'] === null) {
            return;
        }
        if(empty($this->_options['action'])) {
            return;
        }

        /** @var Teamlab_Batch_Task $class */
        $class = new $this->_options['class'];
        if(!($class instanceof Teamlab_Batch_Task)) {
            throw new Exception('バッチタスククラスじゃない');
        }

        Teamlab_Batch_Task::setDi($this->_di);
        call_user_func(array($this->_options['class'], 'setUpBeforeClass'));

        /** @var Teamlab_Batch_Task $instance */
        $instance = new $class();
        foreach($this->_options['action'] as $action) {
            $instance->setUp();
            $instance->$action();
            $instance->tearDown();
        }

        call_user_func(array($this->_options['class'], 'tearDownAfterClass'));
    }

    protected function _optionNormalize($options) {
        $ret = $options;
        if(!isset($options['action'])) {
            $ret['action'] = array();
            return $ret;
        }

        // 単一値の場合stringで入ってくるので、arrayでくるみなおす。
        if(is_string($options['action'])) {
            $ret['action'] = array();
            $ret['action'][] = $options['action'];
        }
        return $ret;
    }
    
    protected function _getopt ()
    {
        global $argv;
        $options = array();
        
        $opt_flag = false;
        foreach ($argv as $param) {                       
            if ($opt_flag) {
                $options['class'] = $param;
                $opt_flag = false;
            }
            
            if (strpos($param, "--class") !== false) {
                $opt_flag = true;
            }
        }
       
        $opt_flag = false;       
        foreach($argv as $param)
        {
            if ($opt_flag) {
                if (!isset($options['action'])) {
                    $options['action'] = array();
                }
                
                $options['action'][] = $param;
                $opt_flag = false;
                continue;
            }
            
            if (strpos($param, "--action") !== false) {
                $opt_flag = true;
            }
        }
        
        return $options;
    }
    
    protected function handleArguments()
    {
        $shortopts = '';
        $longopts = array(
            'class:',
            'action:',
        );
        
        $options = (!function_exists('getopt')) 
            ? $this->_getopt()
            : $options = getopt($shortopts, $longopts);      
             
        $options = $this->_optionNormalize($options);

        if(!isset($options['class'])) {
            throw new Exception('class name is require.');
        }

        $this->_options['class'] = 'Tasks_' . $options['class'];

        if(empty($options['action'])) {
            // actionの指定がない場合
            $ref_class = new ReflectionClass($this->_options['class']);
            foreach($ref_class->getMethods() as $methods){
                if(strpos($methods->name, 'task', 0) !== 0) continue; // taskから始まらないメソッドはskip
                $this->_options['action'][] = $methods->name; // taskメソッドを全て実行対象とする
            }
        } else {
            // 指定があればそれらのみを実行対象とする
            foreach($options['action'] as $action) {
                $this->_options['action'][] = 'task' . ucfirst($action);
            }
        }
    }

    public function getOptions()
    {
        return $this->_options['class'];
    }
}