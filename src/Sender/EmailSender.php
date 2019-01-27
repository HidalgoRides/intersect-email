<?php

namespace Intersect\Email\Sender;

use Intersect\Email\EmailHeaders;

interface EmailSender {

    public function send($recipient, $subject, $message, EmailHeaders $headers = null, $extraData = []);

}