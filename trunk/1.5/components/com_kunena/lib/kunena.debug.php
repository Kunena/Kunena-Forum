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
* @Copyright (C) 2008 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

defined( '_JEXEC' ) or die('Restricted access');

$kunena_db = &JFactory::getDBO();

// Debugging helpers

// First lets set some assertion settings for the code
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_CALLBACK, 'debug_assert_callback');

// Default assert call back funtion
// If certain things fail hard we MUST know about it
function debug_assert_callback($script, $line, $message) {
    echo "<h1>Assertion failed!</h1><br />
        Script: <strong>$script</strong><br />
        Line: <strong>$line</strong><br />
        Condition: <br /><pre>$message</pre>";
    // Now display the call stack
    echo debug_callstackinfo();
}

// Production error handling
function check_dberror($text = '', $back=0)
{
	$kunena_db = &JFactory::getDBO();
	$dberror = $kunena_db->stderr(true);
	echo debug_callstackinfo($back+1);

	require_once (KUNENA_PATH_LIB .DS. 'kunena.version.php');
	$kunenaVersion = CKunenaVersion::version();
	$kunenaPHPVersion = CKunenaVersion::PHPVersion();
	$kunenaMySQLVersion = CKunenaVersion::MySQLVersion();
?>
 <!-- Version Info -->
<div class="fbfooter">
Installed version:  <?php echo $kunenaVersion; ?> | php <?php echo $kunenaPHPVersion; ?> | mysql <?php echo $kunenaMySQLVersion; ?>
</div>
<!-- /Version Info -->
<?php

	kunena_error($text.'<br /><br />'.$dberror, E_USER_ERROR, $back+1);
}

function check_dberror($text='', $back=0)
{
	$kunena_db = &JFactory::getDBO();
	if ($kunena_db->getErrorNum() != 0)
	{
		check_dberror($text, $back+1);
	}
}

function check_dbwarning($text='')
{
	$kunena_db = &JFactory::getDBO();
	if ($kunena_db->getErrorNum() != 0)
	{
		check_dbwarning($text);
	}
}

function check_dbwarning($text = '')
{
	$kunena_db = &JFactory::getDBO();
	kunena_error($text.'<br />'.$kunena_db->stderr(true), E_USER_WARNING);
}

// Little helper to created a formated output of variables
function debug_vars($varlist)
{
	$output =  '<table border=1><tr> <th>variable</th> <th>value</th> </tr>';

	foreach( $varlist as $key => $value)
	{
	    if (is_array ($value) )
	    {
	        $output .= '<tr><td>$'.$key .'</td><td>';
	    if ( sizeof($value)>0 )
	        {
		        $dberror = false;
				$output .= '<table border=1><tr> <th>key</th> <th>value</th> </tr>';
		        foreach ($value as $skey => $svalue)
		        {
		        	if (is_array ($svalue) )
		        	{
		        		$output .= '<tr><td>[' . $skey .']</td><td>Nested Array</td></tr>';
		        	}
				    else if (is_object($svalue))
				    {
				    	$objvarlist = get_object_vars($svalue);

				    	// recursive function call
				    	debug_vars($objvarlist);
				    }
				    else
				    {
				    	$dberror = ($svalue == "check_dberror");
						$output .= '<tr><td>$' . $skey .'</td><td>"'. $svalue .'"</td></tr>';
				    }
		        }
				if ($dberror) {
					$kunena_db = &JFactory::getDBO();
					jimport('geshi.geshi');
					$sql = $kunena_db->_sql;
					if (file_exists(JPATH_ROOT.DS.'libraries'.DS.'geshi'.DS.'geshi'.DS."mysql.php")) {
						$geshi = new GeSHi($sql, "mysql");
						$geshi->enable_keyword_links(false);
						$geshi->set_header_type(GESHI_HEADER_NONE);
						$sql = $geshi->parse_code();
					}
					$output .= '<tr><td>Query</td><td>'. $sql.'</td></tr>';
					$output .= '<tr><td>Db error</td><td>'. $kunena_db->getErrorMsg().'</td></tr>';
				}
		        $output .= '</table>';
	        }
	        else
	        {
	            $output .= 'EMPTY';
	        }
	        $output .= '</td></tr>';
	    }
	    else if (is_object($value))
	    {
	    	$objvarlist = get_object_vars($value);

	    	// recursive function call
	    	debug_vars($objvarlist);
	    }
	    else
	    {
	    	$output .= '<tr><td>$' . $key .'</td><td>"'. $value .'"</td></tr>';
	    }
	}
	$output .= '</table>';

	return $output;
}

// Show the callstack to this point in a decent format
function debug_callstackinfo($back=1)
{
	$trace = array_slice(debug_backtrace(), $back);
	return debug_vars($trace);
}

function kunena_error($message, $level=E_USER_NOTICE, $back=1) {
	$trace = debug_backtrace();
	$caller = $trace[$back];
	trigger_error($message.' in <strong>'.$caller['function'].'()</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."\n<br /><br />Error reported", $level);
}
?>
