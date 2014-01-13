@ECHO OFF
REM javac %1.java && java %*
SET clss=%1
SET ikvm="D:\download\ikvmbin-7.2.4630.5\ikvm-7.2.4630.5\bin\ikvm"
SET ecj=java -jar ecj-4.3.1.jar -1.6 -classpath .;rt.jar
SET cp= -classpath .;rt.jar;lib/mysql-connector.jar

javac %clss% && java %cp% -DENV=DEV -DLOG="no log" %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %cp% -DENV=DEV -DLOG="no log" %clss:~0,-5% %2 %3 %4
REM %ecj% %clss% && java -DENV=DEV -DLOG="no log" %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat -DENV=DEV -DLOG="no log" %clss:~0,-5% %2 %3 %4
REM javac %clss% && java -XX:+AggressiveOpts %clss:~0,-5% %2 %3 %4 
REM javac %clss% && ECHO ------------------ && java -XX:+AggressiveOpts %clss:~0,-5% %2 %3 %4 && ECHO ------------------ && %ikvm% %clss:~0,-5% %2 %3 %4 
REM javac -bootclasspath rt.jar %clss% && java %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %clss:~0,-5% %2 %3 %4
REM D:\download\jikes-1.22-1.windows\bin\jikes.exe -classpath rt.jar %clss% && java %clss:~0,-5% %2 %3 %4 && echo ---------------- && php_javaClass.bat %clss:~0,-5% %2 %3 %4
REM javac -Xlint:deprecation %clss% && java %clss:~0,-5% %2 %3 %4
REM javac %clss% && php_javaClass.bat %clss:~0,-5% %2 %3 %4
