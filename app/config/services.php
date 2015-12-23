<?php

class MyFactory extends Teamlab_Di_Container_Factory_Abstract {

    public function __construct(Teamlab_Config $config) {
        $this->_config = $config;
    }

    public function buildConfig() {
        return $this->_config;
    }

    /** Hmv Dao */
    public function buildHmvDao() {
        
        return new Hmv_Dao_PdoBase(
            $this->container->get('config')->db->dsn,
            $this->container->get('config')->db->user,
            $this->container->get('config')->db->password,
            null
        );
    }

    /** ProcessManager */
    public function buildUnixProcessLockFile() {
        global $argv;
        $lockfile_name = str_replace(array('.', '\\', '/', ':'), '_',  implode('_', $argv));

        $options = $this->container->get('config')->processmanager;
        $manager = Teamlab_Batch_ProcessManager_UnixProcessLockfile::getInstance($options);
        $manager->setLockfileName($lockfile_name);
        return $manager;
    }

    /** ProcessManager */
    public function buildWindowsProcessLockFile() {
        global $argv;
        $lockfile_name = str_replace(array('.', '\\', '/', ':'), '_',  implode('_', $argv));

        $options = $this->container->get('config')->processmanager;
        $manager = Teamlab_Batch_ProcessManager_WindowsProcessLockfile::getInstance($options);
        $manager->setLockfileName($lockfile_name);
        return $manager;
    }

    public function buildProcessManager() {
        $logger = $this->container->get('logger');
        $processmanager_config = $this->container->get('config')->processmanager;
        $manager = $this->container->get($processmanager_config->manager);
        return new Teamlab_Batch_ProcessManager($manager, $logger);
    }

    /** Importers */
    public function buildImporterFromDb() {
        $config = $this->container->get('config');
        $dao = $this->container->getShared('hmvdao');
        $error_handler = $this->container->get('errorhandlerdb');
        return new Teamlab_Batch_Importer_Reader_Db($dao, $error_handler, $config);
    }

    public function buildImporterFromFile() {
        return new Teamlab_Batch_Importer_Reader_Csv();
    }

    public function buildImporter() {
        return new Teamlab_Batch_Importer();
    }

    /** Exporters */
    public function buildExportWriterCsv() {
        return new Teamlab_Batch_Exporter_Writer_Csv();
    }

    public function buildExporter() {
        return new Teamlab_Batch_Exporter();
    }

    /** Archiver */
    public function buildArchiver() {
        return new Teamlab_Batch_Archiver();
    }

    public function buildArchiveZipFormat() {
        return new Teamlab_Batch_Archiver_Format_Zip();
    }

    /** Uploader */
    public function buildUploader() {
        return new Teamlab_Batch_Uploader();
    }
    public function buildUploaderTransportS3() {
        return new Teamlab_Batch_Transport_S3($this->container->get('config')->s3);
    }
    
    /** Downloader */
    public function buildDownloader() {
        return new Teamlab_Batch_Downloader();
    }
    public function buildDownloaderTransportSftp() {
        $logger = $this->container->get('logger');
        return new Teamlab_Batch_Transport_SFTP($this->container->get('config')->sftp, $logger);
    }

    /** Logger */
    public function buildLogger() {
        Hmv_Log::$output_log_level = $this->container->get('config')->logger->output_log_level;
        Hmv_Log::$separate_log_flag = $this->container->get('config')->logger->separate_log_flag;
        Hmv_Log::$log_dir_path = $this->container->get('config')->logger->log_dir_path;
        return Hmv_Log::getLogger();
    }
    
}

$di = new Teamlab_Di_Container(new MyFactory($config));