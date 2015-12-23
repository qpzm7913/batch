<?php

class Teamlab_Batch_Task implements Teamlab_Batch_Task_Interface
{
    protected static $_di;

    public function __construct() {
    }

    public static function setDi(Teamlab_Di_Container $di) {
        self::$_di = $di;
    }

    /** @return Teamlab_Di_Container */
    public static function getDi() {
        return self::$_di;
    }

    public static function setUpBeforeClass() {
    }

    public function setUp() {
    }

    public function tearDown() {
    }

    public static function tearDownAfterClass() {
    }
}