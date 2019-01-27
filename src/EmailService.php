<?php

namespace Intersect\Email;

use Intersect\Email\AbstractEmail;
use Intersect\Email\Sender\EmailSender;

class EmailService {

    /** @var EmailSender */
    private $emailSender;

    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param AbstractEmail $email
     * @throws \Exception
     */
    public function send(AbstractEmail $email)
    {
        if (is_null($email->getFromEmail()) || trim($email->getFromEmail()) == '')
        {
            throw new \Exception('Email cannot be sent because the From email is not set');
        }

        if (is_null($email->getRecipient()))
        {
            throw new \Exception('Email cannot be sent because a recipient has not be added');
        }

        $this->emailSender->send($email->getRecipient(), $email->getSubject(), $email->getMessage(), $email->getHeaders(), $email->getExtraData());
    }

}
