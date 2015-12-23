<?php

class Teamlab_Config {
    protected $_data;

    public function __construct(array $array) {
        $this->_data = array();
        foreach($array as $k => $v) {
            if(is_array($v)) {
                $this->_data[$k] = new self($v);
            }else{
                $this->_data[$k] = $v;
            }
        }
    }

    public function get($name, $default = null){
        $result = $default;
        if(array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        }
        return $result;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function toArray()
    {
        $array = array();
        $data = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof Teamlab_Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }
}