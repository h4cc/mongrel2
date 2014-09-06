<?php



namespace h4cc\Mongrel2;

interface TransportInterface
{
    /**
     * The ZeroMQ Socket for sending messages with our responses.
     *
     * @return \ZMQSocket
     */
    public function getSendSocket();

    /**
     * The ZeroMQ Socket for recieving messages.
     *
     * @return \ZMQSocket
     */
    public function getReceiveSocket();
}
