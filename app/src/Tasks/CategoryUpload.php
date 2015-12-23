<?php

class Tasks_CategoryUpload extends Teamlab_Batch_Task {

    private $_work_directory;
    private $_sql_directory;
    private $_file_prefix;
    private $_importer;

    public static function setUpBeforeClass() {
        $process_manager = self::getDi()->get('processmanager');
        $process_manager->start();
    }

    public function setUp() {
        echo 'setup'. PHP_EOL;
    }

    public function taskInit() {
        echo 'initialize' . PHP_EOL;

        $task_directory = explode('_', get_class());
        $work_directory = self::getDi()->get('config')->app->work_dir;
        $this->_work_directory = $work_directory . DIRECTORY_SEPARATOR . date("Ymd") . DIRECTORY_SEPARATOR . $task_directory[1];
        $this->_sql_directory  = self::getDi()->get('config')->app->sql_dir . $task_directory[1];
        $this->_file_prefix    = 'category_';

        /** @var Teamlab_Batch_Importer_Interface $importer */
        $this->_importer = self::getDi()->get('importer');
    }

    public function taskImport() {
        echo 'import' . PHP_EOL;

        /** @var Teamlab_Batch_Importer_Reader_Interface $reader */
        $reader = self::getDi()->get('importerfromdb');

        // set sqlfile
        $reader->setSql($this->_sql_directory . DIRECTORY_SEPARATOR . 'select_category.sql');

        /** @var Teamlab_Batch_Importer_Interface $importer */
        $importer = self::getDi()->get('importer');
        $importer->setReader($reader);

        $this->_import_data = $importer->import();
    }

    public function taskExport() {
        echo 'export' . PHP_EOL;

        $filename = $this->_file_prefix . date('Ymd_His') . '.csv';

        $filters = new Teamlab_Batch_Filter();
        $filters->addFilter(new Teamlab_Batch_Filter_ReplaceConvertEncoding());

        /** @var Teamlab_Batch_Exporter_Writer_Interface $writer */
        $writer = self::getDi()->get('exportwritercsv');
        $writer->setFilter($filters);
        $writer->setFilePath($this->_work_directory . DIRECTORY_SEPARATOR . $filename);

        /** @var Teamlab_Batch_Exporter_Interface $exporter */
        $exporter = self::getDi()->get('exporter');
        $exporter->setWriter($writer);

        foreach ($this->_import_data as $row) {
            $exporter->export($row);
        }

        $exporter->shutdown();
    }

    public function taskArchive() {
        echo 'archive' . PHP_EOL;

        /** @var Teamlab_Batch_Archiver_Format_Interface */
        $archiver = self::getDi()->get('archiver');
        $archiver->setArchiveFormat(self::getDi()->get('archivezipformat'));

        $fileselecter = new Teamlab_Batch_FileSelecter($this->_work_directory);
        $filterlist = $fileselecter->getPrefixFilelist($this->_file_prefix);
        foreach ($filterlist as $filename) {
            $fileinfo = pathinfo($filename);
            $zip_file = $fileinfo['filename'] . '.zip';

            $archiver->setInputFile($this->_work_directory . DIRECTORY_SEPARATOR . $filename);
            $archiver->setOutputDir($this->_work_directory);
            $archiver->setOutputFilename($zip_file);
            $archiver->compress();

            unlink($this->_work_directory . DIRECTORY_SEPARATOR . $filename);
        }
    }

    public function taskUplod() {
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