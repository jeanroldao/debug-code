<?php
$full_assembly_string = 'System.Windows.Forms, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089';
$namespace = 'System.Windows.Forms';

// Main Form
$form = new DOTNET($full_assembly_string, $namespace.'.Form');
$form->Width = 450;
$form->Height = 550;
$form->Show();

// Textbox 1
$txt1 = new DOTNET($full_assembly_string, $namespace.'.TextBox');
$txt1->Name = "IP";
$txt1->Height = 400;
$txt1->Width = 400;
$txt1->Top = 40;
$txt1->Left = 5;
$txt1->Parent = $form;

// Textbox 1
$chec = new DOTNET($full_assembly_string, $namespace.'.CheckBox');
$chec->Name = "CK";
$chec->Top = 40;
$chec->Left = 405;
$chec->Parent = $form;

// Textbox 2
$txt2 = new DOTNET($full_assembly_string, $namespace.'.TextBox');
$txt2->Height = 300;
$txt2->Width = 400;
$txt2->Top = 80;
$txt2->Left = 5;
$txt2->Multiline = True;
$txt2->ScrollBars = 3;
$txt2->Parent = $form;

// Button 1
$btn1 = new DOTNET($full_assembly_string, $namespace.'.Button');
$btn1->Height = 25;
$btn1->Width = 75;
$btn1->Text = "Backtrace";
$btn1->Top = 5;
$btn1->Left = 5;
$btn1->Parent = $form;

// Hack message/event capturing loop
while(true) {
    com_message_pump(1);
    // Check if 'Capture' property of Button 1 changed in lieu of actual click event
    if ((bool) $btn1->Capture) {
        $txt2->Text .= "BACKTRACING ".$txt1->Name." ".$txt1->Text."!!".PHP_EOL;
        if ((bool) $chec->Checked) {
          $txt2->Enabled = !$txt2->Enabled;
        }
        $btn1->Capture = false; // force release of the button
    }
}
?>