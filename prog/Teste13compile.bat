@echo off
gcc -Wl,--add-stdcall-alias -I"C:\Program Files\Java\jdk1.7.0_21\include" -I"C:\Program Files\Java\jdk1.7.0_21\include\win32" -shared -o Teste13native.dll Teste13.c