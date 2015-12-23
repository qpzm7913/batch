<?php

interface Teamlab_Batch_Uploader_Interface
{
    /** @return Teamlab_Batch_Uploader_Interface */
    public function setTransport(Teamlab_Batch_Transport_Interface $transport);
    public function upload($filepath);
}