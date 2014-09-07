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
