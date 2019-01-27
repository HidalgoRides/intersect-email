<?php

namespace Intersect\Email\Sender;

use Intersect\Email\EmailHeaders;

/**
 * composer requirements
 * "sendgrid/sendgrid": "7.0.0"
 */

class SendGridEmailSender extends AbstractEmailSender {

    /** @var \SendGrid */
    private $sendGrid;

    public function __construct($apiKey)
    {
        parent::__construct();

        $this->sendGrid = new \SendGrid($apiKey);
    }

    /**
     * @param $recipient
     * @param $subject
     * @param $message
     * @param EmailHeaders $headers
     * @param array $extraData
     * @throws \Exception
     */
    public function send($recipient, $subject, $message, EmailHeaders $headers = null, $extraData = [])
    {
        $headers = (is_null($headers) ? new EmailHeaders() : $headers);

        $this->verifyHeaders($headers, ['X-From-Email']);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($headers->getHeaderValue('X-From-Email'), $headers->getHeaderValue('X-From-Name'));
        $email->setSubject($subject);
        $email->addTo($recipient);
        $email->setReplyTo($headers->getHeaderValue('X-Reply-To-Email'), $headers->getHeaderValue('X-Reply-To-Name'));
        $email->addContent("text/html", $message);

        if (array_key_exists('attachments', $extraData))
        {
            foreach ($extraData['attachments'] as $attachmentDetails)
            {
                $attachment = file_get_contents($attachmentDetails['path']);

                $email->addAttachment(
                    base64_encode($attachment),
                    $attachmentDetails['type'],
                    $attachmentDetails['filename'],
                    $attachmentDetails['disposition'],
                    $attachmentDetails['cid']
                );
            }
        }

        $ccHeaderValue = $headers->getHeaderValue('Cc');
        $bccHeaderValue = $headers->getHeaderValue('Bcc');

        if (!is_null($ccHeaderValue))
        {
            $ccEmails = explode(',', $ccHeaderValue);
            foreach ($ccEmails as $ccEmail)
            {
                $email->addCc($ccEmail);
            }
        }

        if (!is_null($bccHeaderValue))
        {
            $bccEmails = explode(',', $bccHeaderValue);
            foreach ($bccEmails as $bccEmail)
            {
                $email->addBcc($bccEmail);
            }
        }

        $this->sendGrid->send($email);
    }

}
