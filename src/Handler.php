<?php

namespace h4cc\Mongrel2;

/**
 * Facade for Transport and MessageParser.
 *
 * Here you can receive and send as you like.
 */
class Handler
{
    private $transport;
    private $messageParser;

    public function __construct(Transport $transport, MessageParser $messageParser = null)
    {
        $this->transport = $transport;
        $this->messageParser = ($messageParser) ? $messageParser : new MessageParser();
    }

    /**
     * @return Request
     */
    public function receiveRequest()
    {
        $message = $this->transport->getReceiveSocket()->recv();

        $request = $this->messageParser->transformMessageToRequest($message);

        return $request;
    }

    public function sendResponse(Response $response)
    {
        $message = $this->messageParser->transformResponseToMessage($response);

        $this->transport->getSendSocket()->send($message);
    }
} 