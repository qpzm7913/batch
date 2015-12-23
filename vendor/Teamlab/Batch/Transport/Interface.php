<?php

interface Teamlab_Batch_Transport_Interface
{
    public function setUploadFile($path);
    public function upload();
    public function setDownloadFile($path);
    public function download();
    public function getFilelist($path);
}