<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Control Structures Brackets Test.
 *
 * Checks if the declaration of control structures is correct.
 * Curly brackets must be on a line by their own.
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_ControlStructures_ControlStructuresBracketsSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * The number of spaces code should be indented.
	 *
	 * @var integer
	 */
	public $indent = 4;


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_IF,
				T_ELSEIF,
				T_ELSE,
				T_FOREACH,
				T_FOR,
				T_SWITCH,
				T_DO,
				T_WHILE,
				T_TRY,
				T_CATCH,
				T_FINALLY,
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
		$tokens    = $phpcsFile->getTokens();
		$errorData = array(strtolower($tokens[$stackPtr]['content']));

		if (false === isset($tokens[$stackPtr]['scope_opener']))
		{
			// @todo sort while thing...
			if ($tokens[$stackPtr]['code'] !== T_WHILE)
			{
				$error = 'Possible parse error: %s missing opening or closing brace';
				$phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);
			}

			return;
		}

		$curlyBrace  = $tokens[$stackPtr]['scope_opener'];
		$lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
		$classLine   = $tokens[$lastContent]['line'];
		$braceLine   = $tokens[$curlyBrace]['line'];

		if ($braceLine === $classLine)
		{
			$phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'same line');
			$error = 'Opening brace of a %s must be on the line after the definition';
			$fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceNewLine', $errorData);

			if (true === $fix)
			{
				$phpcsFile->fixer->beginChangeset();

				if (T_WHITESPACE === $tokens[($curlyBrace - 1)]['code'])
				{
					$phpcsFile->fixer->replaceToken(($curlyBrace - 1), '');
				}

				$phpcsFile->fixer->addNewlineBefore($curlyBrace);
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}
		else
		{
			$phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'new line');

			if ($braceLine > ($classLine + 1))
			{
				$error = 'Opening brace of a %s must be on the line following the %s declaration.; Found %s line(s).';
				$data  = array(
						  $tokens[$stackPtr]['content'],
						  $tokens[$stackPtr]['content'],
						  ($braceLine - $classLine - 1),
						 );
				$fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceWrongLine', $data);

				if (true === $fix)
				{
					$phpcsFile->fixer->beginChangeset();

					for ($i = ($curlyBrace - 1); $i > $lastContent; $i--)
					{
						if ($tokens[$i]['line'] === ($tokens[$curlyBrace]['line'] + 1))
						{
							break;
						}

						$phpcsFile->fixer->replaceToken($i, '');
					}

					$phpcsFile->fixer->endChangeset();
				}

				return;
			}
		}

		if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar)
		{
			$error = 'Opening %s brace must be on a line by itself.';
			$fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceNotAlone', $errorData);

			if (true === $fix)
			{
				$phpcsFile->fixer->addContent($curlyBrace, $phpcsFile->eolChar);
			}
		}

		if (T_WHITESPACE === $tokens[($curlyBrace - 1)]['code'])
		{
			$prevContent = $tokens[($curlyBrace - 1)]['content'];

			if ($prevContent === $phpcsFile->eolChar)
			{
				$spaces = 0;
			}
			else
			{
				$blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));

				$spaces     = strlen($blankSpace);
			}

			$expected = ($tokens[$stackPtr]['level'] * ($this->indent));

			if ($spaces !== $expected)
			{
				$error = 'Expected %s tabs before opening brace; %s found';

				// We have to convert the space values to tab values here, due to the tab-width conversion done by phpcs
				$expected /= 4;
				$spaces /= 4;
				$data  = array(
						  $expected,
						  $spaces,
						 );
				$fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'SpaceBeforeBrace', $data);

				if ($fix === true)
				{
					$indent = str_repeat("\t", $expected);

					if ($spaces === 0)
					{
						$phpcsFile->fixer->addContentBefore($curlyBrace, $indent);
					}
					else
					{
						$phpcsFile->fixer->replaceToken(($curlyBrace - 1), $indent);
					}
				}
			}
		}

		// Single newline after opening brace.
		if (isset($tokens[$stackPtr]['scope_opener']) === true)
		{
			$opener = $tokens[$stackPtr]['scope_opener'];

			for ($next = ($opener + 1); $next < $phpcsFile->numTokens; $next++)
			{
				$code = $tokens[$next]['code'];

				if ($code === T_WHITESPACE)
				{
					continue;
				}

				// Skip all empty tokens on the same line as the opener.
				if ($tokens[$next]['line'] === $tokens[$opener]['line']
					&& (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$code]) === true
					|| $code === T_CLOSE_TAG)
				)
				{
					continue;
				}

				// We found the first bit of a code, or a comment on the following line.
				break;
			}

			$found = ($tokens[$next]['line'] - $tokens[$opener]['line']);

			if ($found !== 1)
			{
				$error = 'Expected 1 newline after opening brace; %s found';
				$data  = array($found);
				$fix   = $phpcsFile->addFixableError($error, $opener, 'NewlineAfterOpenBrace', $data);

				if ($fix === true)
				{
					$phpcsFile->fixer->beginChangeset();

					for ($i = ($opener + 1); $i < $next; $i++)
					{
						if ($found > 0 && $tokens[$i]['line'] === $tokens[$next]['line'])
						{
							break;
						}

						$phpcsFile->fixer->replaceToken($i, '');
					}

					$phpcsFile->fixer->addContent($opener, $phpcsFile->eolChar);
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}
}
