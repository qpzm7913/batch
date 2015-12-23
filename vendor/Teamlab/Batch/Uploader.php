<?php

class Teamlab_Batch_Uploader implements Teamlab_Batch_Uploader_Interface
{
    /** @var  Teamlab_Batch_Uploader_Transport_Interface */
    protected $_transport;

    public function setTransport(Teamlab_Batch_Transport_Interface $transport) {
        $this->_transport = $transport;
        return $this;
    }

    public function upload($filepath)
    {
        $this->_transport->setUploadFile($filepath);

        if ($this->_transport->upload()) {
            unlink($filepath);
        }
    }
}