<?php

class Teamlab_Batch_Archiver_Format_Zip implements Teamlab_Batch_Archiver_Format_Interface
{
    public function compress($input_file, $output_file, $output_dir)
    {
        // 出力ファイル名が未指定であれば元ファイル名を引き継ぎ、suffixのみ変更する
        if(empty($output_file)) {
            $from_path_info = pathinfo($input_file);
            $output_file = $from_path_info['filename'] . '.zip';
        }

        $from_path_info = pathinfo($input_file);
        $local_name = $from_path_info['basename'];

        $create_file = $output_dir . DIRECTORY_SEPARATOR . $output_file;

        $zip = new ZipArchive();
        $res = $zip->open($create_file, ZipArchive::CREATE);
        if ($res === true) {
            $zip->addFile($input_file, $local_name);
            $zip->close();
        }else{
            throw new Teamlab_Batch_Archiver_Format_Exception('ファイルの圧縮に失敗しました : '. $input_file);
        }
    }

    public function uncompress($path, $output_dir)
    {
        // TODO: Implement uncompress() method.
        $zip = new ZipArchive();
        $res = $zip->open($path);
        if ($res === true) {
            $zip->extractTo($output_dir);
            $zip->close();
        }       
    }
}
