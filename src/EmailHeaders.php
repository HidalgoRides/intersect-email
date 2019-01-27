<?php

namespace Intersect\Email;

use Intersect\Email\EmailHeader;

class EmailHeaders {

    /** @var EmailHeader[] */
    private $headers = [];

    public function __construct() {}

    /**
     * @return EmailHeader[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function addHeader(EmailHeader $emailHeader)
    {
        $this->headers[$emailHeader->getName()] = $emailHeader;
    }

    /**
     * @param $name
     * @return EmailHeader|null
     */
    public function getHeader($name)
    {
        return (array_key_exists($name, $this->headers) ? $this->headers[$name] : null);
    }

    public function getHeaderValue($name)
    {
        $value = null;
        $header = $this->getHeader($name);

        if (!is_null($header))
        {
            $value = $header->getValue();
        }

        return $value;
    }

    public function getAsString($lineSeparator = "\r\n")
    {
        if (is_null($this->headers) || count($this->headers) == 0)
        {
            return '';
        }

        $headers = [];

        /** @var EmailHeader $header */
        foreach ($this->headers as $header)
        {
            $headers[] = $header->getName() . ': ' . trim($header->getValue());
        }

        return implode($lineSeparator, $headers);
    }

}