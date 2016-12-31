<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Ensures that new classes are instantiated without brackets if they do not have any parameters.
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_Classes_InstantiateNewClassesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * If true, short Array Syntax for php 5.4+ will be allow for Instantiating New Classes if they are found in the code.
	 * this should be removed when all Joomla projects no longer need to support php 5.3
	 *
	 * @var boolean
	 */
	public $shortArraySyntax = false;

	/**
	 * Registers the token types that this sniff wishes to listen to.
	 *
	 * @return  array
	 */
	public function register()
	{
		return array(T_NEW);
	}

	/**
	 * Process the tokens that this sniff is listening for.
	 *
	 * @param   PHP_CodeSniffer_File  $phpcsFile  The file where the token was found.
	 * @param   integer               $stackPtr   The position in the stack where the token was found.
	 *
	 * @return  void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		$running = true;
		$valid   = false;
		$started = false;

		$cnt = $stackPtr + 1;

		do
		{
			if (false === isset($tokens[$cnt]))
			{
				$running = false;
			}
			else
			{
				switch ($tokens[$cnt]['code'])
				{
					case T_SEMICOLON :
					case T_COMMA :
						$valid   = true;
						$running = false;
						break;

					case T_OPEN_PARENTHESIS :
						$started = true;
						break;

					case T_VARIABLE :
					case T_STRING :
					case T_LNUMBER :
					case T_CONSTANT_ENCAPSED_STRING :
					case T_DOUBLE_QUOTED_STRING :
					case T_ARRAY :
					case T_TRUE :
					case T_FALSE :
					case T_NULL :
						if ($started === true)
						{
							$valid   = true;
							$running = false;
						}

						break;

					case T_CLOSE_PARENTHESIS :
						if ($started === false)
						{
							$valid = true;
						}

						$running = false;
						break;

					case T_WHITESPACE :
						break;

					case T_OPEN_SHORT_ARRAY :
						if ($shortArraySyntax === true)
						{
							if ($started === true)
							{
								$valid   = true;
								$running = false;
							}
						}
						break;
				}

				$cnt++;
			}
		}
		while ($running === true);

		if ($valid === false)
		{
			$error = 'Instanciating new class without parameters does not require brackets.';
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NewClass');

			if ($fix === true)
			{
				$classNameEnd = $phpcsFile->findNext(
					array(
						T_VARIABLE,
						T_WHITESPACE,
						T_NS_SEPARATOR,
						T_STRING,
					),
					($stackPtr + 1),
					null,
					true,
					null,
					true
				);

				$phpcsFile->fixer->beginChangeset();

				if ($tokens[($stackPtr + 3)]['code'] === T_WHITESPACE)
				{
					$phpcsFile->fixer->replaceToken(($stackPtr + 3), '');
				}

				for ($i = $classNameEnd; $i < $cnt; $i++)
				{
					$phpcsFile->fixer->replaceToken($i, '');
				}

				$phpcsFile->fixer->endChangeset();
			}
		}
	}
}
