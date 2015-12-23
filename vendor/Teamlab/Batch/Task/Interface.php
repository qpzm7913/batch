<?php

interface Teamlab_Batch_Task_Interface
{
    public static function setDi(Teamlab_Di_Container $di);
    public static function getDi();
}