<?php

class Teamlab_Batch_Downloader implements Teamlab_Batch_Downloader_Interface
{
    /** @var  Teamlab_Batch_Downloader_Transport_Interface */
    protected $_transport;

    public function setTransport(Teamlab_Batch_Transport_Interface $transport)
    {
        $this->_transport = $transport;
        return $this;
    }

    public function download($filepath)
    {
        $this->_transport->setDownloadFile($filepath);

        if ($this->_transport->download()) {
            if ( !file_exists($filepath) ) {
                throw new Exception( sprintf("取得先のファイルが存在しません。 %s", $filepath) );
            }
        }
    }

    public function getFilelist($path)
    {
        return $this->_transport->getFilelist($path);
    }
}