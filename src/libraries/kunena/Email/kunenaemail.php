<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Email
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

defined('_JEXEC') or die();

use ErrorException;
use Exception;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use function defined;

/**
 * Class KunenaEmail
 *
 * @since   Kunena 6.0
 */
abstract class KunenaEmail
{
	public static $mailer_error_status = null;

	/**
	 * @param   MailTemplate  $mail       mail
	 * @param   array         $receivers  receivers
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function send($mail, array $receivers)
	{
		if (isset(static::$mailer_error_status))
		{
			// Mailer is broken, so prevent any sendings
			Log::add(static::$mailer_error_status->getMessage(), Log::ERROR, 'kunena');

			return false;
		}

		$config = KunenaFactory::getConfig();

		if (!empty($config->email_recipient_count))
		{
			$email_recipient_count = $config->email_recipient_count;
		}
		else
		{
			$email_recipient_count = 1;
		}

		$email_recipient_privacy = $config->get('email_recipient_privacy', 'bcc');

		// If we hide email addresses from other users, we need to add TO address to prevent email from becoming spam.
		if ($email_recipient_count > 1
			&& $email_recipient_privacy == 'bcc'
			&& MailHelper::isEmailAddress($config->get('email_visible_address'))
		)
		{
			$mail->AddAddress($config->email_visible_address, MailHelper::cleanAddress($config->board_title));

			// Also make sure that email receiver limits are not violated (TO + CC + BCC = limit).
			if ($email_recipient_count > 9)
			{
				$email_recipient_count--;
			}
		}

		$chunks = array_chunk($receivers, $email_recipient_count);

		$success = true;

		foreach ($chunks as $emails)
		{
			if ($email_recipient_count == 1 || $email_recipient_privacy == 'to')
			{
				$mail->ClearAddresses();
				$mail->addRecipient($emails);
			}
			elseif ($email_recipient_privacy == 'cc')
			{
				$mail->ClearCCs();
				$mail->addCC($emails);
			}
			else
			{
				$mail->ClearBCCs();
				$mail->addBCC($emails);
			}

			try
			{
				$result = $mail->Send();

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
	public static function on_mail_error($errno, $errstr, $errfile, $errline)
	{
		if (strpos($errstr, "mail(): Failed to connect to mailserver") !== false)
		{
			static::$mailer_error_status = new MailerBrokenException(
				$errstr, $errno, Log::ERROR, $errfile, $errline
			);

			Log::add(static::$mailer_error_status->errorMessage(), Log::ERROR, 'kunena');
		}

		return false;
	}
}

class MailerBrokenException extends ErrorException
{
	/**
	 * @return  string
	 *
	 * @since   Kunena 5.1.15
	 */
	public function errorMessage()
	{
		return "$this->code - $$this->message\n at: $$this->file:$$this->line";
	}
}
