@ECHO OFF
REM javac %1.java && java %*
SET clss=%1
SET ikvm="D:\download\ikvmbin-7.2.4630.5\ikvm-7.2.4630.5\bin\ikvm"
javac %clss% && java %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %clss:~0,-5% %2 %3 %4
REM javac %clss% && java -XX:+AggressiveOpts %clss:~0,-5% %2 %3 %4 
REM javac %clss% && ECHO ------------------ && java -XX:+AggressiveOpts %clss:~0,-5% %2 %3 %4 && ECHO ------------------ && %ikvm% %clss:~0,-5% %2 %3 %4 
REM javac -bootclasspath rt.jar %clss% && java %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %clss:~0,-5% %2 %3 %4
REM D:\download\jikes-1.22-1.windows\bin\jikes.exe -classpath rt.jar %clss% && java %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %clss:~0,-5% %2 %3 %4
REM javac -Xlint:deprecation %clss% && java %clss:~0,-5% %2 %3 %4
REM javac %clss% && php_javaClass.bat %clss:~0,-5% %2 %3 %4
