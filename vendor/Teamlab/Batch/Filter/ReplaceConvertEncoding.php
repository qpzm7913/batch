<?php

class Teamlab_Batch_Filter_ReplaceConvertEncoding implements Teamlab_Batch_Filter_Interface
{
    public function filter($value)
    {
        mb_language('Japanese');

        return mb_convert_encoding($value, 'UTF-8', 'sjis-win');
    }
}