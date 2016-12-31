<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Joomla_Sniffs_Commenting_SingleCommentSniff
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_Commenting_SingleCommentSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_COMMENT);
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
		$tokens     = $phpcsFile->getTokens();
		$singleLine = false;
		$comment    = trim($tokens[$stackPtr]['content']);

		// Hash comments are not allowed.
		if ($tokens[$stackPtr]['content']{0} === '#')
		{
			$phpcsFile->recordMetric($stackPtr, 'Inline comment style', '# ...');

			$error  = 'Perl-style Hash comments are prohibited. Use "// Comment."';
			$error .= ' or "/* comment */" instead.';
			$fix    = $phpcsFile->addFixableError($error, $stackPtr, 'WrongStyle');

			if ($fix === true)
			{
				$newComment = ltrim($tokens[$stackPtr]['content'], '# ');
				$newComment = '// ' . $newComment;
				$phpcsFile->fixer->replaceToken($stackPtr, $newComment);
			}
		}
		elseif ($tokens[$stackPtr]['content']{0} === '/' && $tokens[$stackPtr]['content']{1} === '/')
		{
			$phpcsFile->recordMetric($stackPtr, 'Inline comment style', '// ...');
			$singleLine = true;
		}
		elseif ($tokens[$stackPtr]['content']{0} === '/' && $tokens[$stackPtr]['content']{1} === '*')
		{
			$phpcsFile->recordMetric($stackPtr, 'Inline comment style', '/* ... */');
		}

		// Always have a space between // and the start of the comment text.
		// The exception to this is if the preceding line consists of a single open bracket.
		if ($tokens[$stackPtr]['content']{0} === '/' && $tokens[$stackPtr]['content']{1} === '/' && isset($tokens[$stackPtr]['content']{2})
			&& $tokens[$stackPtr]['content']{2} !== ' ' && isset($tokens[($stackPtr - 1)]['content']{0})
			&& $tokens[($stackPtr - 1)]['content']{0} !== '}'
		)
		{
			$error = 'Missing space between the // and the start of the comment text.';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpace');

			if ($fix === true)
			{
				$newComment = ltrim($tokens[$stackPtr]['content'], '\//');
				$newComment = '// ' . $newComment;

				$phpcsFile->fixer->replaceToken($stackPtr, $newComment);
			}
		}

		/*
		 * New lines should always start with an upper case letter, unless
		 * the line is a continuation of a complete sentence,
		 * the term is code and is case sensitive.(@todo)
		 */
		if (($singleLine === true && isset($tokens[$stackPtr]['content']{3}) && $tokens[$stackPtr]['content']{2} === ' '
			&& $tokens[$stackPtr]['content']{3} !== strtoupper($tokens[$stackPtr]['content']{3})) || (isset($comment{2}) && $comment{0} === '*'
			&& $comment{1} === ' ' && $comment{2} !== strtoupper($comment{2}))
		)
		{
			$error = 'Comment must start with a capital letter; found "%s"';
			$previous = $phpcsFile->findPrevious(T_COMMENT, $stackPtr - 1);

			if ($singleLine === true)
			{
				$data       = array($comment{3});
				$newComment = ltrim($tokens[$stackPtr]['content'], '\// ');
				$newComment = '// ' . ucfirst($newComment);
			}
			else
			{
				$data       = array($comment{2});
				$padding    = (strlen($tokens[$stackPtr]['content']) - strlen($comment));
				$padding    = str_repeat("\t", $padding - 2);
				$newComment = ltrim($comment, '* ');
				$newComment = $padding . ' * ' . ucfirst($newComment) . $phpcsFile->eolChar;
			}

			// Check for a comment on the previous line.
			if ($tokens[$previous]['line'] === $tokens[$stackPtr]['line'] - 1 && '/*' !== substr(ltrim($tokens[$previous]['content']), 0, 2))
			{
				$test = trim($tokens[$previous]['content']);

				if ('.' === $test{(strlen($test) - 1)})
				{
					$fix = $phpcsFile->addFixableError($error, $stackPtr, 'LowerCaseAfterSentenceEnd', $data);

					if ($fix === true)
					{
						$phpcsFile->fixer->replaceToken($stackPtr, $newComment);
					}
				}
			}
			else
			{
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'LowerCaseStart', $data);

				if ($fix === true)
				{
					$phpcsFile->fixer->replaceToken($stackPtr, $newComment);
				}
			}
		}

		/*
		 * Comments should not be on the same line as the code to which they refer
		 * (which puts them after the code they reference).
		 * They should be on their own lines.
		 * @todo Add fixer
		 */
		$previous = $phpcsFile->findPrevious(T_SEMICOLON, $stackPtr);

		if (isset($tokens[$previous]['line']) && $tokens[$previous]['line'] === $tokens[$stackPtr]['line'])
		{
			$error = 'Please put your comment on a separate line *preceding* your code; found "%s"';
			$data = array($comment);
			$phpcsFile->addError($error, $stackPtr, 'SameLine', $data);
		}

		/*
		 * Always have a single blank line before a comment or block of comments.
		 * Don't allow preceding "code" - identified by a semicolon ;)
		 */
		if (isset($tokens[$previous]['line']) && $tokens[$previous]['line'] === $tokens[$stackPtr]['line'] - 1
			&& $tokens[($stackPtr - 1)]['content'] !== '}'
		)
		{
			$error = 'Please consider a blank line preceding your comment.';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'BlankBefore');

			if ($fix === true)
			{
				$phpcsFile->fixer->addContent(($previous + 1), $phpcsFile->eolChar);
			}
		}

		/*
		 * Comment blocks that introduce large sections of code and are more than 2 lines long
		 * should use /* * / and should use * on each line with the same space/tab rules as doc
		 * blocks.
		 * If you need a large introduction consider whether this block should be separated into a
		 * method to reduce complexity and therefore providing a full docblock.
		 */
		$next = $phpcsFile->findNext(T_COMMENT, $stackPtr + 1);

		if ($singleLine === true && isset($tokens[$next]['line']) && $tokens[$next]['line'] === $tokens[$stackPtr]['line'] + 1
			&& $tokens[($stackPtr - 1)]['content'] !== '}'
		)
		{
			// The following line contains also a comment.
			$nextNext = $phpcsFile->findNext(T_COMMENT, $next + 1);

			if ($tokens[$nextNext]['line'] === $tokens[$next]['line'] + 1)
			{
				// Found 3 lines of // comments - too much.
				$error = 'Use the /* */ style for comments that span over multiple lines.';
				$phpcsFile->addError($error, $stackPtr, 'MultiLine');
			}
		}
	}
}
