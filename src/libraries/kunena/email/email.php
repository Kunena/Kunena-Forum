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
defined('_JEXEC') or die();

use Joomla\CMS\Log\Log;

/**
 * Class KunenaEmail
 *
 * @since Kunena
 */
abstract class KunenaEmail
{
	public static $mailer_error_status = null;

	/**
	 * @param   \Joomla\CMS\Mail\Mail  $mail       mail
	 * @param   array                  $receivers  receivers
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
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
			&& \Joomla\CMS\Mail\MailHelper::isEmailAddress($config->get('email_visible_address'))
		)
		{
			$mail->AddAddress($config->email_visible_address, \Joomla\CMS\Mail\MailHelper::cleanAddress($config->board_title));

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
					// mail is turned off, or broken
					return false;

				if (is_subclass_of($result, 'Exception'))
				{
					// mail send is failed
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
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 *
	 * @return bool
	 *
	 * @since version
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
	public function errorMessage()
	{
		return "$this->code - $$this->message\n at: $$this->file:$$this->line";
	}
}
