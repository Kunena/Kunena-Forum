<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
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
defined( '_JEXEC' ) or die('Restricted access');



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
        $mainframe->close();
    }
}

function fbAssertOrGoTo($predicate, $msg, $url)
{
    if (!$predicate)
    {
        $msg = fbJsEscape($msg);
        $url = JRoute::_($url);
        echo "<script> alert('$msg'); window.location=$url'; </script>\n";
    }
}

// FIXME: deprecated
function fbSetTimeout($url, $time, $script = 1)
{
    $url = JRoute::_($url);

    if ($script)
        echo CKunenaLink::GetAutoRedirectHTML($url, $time);
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
