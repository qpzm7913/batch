<?php

interface Teamlab_Batch_Archiver_Format_Interface
{
    public function compress($input_file, $output_file, $output_dir);
    public function uncompress($path, $output_dir);
}