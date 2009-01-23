<?php
/**
* @version $Id: fb_helpers.php 462 2007-12-10 00:05:53Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

function fbJsEscape($msg)
{
    // escape javascript quotes and backslashes, newlines, etc.
    static $convertions = array
    (
        '\\' => '\\\\',
        "'" => "\\'",
        '"' => '\\"',
        "\r" => '\\r',
        "\n" => '\\n',
        '</' => '<\/'
    );

    return strtr($msg, $convertions);
}

function fbAlert($msg)
{
    $msg = fbJsEscape($msg);
    echo "<script> alert('$msg'); </script>\n";
}

function fbAssertOrGoBack($predicate, $msg)
{
    if (!$predicate)
    {
        $msg = fbJsEscape($msg);
        echo "<script> alert('$msg'); window.history.go(-1); </script>\n";
        exit();
    }
}

function fbAssertOrGoTo($predicate, $msg, $url)
{
    if (!$predicate)
    {
        $msg = fbJsEscape($msg);
        $url = sefRelToAbs($url);
        echo "<script> alert('$msg'); window.location=$url'; </script>\n";
    }
}

function fbSetTimeout($url, $time, $script = 1)
{
    $url = sefRelToAbs($url);

    if ($script)
        echo '<script language="javascript">setTimeout("location=\'' . $url . '\'",$time)</script>';
    else
        echo 'setTimeout("location=\'' . $url . '\'",$time)';
}

function fbRedirect($url, $time, $msg)
{
    echo '<script language="javascript">';
    echo 'alert(\'' . fbJsEscape($msg) . '\')';
    fbSetTimeout($url, $time, 0);
    echo '</script>';
}
?>