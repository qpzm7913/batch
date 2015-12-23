<?php

abstract class Teamlab_Di_Container_Factory_Abstract {
    /** @var  Teamlab_Di_Container $container */
    protected $container;

    public function get($name) {
        return $this->{'build' . $name}();
    }

    public function accept(Teamlab_Di_Container $c) {
        $this->container = $c;
    }
}