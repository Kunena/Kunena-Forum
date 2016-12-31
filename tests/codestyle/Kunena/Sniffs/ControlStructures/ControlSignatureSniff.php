<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 *
 * @since   1.0
 */
class Kunena_Sniffs_ControlStructures_ControlSignatureSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * The number of spaces code should be indented.
	 *
	 * @var integer
	 */
	public $indent = 1;

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
								   'PHP',
								   'JS',
								  );

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return int[]
	 */
	public function register()
	{
		return array(
				T_TRY,
				T_CATCH,
				T_FINALLY,
				T_DO,
				T_WHILE,
				T_FOR,
				T_FOREACH,
				T_IF,
				T_ELSE,
				T_ELSEIF,
				T_SWITCH,
			   );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param   PHP_CodeSniffer_File  $phpcsFile  The file being scanned.
	 * @param   int                   $stackPtr   The position of the current token in the stack passed in $tokens.
	 *
	 * @return  void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[($stackPtr + 1)]) === false)
		{
			return;
		}

		// Single space after the keyword.
		$found = 1;

		if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE)
		{
			$found = 0;
		}
		elseif ($tokens[($stackPtr + 1)]['content'] !== ' ')
		{
			if (strpos($tokens[($stackPtr + 1)]['content'], $phpcsFile->eolChar) !== false)
			{
				$found = 'newline';
			}
			else
			{
				$found = strlen($tokens[($stackPtr + 1)]['content']);
			}
		}

		if ($found !== 1
			&& $tokens[($stackPtr)]['code'] !== T_ELSE
			&& $tokens[($stackPtr)]['code'] !== T_TRY
			&& $tokens[($stackPtr)]['code'] !== T_DO
			&& $tokens[($stackPtr)]['code'] !== T_FINALLY
		)
		{
			$error = 'Expected 1 space after %s keyword; %s found';
			$data  = array(
					  strtoupper($tokens[$stackPtr]['content']),
					  $found,
					 );
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);

			if ($fix === true)
			{
				if ($found === 0)
				{
					$phpcsFile->fixer->addContent($stackPtr, ' ');
				}
				else
				{
					$phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
				}
			}
		}

		if ($tokens[$stackPtr]['code'] === T_WHILE && !isset($tokens[$stackPtr]['scope_opener']) === true)
		{
			// Zero spaces after parenthesis closer.
			$closer = $tokens[$stackPtr]['parenthesis_closer'];
			$found  = 0;

			if ($tokens[($closer + 1)]['code'] === T_WHITESPACE)
			{
				if (strpos($tokens[($closer + 1)]['content'], $phpcsFile->eolChar) !== false)
				{
					$found = 'newline';
				}
				else
				{
					$found = strlen($tokens[($closer + 1)]['content']);
				}
			}

			if ($found !== 0)
			{
				$error = 'Expected 0 spaces before semicolon; %s found';
				$data  = array($found);
				$fix   = $phpcsFile->addFixableError($error, $closer, 'SpaceBeforeSemicolon', $data);

				if ($fix === true)
				{
					$phpcsFile->fixer->replaceToken(($closer + 1), '');
				}
			}
		}//end if

		// Only want to check multi-keyword structures from here on.
		if ($tokens[$stackPtr]['code'] === T_DO)
		{
			if (isset($tokens[$stackPtr]['scope_closer']) === false)
			{
				return;
			}

			$closer = $tokens[$stackPtr]['scope_closer'];
		}
		elseif ($tokens[$stackPtr]['code'] === T_ELSE
			|| $tokens[$stackPtr]['code'] === T_ELSEIF
			|| $tokens[$stackPtr]['code'] === T_CATCH
		)
		{
			$closer = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);

			if ($closer === false || $tokens[$closer]['code'] !== T_CLOSE_CURLY_BRACKET)
			{
				return;
			}
		}
		else
		{
			return;
		}

		// Own line for do, else, elseif, catch and no white space after closing brace
		$found = 0;

		if ($tokens[($closer + 1)]['code'] === T_WHITESPACE
			&& $tokens[($closer + 1)]['content'] !== $phpcsFile->eolChar
		)
		{
			$found = strlen($tokens[($closer + 1)]['content']);
		}

		if (0 !== $found)
		{
			$error = 'Expected 0 space after closing brace; %s found';
			$data  = array($found);
			$fix   = $phpcsFile->addFixableError($error, $closer, 'SpaceAfterCloseBrace', $data);

			if (true === $fix)
			{
				$phpcsFile->fixer->replaceToken(($closer + 1), '' . $phpcsFile->eolChar);
			}
		}

		if ($tokens[($closer + 1)]['content'] !== $phpcsFile->eolChar && 0 === $found)
		{
			$error  = 'Definition of do,else,elseif,catch must be on their own line.';
			$fix    = $phpcsFile->addFixableError($error, $closer, 'NewLineAfterCloseBrace');
			$blanks = substr($tokens[($closer - 1)]['content'], strpos($tokens[($closer - 1)]['content'], $phpcsFile->eolChar));
			$spaces = str_repeat("\t", strlen($blanks));

			if (true === $fix)
			{
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($closer, $phpcsFile->eolChar);
				$phpcsFile->fixer->addContentBefore(($closer + 1), $spaces);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}
}
