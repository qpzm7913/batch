<?php

class Teamlab_Batch_Task_ErrorHandler_File implements
    Teamlab_Batch_ErrorHandler_Behavior_File_Empty,
    Teamlab_Batch_ErrorHandler_Behavior_File_NotFound
{

    public function _errorFileEmpty()
    {
        // TODO: Implement _errorFileEmpty() method.
        throw new Exception('ファイルが空です。');
    }

    public function _errorFileNotFound()
    {
        // TODO: Implement _errorFileNotFound() method.
        throw new Exception('ファイルが見つかりません。');
    }
}