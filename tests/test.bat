cls
@echo off
:: c:\\appserv\php5.5\php.exe ./dump-create.php

call test_configure.bat

call test_view.bat

call test_dispatcher.bat

call test_staticobservable.bat

call test_controller.bat

call test_Models.bat

:: call test_FileStorage.bat

:: c:\\appserv\php5.5\php.exe ./dump-commit.php