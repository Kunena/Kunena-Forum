<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Joomla_Sniffs_Functions_StatementNotFunctionSniff.
 *
 * Checks that language statements do no use brackets.
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_Functions_StatementNotFunctionSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_INCLUDE_ONCE,
			T_REQUIRE_ONCE,
			T_REQUIRE,
			T_INCLUDE,
			T_CLONE,
			T_ECHO,
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
		$tokens    = $phpcsFile->getTokens();
		$nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

		if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS && $tokens[($stackPtr)]['code'] !== T_ECHO)
		{
			$error = '"%s" is a statement not a function; no parentheses are required';
			$data  = array($tokens[$stackPtr]['content']);
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'BracketsNotRequired', $data);

			if ($fix === true)
			{
				$end      = $phpcsFile->findEndOfStatement($nextToken);
				$ignore   = PHP_CodeSniffer_Tokens::$emptyTokens;
				$ignore[] = T_SEMICOLON;
				$closer   = $phpcsFile->findPrevious($ignore, ($end - 1), null, true);

				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($nextToken, '');

				if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE)
				{
					$phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
				}

				if ($tokens[$closer]['code'] === T_CLOSE_PARENTHESIS)
				{
					$phpcsFile->fixer->replaceToken($closer, '');
				}

				$phpcsFile->fixer->addContent($stackPtr, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		}

		if ($tokens[($stackPtr)]['code'] === T_ECHO
			&& $tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS
			&& $tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE
		)
		{
			$error = 'There must be one space between the "%s" statement and the opening parenthesis';
			$data  = array($tokens[$stackPtr]['content']);
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterEcho', $data);

			if ($fix === true)
			{
				$this->requiredSpacesBeforeOpen = 1;
				$padding = str_repeat(' ', $this->requiredSpacesBeforeOpen);
				$phpcsFile->fixer->addContent($stackPtr, $padding);
			}
		}
	}
}
