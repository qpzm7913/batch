<?php

class Teamlab_Batch_Transport_SFTP implements Teamlab_Batch_Transport_Interface
{
    private $_remotehost;
    private $_username;
    private $_password;

    protected $_upload_file;
    protected $_upload_dir;
    protected $_download_file;

    protected $_remote_path;
    protected $_local_path;

    protected $_hmv_file_transfer;

    protected $_logger;

    protected $_del_flg = true;

    public function __construct($config, $logger)
    {
        if($config instanceof Teamlab_Config) {
            $config = $config->toArray();
        }
        $this->_logger = $logger;

        $this->_remotehost = $config['remotehost'];
        $this->_username   = $config['username'];
        $this->_password   = $config['password'];

        $this->_hmv_file_transfer = new Hmv_FileTransfer(Hmv_FileTransfer::PROTOCOL_TYPE_SFTP);
    }

    public function setUploadFile($path) {
        $this->_upload_file = $path;
        return $this;
    }

    public function upload() {
    }

    public function setDownloadFile($path)
    {
        $this->_download_file = $path;
        return $this;
    }

    public function setRemotePath($path) {
        $this->_remote_path = $path;
        return $this;
    }

    public function setLocalPath($path) {
        $this->_local_path = $path;
        return $this;
    }

    public function setDeleteFlag($flag = true) {
        $this->_del_flg = $flag;
        return $this;
    }

    public function download() {

        // SFTP Server Connect
        $res_connect = $this->_hmv_file_transfer->connect($this->_remotehost, $this->_username, $this->_password, $this->_logger);
        if (!$res_connect) {
            throw new Teamlab_Batch_Exception(sprintf("SFTP接続に失敗しました。 サーバ情報： %s", $this->_remotehost));
        }
        // SFTP Directory Path
        $sftp_path = (is_array($this->_download_file) ) ? $this->_download_file : array($this->_download_file);

        foreach ($sftp_path as $file_path) {
            if(!file_exists($this->_local_path)) {
                mkdir($this->_local_path, 0777, true);
            }

            // download file
            $fileinfo = pathinfo($file_path);
            $res_download = $this->_hmv_file_transfer->getData($file_path, $this->_local_path . DIRECTORY_SEPARATOR . $fileinfo['basename'], $this->_logger);
            if (!$res_download) {
                throw new Teamlab_Batch_Exception(sprintf("ファイルのダウンロードに失敗しました。 サーバ情報：%s ファイル名：%s", $this->_remotehost, $file_path));
            }

            if ($this->_del_flg) {
                $this->_hmv_file_transfer->deleteData($file_path, $this->_logger);
            }
        }
    }

    public function getFilelist($path) {
        $ret = array();

        // SFTP Server Connect
        $res_connect = $this->_hmv_file_transfer->connect($this->_remotehost, $this->_username, $this->_password, $this->_logger);
        if (!$res_connect) {
            throw new Teamlab_Batch_Exception(sprintf("SFTP接続に失敗しました。 サーバ情報： %s", $this->_remotehost));
        }

        // SFTP FilePath List
        $contents = $this->_hmv_file_transfer->getContentsList($path, $this->_logger);

        // Check SFTP Files
        if (!is_array($contents)) {
            throw new Teamlab_Batch_Exception(sprintf("ファイルリスト取得に失敗しました。 ディレクトリパス： %s", $path));
        }

        foreach ($contents as $content) {
            $ret[] = $path . $content[1];
        }

        return $ret;
    }
}