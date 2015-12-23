<?php

interface Teamlab_Batch_Importer_Interface {
    public function setReader(Teamlab_Batch_Importer_Reader_Interface $reader);
    public function import();
}