<?php

namespace Intersect\Email;

use Intersect\Email\EmailHeader;
use Intersect\Email\EmailHeaders;

abstract class AbstractEmail {

    private $recipient;
    private $ccRecipients = [];
    private $bccRecipients = [];
    private $extraData = [];

    public function __construct($recipient = null) 
    {
        $this->recipient = $recipient;
    }

    abstract public function getSubject();
    abstract public function getMessage();
    abstract public function getFromEmail();
    abstract public function getFromName();
    abstract public function getReplyToEmail();

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function addCCRecipient($ccRecipient)
    {
        $this->ccRecipients[] = $ccRecipient;
    }

    public function addCCRecipients(array $ccRecipients = [])
    {
        foreach ($ccRecipients as $ccRecipient)
        {
            $this->addCCRecipient($ccRecipient);
        }
    }

    public function getCCRecipients()
    {
        return $this->ccRecipients;
    }

    public function addBCCRecipient($bccRecipient)
    {
        $this->bccRecipients[] = $bccRecipient;
    }

    public function addBCCRecipients(array $bccRecipients = [])
    {
        foreach ($bccRecipients as $bccRecipient)
        {
            $this->addBCCRecipient($bccRecipient);
        }
    }

    public function getBCCRecipients()
    {
        return $this->bccRecipients;
    }

    public function addExtraData($key, $value)
    {
        $this->extraData[$key] = $value;
    }

    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @return EmailHeaders
     */
    public function getHeaders()
    {
        $fromName = (!is_null($this->getFromName()) && trim($this->getFromName()) != '') ? $this->getFromName() : $this->getFromEmail();
        $replyToEmail = (!is_null($this->getReplyToEmail()) && trim($this->getReplyToEmail()) != '') ? $this->getReplyToEmail() : $this->getFromEmail();

        $headers = new EmailHeaders();

        $headers->addHeader(new EmailHeader('MIME-Version', '1.0'));
        $headers->addHeader(new EmailHeader('Content-type', 'text/html; charset=iso-8859-1'));
        $headers->addHeader(new EmailHeader('From', $fromName . ' <' . $this->getFromEmail() . '>'));
        $headers->addHeader(new EmailHeader('Reply-To', $fromName . ' <' . $replyToEmail . '>'));

        if (count($this->getCCRecipients()) > 0)
        {
            $headers->addHeader(new EmailHeader('Cc', implode(',', $this->getCCRecipients())));
        }
        if (count($this->getBCCRecipients()) > 0)
        {
            $headers->addHeader(new EmailHeader('Bcc', implode(',', $this->getBCCRecipients())));
        }

        $headers->addHeader(new EmailHeader('X-From-Email', $this->getFromEmail()));
        $headers->addHeader(new EmailHeader('X-From-Name', $fromName));
        $headers->addHeader(new EmailHeader('X-Reply-To-Email', $replyToEmail));
        $headers->addHeader(new EmailHeader('X-Reply-To-Name', $fromName));

        return $headers;
    }
}
