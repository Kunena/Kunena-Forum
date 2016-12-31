<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * White space before a control structure.
 *
 * Checks that there is an empty line before a control structure to improve readability.
 * This only applies if the line before the structure contains code.
 * Comments or curly braces are considered valid.
 *
 * Bad1:
 * $foo = $bar;
 * if(condition)
 * {
 *     // blah
 * }
 *
 * Bad2:
 * if(condition)
 * {
 *     // blah
 * }
 * if(nextcondition)
 * {
 *     // blubb
 * }
 *
 * Good1:
 * $foo = $bar;
 *
 * if(condition)
 * {
 *     // blah
 * }
 *
 * Good2:
 * if(condition)
 * {
 *     // blah
 * }
 *
 * if(nextcondition)
 * {
 *     // blubb
 * }
 *
 * This rule applies for the structures:
 * if, for, foreach, while, switch, try, do and return
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_ControlStructures_WhiteSpaceBeforeSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_IF,
				T_FOR,
				T_FOREACH,
				T_SWITCH,
				T_TRY,
				T_WHILE,
				T_DO,
				T_RETURN,
			   );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param   PHP_CodeSniffer_File  $phpcsFile  The file being scanned.
	 * @param   integer               $stackPtr   The position of the current token in the stack passed in $tokens.
	 *
	 * @return  void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[$stackPtr]['scope_opener']) === false && $tokens[$stackPtr]['code'] !== T_RETURN)
		{
			return;
		}

		$prev = $phpcsFile->findPrevious(array(T_SEMICOLON, T_CLOSE_CURLY_BRACKET), ($stackPtr - 1), null, false);

		if ($tokens[$stackPtr]['line'] - 1 === $tokens[$prev]['line'])
		{
			$error = 'Please consider an empty line before the %s statement;';
			$data  = array($tokens[$stackPtr]['content']);
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBefore', $data);

			if ($fix === true)
			{
				$phpcsFile->fixer->addNewlineBefore($stackPtr);
			}
		}
	}
}
