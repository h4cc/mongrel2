<?php



namespace h4cc\Mongrel2;

/**
 * Mongrel2 Response data.
 */
class Response
{
    private $uuid = null;
    private $listeners = array();
    private $httpVersion = '1.0';
    private $statusCode = 200;
    private $reasonPhrase = 'OK';
    private $headers = array();
    private $content = '';

    public function __construct($uuid, array $listeners)
    {
        $this->uuid = $uuid;
        $this->listeners = $listeners;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    public function getListeners()
    {
        return $this->listeners;
    }

    public function setListeners($listeners)
    {
        $this->listeners = $listeners;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }
}
