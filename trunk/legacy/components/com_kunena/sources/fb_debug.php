<?php
/**
* @version $Id: fb_debug.php 966 2008-08-12 05:00:34Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2008 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

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
function trigger_dberror($text = '')
{
	global $database;
	echo debug_callstackinfo();
	trigger_error($text.'\n'.$database->stderr(true), E_USER_ERROR);
}

function check_dberror($text='')
{
	global $database;
	if ($database->_errorNum != 0)
	{
		trigger_dberror($text);
	}
}

function trigger_dbwarning($text = '')
{
	global $database;
	trigger_error($text.'\n'.$database->stderr(true), E_USER_WARNING);
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
		        $output .= '"<table border=1><tr> <th>key</th> <th>value</th> </tr>';
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
				    	$output .= '<tr><td>$' . $skey .'</td><td>"'. $svalue .'"</td></tr>';
				    }
		        }
		        $output .= '</table>"';
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
function debug_callstackinfo()
{
	return debug_vars(debug_backtrace());
}
?>