<?php

interface Teamlab_Batch_Archiver_Interface
{
    /** @return Teamlab_Batch_Archiver_Interface */
    public function setArchiveFormat(Teamlab_Batch_Archiver_Format_Interface $format);

    /** @return Teamlab_Batch_Archiver_Interface */
    public function setInputFile($filepath);

    /** @return Teamlab_Batch_Archiver_Interface */
    public function setOutputDir($path);

    /** @return Teamlab_Batch_Archiver_Interface */
    public function setOutputFilename($filename);

    public function compress();
    public function uncompress();
}