<?php

class Tasks_ProductMasterS3Upload extends Teamlab_Batch_Task {

    private $_work_directory;
    private $_temp_directory;
    private $_sftp_directory;
    private $_file_prefix;

    public static function setUpBeforeClass() {
        $process_manager = self::getDi()->get('processmanager');
        $process_manager->start();
    }

    public function setUp() {
        echo 'setup'. PHP_EOL;
    }

    public function taskInit() {
        echo 'initialize' . PHP_EOL;

        // Application Work Directory
        $task_directory = explode('_', get_class());
        $work_directory = self::getDi()->get('config')->app->work_dir;
        $this->_work_directory = $work_directory . DIRECTORY_SEPARATOR . date("Ymd") . DIRECTORY_SEPARATOR . $task_directory[1];
        $this->_temp_directory = $work_directory . DIRECTORY_SEPARATOR . 'tmp';
        
        // Sftp Server home Directory
        $this->_sftp_directory = self::getDi()->get('config')->remote->sftp_dir;

        // File Prefix
        $this->_file_prefix = 'goods_';
    }

    public function taskDownload() {

        echo 'download' . PHP_EOL;

        /** @var Teamlab_Batch_Downloader_Transport_Interface $reader */
        $transport = self::getDi()->get('downloadertransportsftp');
        $transport->setLocalPath($this->_work_directory);
        $transport->setRemotePath($this->_sftp_directory);

        /** @var Teamlab_Batch_Downloader_Downloader_Interface $reader */
        $downloader = self::getDi()->get('downloader');
        $downloader->setTransport($transport);

        // Sftp Server Filelist
        $filelist = $downloader->getFilelist($this->_sftp_directory);
        $fileselecter = new Teamlab_Batch_FileSelecter($filelist);
        $filterlist = $fileselecter->getPrefixFilelist($this->_file_prefix);
        foreach ($filterlist as $filepath) {
            // download Sftp Server
            $downloader->download($filepath);
        }
    }

    public function taskCopyFileToTempdir() {
        echo 'Copy File to Tempdir' . PHP_EOL;
        
        $archiver = self::getDi()->get('archiver');
        $archiver->setArchiveFormat(self::getDi()->get('archivezipformat'));
        
        $fileselecter = new Teamlab_Batch_FileSelecter($this->_work_directory);
        $filterlist = $fileselecter->getPrefixFilelist($this->_file_prefix);
        foreach ($filterlist as $filename) {            
            $from_filepath = $this->_work_directory . DIRECTORY_SEPARATOR . $filename;
            $to_filepath   = $this->_temp_directory . DIRECTORY_SEPARATOR . $filename;
           
            // copy temp_directory;
            copy($from_filepath, $to_filepath);
            
            // csv filepath
            $pathinfo = pathinfo($filename);
            $csv_filepath = $this->_temp_directory . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.csv';
            
            // zip file uncompress
            $archiver->setInputFile($to_filepath);
            $archiver->setOutputDir($this->_temp_directory);
            $archiver->setOutputFilename($csv_filepath);
            $archiver->uncompress();

            unlink($to_filepath);            
        }                
    }

    public function taskUpload() {
        echo 'upload' . PHP_EOL;

        /** @var Teamlab_Batch_Downloader_Transport_Interface $reader */
        $transport = self::getDi()->get('uploadertransports3');

        /** @var Teamlab_Batch_Uploader_Interface $uploader */
        $uploader = self::getDi()->get('uploader');
        $uploader->setTransport($transport);

        $fileselecter = new Teamlab_Batch_FileSelecter($this->_work_directory);
        $filterlist = $fileselecter->getPrefixFilelist($this->_file_prefix);
        foreach ($filterlist as $filepath) {
            // upload Sftp Server
            $uploader->upload($this->_work_directory . DIRECTORY_SEPARATOR . $filepath);
        }
    }

    public function tearDown() {
        echo 'teardown'. PHP_EOL;
    }

    public static function tearDownAfterClass() {
        $process_manager = self::getDi()->get('processmanager');
        $process_manager->end();
    }
}