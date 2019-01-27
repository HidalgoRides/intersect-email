<?php

namespace Intersect\Email\Sender;

use Http\Adapter\Guzzle6\Client;
use Intersect\Email\EmailHeaders;
use Mailgun\Mailgun;

/**
 * composer dependency requirements
 * "mailgun/mailgun-php": "^2.6",
 * "guzzlehttp/psr7": "^1.4",
 * "php-http/guzzle6-adapter": "^1.1",
 */

class MailGunEmailSender extends AbstractEmailSender {

    private $mailGunDomain;
    private $mailGunInstance;

    public function __construct($domain, $apiKey)
    {
        parent::__construct();

        $this->mailGunDomain = $domain;

        $this->mailGunInstance = new Mailgun($apiKey, new Client());
    }

    public function send($recipient, $subject, $message, EmailHeaders $headers = null, $extraData = [])
    {
        $headers = (is_null($headers) ? new EmailHeaders() : $headers);

        $this->verifyHeaders($headers, ['From']);

        $emailDetails = [];
        $emailDetails['from'] = $headers->getHeaderValue('From');
        $emailDetails['to'] = $recipient;
        $emailDetails['subject'] = $subject;
        $emailDetails['html'] = $message;

        $ccHeaderValue = $headers->getHeaderValue('Cc');
        $bccHeaderValue = $headers->getHeaderValue('Bcc');

        if (!is_null($ccHeaderValue))
        {
            $emailDetails['cc'] = $ccHeaderValue;
        }

        if (!is_null($bccHeaderValue))
        {
            $emailDetails['bcc'] = $bccHeaderValue;
        }

        $this->mailGunInstance->sendMessage($this->mailGunDomain, $emailDetails, $extraData);
    }

}