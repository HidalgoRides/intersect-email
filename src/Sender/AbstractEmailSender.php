<?php

namespace Intersect\Email\Sender;

use Intersect\Email\EmailHeaders;

abstract class AbstractEmailSender implements EmailSender {

    public function __construct() {}

    protected function verifyHeaders(EmailHeaders $headers, array $headersToValidate = [])
    {
        foreach ($headersToValidate as $headerToValidate)
        {
            if (is_null($headers->getHeader($headerToValidate)))
            {
                throw new \Exception('Header value missing: ' . $headerToValidate);
            }
        }
    }

}