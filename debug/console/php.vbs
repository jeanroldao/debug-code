Set WshShell = WScript.CreateObject("WScript.Shell")
WshShell.run "%comspec% /c dotnet.bat",0
Set WshShell = Nothing
