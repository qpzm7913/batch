@echo off

rem
rem PHPによるバッチの実行
rem 第１引数：バッチ名
rem 第２引数：環境変数
rem 第３引数：強制実行

rem このバッチが存在するフォルダをカレントに
pushd %0\..
cls

rem バッチ実行時の環境変数を設定
if not '%2'=='' set BATCH_EXEC_ENVIRONMENT=%2

rem 第１引数未指定の場合
if '%1'=='' echo 引数にバッチ名を指定してください。
if '%1'=='' goto quit

rem 環境変数が'production'の場合には実行を確認
if not '%BATCH_EXEC_ENVIRONMENT%'=='production' goto exec

rem 第３引数が指定されている場合には強制的に実行する
if '%3'=='X' goto exec

echo 本番環境の設定となっていますが実行しますか？ (Y/N)
set /p userkey=
if /i '%userkey%'=='y' goto exec
if /i '%userkey%'=='yes' goto exec
if /i '%userkey%'=='n' goto quit
if /i '%userkey%'=='no' goto quit
goto quit

:exec

rem index.phpを実行
php index.php --class %1

:quit

