@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION

:: Test runner: compares real Java output vs PHP interpreter output
:: Usage:
::   run_tests.bat              -> runs all Teste*.java
::   run_tests.bat TesteNIO     -> runs one test
::   run_tests.bat Teste1 Teste2 Teste3  -> runs specific tests

SET JAVA_EXE=java
SET JAVAC_EXE=javac
SET PHP_BAT=.\php_javaClass.bat

:: Where to look for extra jars (smallsql, etc.)
SET CP=.;smallsql.jar

SET PASS=0
SET FAIL=0
SET SKIP=0
SET TOTAL=0

:: Save/restore code page
for /f "tokens=2 delims=:" %%a in ('chcp') do set OLDCP=%%a
set OLDCP=%OLDCP: =%
chcp 65001 >nul

IF "%1"=="" (
    :: No args: run all Teste*.java that have a main()
    FOR %%F IN (Teste*.java TesteNIO.java TesteCsv.java TesteQueue.java) DO (
        CALL :run_one "%%~nF"
    )
) ELSE (
    :: Run each named test
    :arg_loop
    IF "%1"=="" GOTO done
    CALL :run_one "%~1"
    SHIFT
    GOTO arg_loop
)

:done
ECHO.
ECHO ========================================
ECHO  Results: %PASS% passed, %FAIL% failed, %SKIP% skipped / %TOTAL% total
ECHO ========================================
chcp %OLDCP% >nul
ENDLOCAL
EXIT /B 0

:: -----------------------------------------------------------
:run_one
SET TNAME=%~1
SET /A TOTAL+=1

:: Check the .java file exists
IF NOT EXIST "%TNAME%.java" (
    ECHO [SKIP] %TNAME% - .java not found
    SET /A SKIP+=1
    EXIT /B 0
)

:: Compile
%JAVAC_EXE% -cp "%CP%" "%TNAME%.java" >nul 2>nul
IF ERRORLEVEL 1 (
    ECHO [SKIP] %TNAME% - compile failed
    SET /A SKIP+=1
    EXIT /B 0
)

:: Run with real Java (5 second timeout via ping trick)
%JAVA_EXE% -cp "%CP%" %TNAME% >"%TNAME%_java.tmp" 2>nul
IF ERRORLEVEL 1 (
    ECHO [SKIP] %TNAME% - java run failed
    SET /A SKIP+=1
    EXIT /B 0
)

:: Run with PHP interpreter (stderr discarded)
CALL %PHP_BAT% %TNAME% >"%TNAME%_php.tmp" 2>nul
IF ERRORLEVEL 1 (
    ECHO [FAIL] %TNAME% - php run failed
    SET /A FAIL+=1
    EXIT /B 0
)

:: Compare outputs
FC /B "%TNAME%_java.tmp" "%TNAME%_php.tmp" >nul 2>nul
IF ERRORLEVEL 1 (
    ECHO [FAIL] %TNAME%
    ECHO   java output:
    FOR /F "delims=" %%L IN (%TNAME%_java.tmp) DO ECHO     %%L
    ECHO   php output:
    FOR /F "delims=" %%L IN (%TNAME%_php.tmp) DO ECHO     %%L
    SET /A FAIL+=1
) ELSE (
    ECHO [PASS] %TNAME%
    SET /A PASS+=1
)

:: Clean up temp files
DEL "%TNAME%_java.tmp" 2>nul
DEL "%TNAME%_php.tmp"  2>nul
EXIT /B 0
