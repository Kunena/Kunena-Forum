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



function kunenaJsEscape($msg)
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

function kunenaAlert($msg)
{
    $msg = kunenaJsEscape($msg);
    echo "<script> alert('$msg'); </script>\n";
}

function kunenaAssertOrGoBack($predicate, $msg)
{
    $app =& JFactory::getApplication();
    if (!$predicate)
    {
        $msg = kunenaJsEscape($msg);
        echo "<script> alert('$msg'); window.history.go(-1); </script>\n";
        $app->close();
    }
}

function kunenaAssertOrGoTo($predicate, $msg, $url)
{
    if (!$predicate)
    {
        $msg = kunenaJsEscape($msg);
        $url = JRoute::_($url);
        echo "<script> alert('$msg'); window.location=$url'; </script>\n";
    }
}

// FIXME: deprecated
function kunenaSetTimeout($url, $time, $script = 1)
{
    $url = JRoute::_($url);

    if ($script)
        echo CKunenaLink::GetAutoRedirectHTML($url, $time);
    else
        echo 'setTimeout("location=\'' . $url . '\'",$time)';
}

function kunenaRedirect($url, $time, $msg)
{
    echo '<script language="javascript">';
    echo 'alert(\'' . kunenaJsEscape($msg) . '\')';
    kunenaSetTimeout($url, $time, 0);
    echo '</script>';
}
?>
