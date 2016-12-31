<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Joomla_Sniffs_Operators_ValidLogicalOperatorsSniff
 *
 * Check to ensure that the logical operators 'and' and 'or' are not used.
 * Use the && and || operators instead.
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_Operators_ValidLogicalOperatorsSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_LOGICAL_AND,
				T_LOGICAL_OR,
			   );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param   PHP_CodeSniffer_File  $phpcsFile  The current file being scanned.
	 * @param   int                   $stackPtr   The position of the current token in the stack passed in $tokens.
	 *
	 * @return  void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens    = $phpcsFile->getTokens();
		$operators = array(
					  'and' => '&&',
					  'or'  => '||',
					 );
		$operator  = strtolower($tokens[$stackPtr]['content']);

		if (false === isset($operators[$operator]))
		{
			// We have correct logical operators in use so return
			return;
		}

		$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

		if ($tokens[$nextToken]['code'] === T_EXIT)
		{
			// This enforces an exception for things like `or die;` and `or exit;`
			return;
		}

		// Special Joomla! cases.
		if ($tokens[$nextToken]['content'] === 'jexit'
			|| $tokens[$nextToken]['content'] === 'JSession'
			|| $tokens[$nextToken]['content'] === 'define'
			|| $tokens[($nextToken + 2)]['content'] === 'sendResponse'
			|| $tokens[($nextToken + 2)]['content'] === 'sendJsonResponse'
		)
		{
			// Exceptions for things like `or jexit()`, `or JSession`, `or define`, `or sendResponse`, `or sendJsonResponse`
			return;
		}

		$error = 'Logical operator "%s" not allowed; use "%s" instead';
		$data  = array(
				  $operator,
				  $operators[$operator],
				 );
		$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed', $data);

		if (true === $fix)
		{
			$phpcsFile->fixer->replaceToken($stackPtr, $operators[$operator]);
		}
	}
}
