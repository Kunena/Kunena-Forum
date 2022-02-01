<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Email
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

\defined('_JEXEC') or die();

use ErrorException;
use Exception;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Class KunenaEmail
 *
 * @since   Kunena 6.0
 */
abstract class KunenaEmail
{
	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public static $mailer_error_status = null;

	/**
	 * @param   MailTemplate  $mail       mail
	 * @param   array         $receivers  receivers
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function send(MailTemplate $mailTemplate, array $receivers): bool
	{
		if (isset(static::$mailer_error_status))
		{
			// Mailer is broken, so prevent any sending
			Log::add(static::$mailer_error_status->getMessage(), Log::ERROR, 'kunena');

			return false;
		}

		$config = KunenaFactory::getConfig();

		if (!empty($config->emailRecipientCount))
		{
			$emailRecipientCount = $config->emailRecipientCount;
		}
		else
		{
			$emailRecipientCount = 1;
		}

		$emailRecipientPrivacy = $config->get('emailRecipientPrivacy', 'bcc');

		// If we hide email addresses from other users, we need to add TO address to prevent email from becoming spam.
		if ($emailRecipientCount > 1
			&& $emailRecipientPrivacy == 'bcc'
			&& MailHelper::isEmailAddress($config->get('emailVisibleAddress'))
		)
		{
			$mailTemplate->AddAddress($config->emailVisibleAddress, MailHelper::cleanAddress($config->boardTitle));

			// Also make sure that email receiver limits are not violated (TO + CC + BCC = limit).
			if ($emailRecipientCount > 9)
			{
				$emailRecipientCount--;
			}
		}

		$chunks = array_chunk($receivers, $emailRecipientCount);

		$success = true;

		foreach ($chunks as $emails)
		{
			if ($emailRecipientCount == 1 || $emailRecipientPrivacy == 'to')
			{
				$mailTemplate->ClearAddresses();
				$mailTemplate->addRecipient($emails);
			}
			elseif ($emailRecipientPrivacy == 'cc')
			{
				$mailTemplate->ClearCCs();
				$mailTemplate->addCC($emails);
			}
			else
			{
				$mailTemplate->ClearBCCs();
				$mailTemplate->addBCC($emails);
			}

			try
			{
				$result = $mailTemplate->Send();

				if ($result === false)
				{
					// Mail is turned off, or broken
					return false;
				}

				if (is_subclass_of($result, 'Exception'))
				{
					// Mail send is failed
					$success = false;
				}
			}
			catch (Exception $e)
			{
				$success = false;
				Log::add($e->getMessage(), Log::ERROR, 'kunena');
			}

			if (isset(static::$mailer_error_status))
			{
				$success = false;
				break;
			}
		}

		return $success;
	}

	/**
	 * @param   integer  $errno    error number
	 * @param   string   $errstr   error string
	 * @param   string   $errfile  error file
	 * @param   string   $errline  error line
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function on_mail_error(int $errno, string $errstr, string $errfile, string $errline): bool
	{
		if (strpos($errstr, "mail(): Failed to connect to mail server") !== false)
		{
			static::$mailer_error_status = new MailerBrokenException(
				$errstr,
				$errno,
				Log::ERROR,
				$errfile,
				$errline
			);

			Log::add(static::$mailer_error_status->errorMessage(), Log::ERROR, 'kunena');
		}

		return false;
	}
}

/**
 * @since   Kunena 5.1.15
 */
class MailerBrokenException extends ErrorException
{
	/**
	 * @return  string
	 *
	 * @since   Kunena 5.1.15
	 */
	public function errorMessage(): string
	{
		return "$this->code - $$this->message\n at: $$this->file:$$this->line";
	}
}
