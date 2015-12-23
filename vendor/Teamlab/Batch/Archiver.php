<?php

class Teamlab_Batch_Archiver implements Teamlab_Batch_Archiver_Interface
{
    /** @var  Teamlab_Batch_Archiver_Format_Interface */
    protected $_archiver;

    protected $_input_file;

    protected $_output_dir;
    protected $_output_filename;

    public function setArchiveFormat(Teamlab_Batch_Archiver_Format_Interface $archiver)
    {
        $this->_archiver = $archiver;
        return $this;
    }

    public function setInputFile($filepath)
    {
        $this->_input_file = $filepath;
        return $this;
    }

    public function setOutputDir($path)
    {
        $this->_output_dir = $path;
        return $this;
    }

    public function setOutputFilename($filename)
    {
        $this->_output_filename = $filename;
        return $this;
    }

    public function compress()
    {
        // 出力先ファイルのディレクトリがなければ作る
        $out_path_info = pathinfo($this->_output_dir);
        if (!file_exists($out_path_info['dirname'])) {
            if (!mkdir($out_path_info['dirname'], 0777, true)) {
                throw new Teamlab_Batch_Exception('圧縮ファイルの出力先ディレクトリの作成に失敗しました : '. $out_path_info);
            }
        }

        return $this->_archiver->compress($this->_input_file, $this->_output_filename, $this->_output_dir);
    }

    public function uncompress()
    {
        // 出力先ファイルのディレクトリがなければ作る
        $out_path_info = pathinfo($this->_output_dir);
        if (!file_exists($out_path_info['dirname'])) {
            if (!mkdir($out_path_info['dirname'], 0777, true)) {
                throw new Teamlab_Batch_Exception('圧縮ファイルの出力先ディレクトリの作成に失敗しました : '. $out_path_info);
            }
        }
        
        return $this->_archiver->uncompress($this->_input_file, $this->_output_dir);
    }


}