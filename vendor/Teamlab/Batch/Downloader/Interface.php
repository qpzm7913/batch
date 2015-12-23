<?php

interface Teamlab_Batch_Downloader_Interface
{
    public function setTransport(Teamlab_Batch_Transport_Interface $transport);
    public function download($filepath);
    public function getFilelist($path);
}