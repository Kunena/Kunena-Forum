<?php
/**
 * Joomla! Coding Standard
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

if (class_exists('PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff', true) === false)
{
	throw new PHP_CodeSniffer_Exception('Class PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff not found');
}

/**
 * Extended ruleset for ensuring method and function names are correct.
 *
 * @package   Joomla.CodingStandard
 * @since     1.0
 */
class Kunena_Sniffs_NamingConventions_ValidFunctionNameSniff extends PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff
{
	/**
	 * Processes the tokens within the scope.
	 *
	 * Extends PEAR.NamingConventions.ValidFunctionName.processTokenWithinScope to remove the requirement for leading underscores on
	 * private method names.
	 *
	 * @param   PHP_CodeSniffer_File $phpcsFile  The file being processed.
	 * @param   integer              $stackPtr   The position where this token was found.
	 * @param   integer              $currScope  The position of the current scope.
	 *
	 * @return  void
	 */
	protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
	{
		$methodName = $phpcsFile->getDeclarationName($stackPtr);

		if ($methodName === null)
		{
			// Ignore closures.
			return;
		}

		$className = $phpcsFile->getDeclarationName($currScope);
		$errorData = array($className . '::' . $methodName);

		// Is this a magic method. i.e., is prefixed with "__" ?
		if (preg_match('|^__|', $methodName) !== 0)
		{
			$magicPart = strtolower(substr($methodName, 2));

			if (isset($this->magicMethods[$magicPart]) === false)
			{
				$error = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
				$phpcsFile->addError($error, $stackPtr, 'MethodDoubleUnderscore', $errorData);
			}

			return;
		}

		// PHP4 constructors are allowed to break our rules.
		if ($methodName === $className)
		{
			return;
		}

		// PHP4 destructors are allowed to break our rules.
		if ($methodName === '_' . $className)
		{
			return;
		}

		$methodProps    = $phpcsFile->getMethodProperties($stackPtr);
		$scope          = $methodProps['scope'];
		$scopeSpecified = $methodProps['scope_specified'];

		if ($methodProps['scope'] === 'private')
		{
			$isPublic = false;
		}
		else
		{
			$isPublic = true;
		}

		// Joomla change: Methods must not have an underscore on the front.
		if ($scopeSpecified === true && $methodName{0} === '_')
		{
			$error = '%s method name "%s" must not be prefixed with an underscore';
			$data  = array(
				ucfirst($scope),
				$errorData[0],
			);

			$phpcsFile->addError($error, $stackPtr, 'MethodUnderscore', $data);
			$phpcsFile->recordMetric($stackPtr, 'Method prefixed with underscore', 'yes');

			return;
		}

		/*
		 * If the scope was specified on the method, then the method must be camel caps
		 * and an underscore should be checked for. If it wasn't specified, treat it like a public method
		 * and remove the underscore prefix if there is one because we cant determine if it is private or public.
		 */
		$testMethodName = $methodName;

		if ($scopeSpecified === false && $methodName{0} === '_')
		{
			$testMethodName = substr($methodName, 1);
		}

		if (PHP_CodeSniffer::isCamelCaps($testMethodName, false, true, false) === false)
		{
			if ($scopeSpecified === true)
			{
				$error = '%s method name "%s" is not in camel caps format';
				$data  = array(
					ucfirst($scope),
					$errorData[0],
				);
				$phpcsFile->addError($error, $stackPtr, 'ScopeNotCamelCaps', $data);
			}
			else
			{
				$error = 'Method name "%s" is not in camel caps format';
				$phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
			}

			return;
		}
	}
}
