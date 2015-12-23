<?php

class Teamlab_Batch_ErrorHandler_Db implements
    Teamlab_Batch_ErrorHandler_Behavior_Db_Connect,
    Teamlab_Batch_ErrorHandler_Behavior_Db_ResultSet,
    Teamlab_Batch_ErrorHandler_Behavior_Db_ResultSetEmpty
{

    public function _errorDbConnect()
    {
        // TODO: Implement _errorDbConnect() method.
        throw new Exception("DB接続に失敗しました。\r\n");
    }

    public function _errorDbResultSet()
    {
        // TODO: Implement _errorDbResultSet() method.
        throw new Exception("データの登録に失敗しました。\r\n");
    }

    public function _errorDbResultSetEmpty($sql_path = '')
    {
        // TODO: Implement _errorDbResultSetEmpty() method.
        throw new Exception("登録データがありません。\r\n". $sql_path);
    }
}