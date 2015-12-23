<?php

interface Teamlab_Batch_ProcessManager_Interface
{
    public static function getInstance($config);

    public function lock();
    public function release();
}