<?php

interface Teamlab_Batch_Exporter_Interface
{
    public function setWriter(Teamlab_Batch_Exporter_Writer_Interface $writer);
    public function export($data);
    public function shutdown();
}