<?php

/*
 * This file is part of the h4cc/mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\Mongrel2;

/**
 * Facade for Transport and MessageParser.
 *
 * Here you can receive and send as you like.
 */
class Handler implements HandlerInterface
{
    /** @var \h4cc\Mongrel2\Transport  */
    private $transport;

    /** @var \h4cc\Mongrel2\MessageParser  */
    private $messageParser;

    public function __construct(TransportInterface $transport, MessageParserInterface $messageParser = null)
    {
        $this->transport = $transport;
        $this->messageParser = ($messageParser) ? $messageParser : new MessageParser();
    }

    public function receiveRequest($blocking = self::BLOCKING)
    {
        $recvMode = (self::NONBLOCKING == $blocking) ? ZMQ::MODE_DONTWAIT : 0;

        try {
            $message = $this->transport->getReceiveSocket()->recv($recvMode);
        }catch (\ZMQSocketException $exception) {
            trigger_error('h4cc/Mongrel2: '.$exception->getMessage(), E_WARNING);
            $message = false;
        }

        if(!$message) {
            return false;
        }

        $request = $this->messageParser->transformMessageToRequest($message);

        return $request;
    }

    public function sendResponse(Response $response, $blocking = self::BLOCKING)
    {
        $sendMode = (self::NONBLOCKING == $blocking) ? ZMQ::MODE_DONTWAIT : 0;

        $message = $this->messageParser->transformResponseToMessage($response);

        try {
            $messageOrFalse = $this->transport->getSendSocket()->send($message, $sendMode);
        }catch (\ZMQSocketException $exception) {
            trigger_error('h4cc/Mongrel2: '.$exception->getMessage(), E_WARNING);
            $messageOrFalse = false;
        }

        if(!$messageOrFalse) {
            return false;
        }

        return true;
    }
} 