<?php

class Teamlab_Batch_Transport_S3 implements Teamlab_Batch_Transport_Interface
{
    private $_access_key;
    private $_secret_key;
    private $_bucket_name;

    protected $_upload_file;
    protected $_upload_dir;

    protected $_donwload_file;
    protected $_download_dir;

    public function __construct($config)
    {
        if($config instanceof Teamlab_Config) {
            $config = $config->toArray();
        }

        $this->_access_key  = $config['aws_access_key'];
        $this->_secret_key  = $config['aws_secret_key'];
        $this->_bucket_name = $config['aws_bucket_name'];
    }

    public function setUploadFile($path) {
        $this->_upload_file = $path;
        return $this;
    }

    public function setUploadDir($path) {
        $this->_upload_dir = $path;
        return $this;
    }

    public function upload()
    {
        if (!empty($this->_upload_dir)) {
            $this->_bucket_name .= $this->_bucket_name . '/' . $this->_upload_dir;
        }

        // アマゾンS3のインスタンス生成
        $s3 = new S3($this->_access_key, $this->_secret_key);
        if (!$s3) {
            throw new Teamlab_Batch_Exception(sprintf("AWS接続に失敗しました。： %s", $this->_bucket_name));
        }

        // ファイルアップロード
        if ($s3->putObjectFile($this->_upload_file, $this->_bucket_name, baseName($this->_upload_file), S3::ACL_PUBLIC_READ)) {
            return true;
        } else {
            throw new Teamlab_Batch_Transport_Exception(sprintf("ファイルのアップロードに失敗しました。サーバ情報：%s ファイル名：%s", $this->_bucket_name, baseName($this->_upload_file)));
        }
    }

    public function setDownloadFile($path) {
        $this->_donwload_file = $path;
        return $this;
    }

    public function setDownloadPath($path) {
        $this->_download_dir = $path;
        return $this;
    }

    public function download() {

    }

    public function getFilelist($path) {

    }
}