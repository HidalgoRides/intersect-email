<?php

namespace Intersect\Email\Sender;

use Intersect\Email\EmailHeaders;

class DevEmailSender extends AbstractEmailSender {

    private $indent;
    private $lineBreak;
    private $outputDirectory;
    private $mode;

    public function __construct($outputDirectory, $mode = 'plain', $lineBreak = PHP_EOL)
    {
        $this->outputDirectory = $outputDirectory;
        $this->mode = $mode;
        $this->lineBreak = $lineBreak;
        $this->indent = '   ';

        if (!file_exists($outputDirectory))
        {
            mkdir($outputDirectory, 0777, true);
        }
    }

    public function send($recipient, $subject, $message, EmailHeaders $headers = null, $extraData = [])
    {
        $fileContents = [];
        $filePath = $this->outputDirectory . '/' . time() . '_' . $recipient;

        if ($this->mode == 'plain')
        {
            $this->appendLineToContents($fileContents, 'To:');
            $this->appendLineToContents($fileContents, $recipient, true);
            $this->appendLineToContents($fileContents);

            $this->appendLineToContents($fileContents, 'Subject:');
            $this->appendLineToContents($fileContents, $subject, true);
            $this->appendLineToContents($fileContents);

            $this->appendLineToContents($fileContents, 'Message:');
            $this->appendLineToContents($fileContents, $message, true);
            $this->appendLineToContents($fileContents);

            $this->appendLineToContents($fileContents, 'Headers:');

            if (is_null($headers))
            {
                $this->appendLineToContents($fileContents, 'None', true);
            }
            else
            {
                if ($headers instanceof EmailHeaders)
                {
                    $headers = $headers->getHeaders();
                }
    
                /** @var EmailHeader $header */
                foreach ($headers as $header)
                {
                    $this->appendLineToContents($fileContents, $header->getName() . ': ' . $header->getValue(), true);
                }
            }
    
            $this->appendLineToContents($fileContents);
            $this->appendLineToContents($fileContents, 'Extra Data:');
            
            $this->appendExtraDataToContents($fileContents, $extraData);
            
            $this->appendLineToContents($fileContents);
        }
        else if ($this->mode = 'html')
        {
            $fileContents[] = $message;
            $filePath .= '.html';
        }

        file_put_contents($filePath, implode($this->lineBreak, $fileContents));
    }

    private function appendExtraDataToContents(&$contents, $extraData)
    {
        if (is_null($extraData) || count($extraData) == 0)
        {
            $this->appendLineToContents($contents, 'None', true);
            return;
        }
        
        foreach ($extraData as $key => $value)
        {
            if (is_array($value))
            {
                $this->appendExtraDataToContents($contents, $value);
            }
            else
            {
                $this->appendLineToContents($contents, $key . ': ' . $value, true);
            }
        }
    }

    private function appendLineToContents(&$contents, $line = '', $indent = false)
    {
        $contents[] = ($indent ? $this->indent : '') . $line;
    }

}