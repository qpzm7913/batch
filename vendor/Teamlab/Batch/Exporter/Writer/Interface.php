<?php

interface Teamlab_Batch_Exporter_Writer_Interface
{
    public function setFilter(Teamlab_Batch_Filter_Interface $filter);

    public function write($data);
    public function close();
}