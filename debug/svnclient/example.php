<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by Sixdegrees                                 *
*   cesar@sixdegrees.com.br                                               *
*   "Working with freedom"                                                *
*   http://www.sixdegrees.com.br                                          *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/

require("phpsvnclient.php");

/**
 *  Creating new phpSVNClient Object.
 */
$svn  = new phpsvnclient;
/**
 *  Repository URL
 */
$svn->setRepository("http://www.ditech.com.br/svn/DITECH/");
//var_dump($svn);

$svn->setAuth('jean', 'senha');

/**
 *  Get Files from "/trunk/phpajax/" directory from the last repository version 
 */
//$files_now = $svn->getDirectoryFiles("/");
//echo '<pre>';var_dump($files_now);

$logs = $svn->getRepositoryLogs($svn->getVersion() -10);
echo '<pre>';var_dump($logs);
exit;


/**
 *  Get Files from "/trunk/phpajax/"  directory from version 7 
 */
$files_7   = $svn->getDirectoryFiles("/trunk/phpajax/",7);

/**
 *  Get "/trunk/phpajax/phpajax.php"  contents from the last repository version   
 */
$phpajax_now = $svn->getFile("/trunk/phpajax/phpajax.php");

/**
 *  Get "/trunk/phpajax/phpajax.php"  contents from version 7 
 */
$phpajax_7   = $svn->getFile("/trunk/phpajax/phpajax.php",7);


/**
 *  Get all logs of /trunk/phpajax/phpajax.org from  between 2 version until the last
 */
$logs = $svn->getRepositoryLogs(2);

/**
 *  Get all logs of /trunk/phpajax/phpajax.org from  between 2 version until 5 version.
 */
$logs = $svn->getRepositoryLogs(2,5);


?>
