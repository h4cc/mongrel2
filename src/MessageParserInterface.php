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
 * Will transform messages from Mongrel2 to objects we can use.
 */
interface MessageParserInterface
{
    /**
     * @param $message string ZeroMQ message.
     * @return Request
     */
    public function transformMessageToRequest($message);

    /**
     * @param Response $response
     * @return string ZeroMQ message.
     */
    public function transformResponseToMessage(Response $response);
}
