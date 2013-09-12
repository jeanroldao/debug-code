@ECHO OFF
SET clss=%1
C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc /optimize /nowin32manifest /unsafe %clss% && %clss:~0,-2%exe