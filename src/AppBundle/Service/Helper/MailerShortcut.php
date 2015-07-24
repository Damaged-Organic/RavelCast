<?php
// src/AppBundle/Service/Helper/MailerShortcut.php
namespace AppBundle\Service\Helper;

use Swift_Message;

class MailerShortcut
{
    private $_mailer;

    public function __construct($mailer)
    {
        $this->_mailer = $mailer;
    }

    public function sendMail($from, $to, $subject, $body)
    {
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html');

        return ( $this->_mailer->send($message) ) ? TRUE : FALSE;
    }
}