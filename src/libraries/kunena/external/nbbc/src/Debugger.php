<?php
/**
 * @copyright Copyright (C) 2008-10, Sean Werkema. All rights reserved.
 * @copyright 2016 Vanilla Forums Inc. (changes only)
 * @license   BSDv2
 */

namespace Nbbc;

// -----------------------------------------------------------------------------
//
//  nbbc_main.php
//
//  This file is part of NBBC, the New BBCode Parser.
//
//  NBBC implements \a fully-validating, high-speed, extensible parser for the
//  BBCode document language.  Its output is XHTML 1.0 Strict conformant no
//  matter what its input is.  NBBC supports the full standard BBCode language,
//  as well as comments, columns, enhanced quotes, spoilers, acronyms, wiki
//  links, several list styles, justification, indentation, and smileys, among
//  other advanced features.
//
// -----------------------------------------------------------------------------
//
//  Copyright (c) 2008-9, the Phantom Inker.  All rights reserved.
//
//
//  Redistribution and use in source and binary forms, with or without
//  modification, are permitted provided that the following conditions
//  are met:
//
//    * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//
//    * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in
//       the documentation and/or other materials provided with the
//       distribution.
//
//  THIS SOFTWARE IS PROVIDED BY THE PHANTOM INKER "AS IS" AND ANY EXPRESS
//  OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
//  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
//  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
//  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
//  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
//  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
//  BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
//  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
//  OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
//  IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//
// -----------------------------------------------------------------------------
//
//  This file provides definitions shared throughout the parser, and a
//  uniform way to include all the parser's pieces.
//
// -----------------------------------------------------------------------------

use DateTime;

class Debugger
{
	public static $level = 9;

	public static $debug = 1;

	public static $info = 2;

	public static $warning = 3;

	public static $error = 4;

	// File to log messages to
	public static $log_file = '';

	/**
	 * Store log information to a file - Modified method by Kunena team
	 * 
	 * @param int     $level
	 * @param string  $string
	 * @param boolean $logAll
	 */
	public static function log($level, $string, $logAll = false)
	{
		if ($level >= static::$level)
		{
			SELF::storeLog($string);
		}
	}

	/**
	 * Added by Kunena Team
	 */
	protected static function storeLog($string)
	{
		if (strpos($string, "\n") === false)
		{
			$string .= "\n";
		}

		$date   = new DateTime;
		$string = '[' . $date->format('Y-m-d H:i:s.u') . '] ' . $string;

		if (static::$log_file)
		{
			file_put_contents(static::$log_file, $string, FILE_APPEND);
		}
		else
		{
			echo $string;
		}
	}

	public static function debug($string)
	{
		static::log(static::$debug, $string);
	}

	public static function info($string)
	{
		static::log(static::$info, $string);
	}

	public static function warning($string)
	{
		static::log(static::$warning, $string);
	}

	public static function error($string)
	{
		static::log(static::$error, $string);
	}
}
