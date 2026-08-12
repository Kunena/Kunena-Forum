<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Email
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

\defined('_JEXEC') or die();

use ErrorException;
use Exception;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailHelper;
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
     * @param   Mail $mail      mail
     * @param   array                 $receivers receivers
     *
     * @return boolean
     * @throws Exception
     * @since Kunena
     */
    public static function send($mail, array $receivers)
    {
        if (isset(static::$mailer_error_status)) {
            // Mailer is broken, so prevent any sending
            Log::add(static::$mailer_error_status->getMessage(), Log::ERROR, 'kunena');
            
            return false;
        }
        
        $config = KunenaFactory::getConfig();
        
        if (!empty($config->emailRecipientCount)) {
            $emailRecipientCount = $config->emailRecipientCount;
        } else {
            $emailRecipientCount = 1;
        }
        
        $chunks = array_chunk($receivers, $emailRecipientCount);
        
        $success = true;
        
        if ($mail instanceof \PHPMailer\PHPMailer\PHPMailer) {
            $mail->SMTPKeepAlive = true;
        }
        
        foreach ($chunks as $emails) {
            $mail->ClearAddresses();
            $mail->addRecipient($emails);            
            
            try {
                $mail->Send();
            } catch (Exception $e) {
                $success = false;
                Log::add($e->getMessage(), Log::ERROR, 'kunena');
            }
        }
        
        if ($mail instanceof \PHPMailer\PHPMailer\PHPMailer) {
            $mail->smtpClose();
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
        if (strpos($errstr, "mail(): Failed to connect to mail server") !== false) {
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
