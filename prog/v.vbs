dim x
'x = MsgBox("Hello World:Text",1+64+4096,"Hello World:Title")
'x = MsgBox("Hello World:Text",4161,"Hello World:Title")
'x = MsgBox("Hello World:Text", vbOKCancel+vbInformation+vbSystemModal, _
'           "Hello World:Title")
x = MsgBox("Hello World:Text", vbYesNoCancel+vbInformation,"Hello World:Title")
MsgBox "The result is " & x