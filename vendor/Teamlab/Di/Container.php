<?php

class Teamlab_Di_Container {
    protected $componentFactory;
    static protected $shared = array();

    public function __construct(Teamlab_Di_Container_Factory_Abstract $c) {
        $this->componentFactory = $c;
        $c->accept($this);
    }

    public function getShared($name) {
        $name = strtolower($name);
        if(isset(self::$shared[$name])) {
            return self::$shared[$name];
        }

        $object = $this->componentFactory->get($name);
        return self::$shared[$name] = $object;
    }

    public function get($name) {
        $name = strtolower($name);
        return $this->componentFactory->get($name);
    }
}