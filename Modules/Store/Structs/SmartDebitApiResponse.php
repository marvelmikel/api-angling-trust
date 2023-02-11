<?php

namespace Modules\Store\Structs;

use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class SmartDebitApiResponse
{
    private $raw;
    private $parsed;

    public function __construct(ResponseInterface $response)
    {
        $this->raw = (string) $response->getBody();
        $this->parsed = (array) new SimpleXMLElement($this->raw);
    }

    public function success()
    {
        return !$this->has_errors();
    }

    public function has_errors()
    {
        return isset($this->parsed['error']);
    }

    public function errors()
    {
        if (isset($this->parsed['error'])) {
            return $this->parsed['error'];
        }

        return null;
    }

    public function raw()
    {
        return $this->raw;
    }

    public function parsed()
    {
        return $this->parsed;
    }

    public function clean()
    {
        return json_decode(
            json_encode($this->parsed), true
        );
    }
}
