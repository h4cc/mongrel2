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
 * Handling Sockets to Mongrel.
 */
class Transport implements TransportInterface
{
    /** @var \ZMQContext  */
    private $context;

    /** @var  \ZMQSocket */
    private $receiver;

    /** @var  \ZMQSocket */
    private $sender;

    public function __construct($receiveDSN, $senderDSN, $senderId = null)
    {
        if (!class_exists('ZMQ')) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('missing zmq extension');
            // @codeCoverageIgnoreEnd
        }

        $this->context = new \ZMQContext();

        // receiving socket (sending socket from Mongrel2)
        $this->receiver = $this->context->getSocket(\ZMQ::SOCKET_PULL);
        $this->receiver->connect($receiveDSN);

        // sending socket (receiving socket from Mongrel2)
        $this->sender = $this->context->getSocket(\ZMQ::SOCKET_PUB);
        if($senderId) {
            $this->sender->setSockOpt(\ZMQ::SOCKOPT_IDENTITY, $senderId);
        }
        $this->sender->connect($senderDSN);
    }

    public function getSendSocket()
    {
        return $this->sender;
    }

    public function getReceiveSocket()
    {
        return $this->receiver;
    }
}
