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
interface HandlerInterface
{
    const NONBLOCKING = 'nonblocking';
    const BLOCKING = 'blocking';

    /**
     * Will return the next request from mongrel2.
     * By default, this operation will be blocking till a request occures.
     * If RECV_NONBLOCKING is given, it will return immediately returning either a response or FALSE.
     *
     * @param string $blocking
     * @return Request
     */
    public function receiveRequest($blocking = self::BLOCKING);

    /**
     * Will send a response back to mongrel2.
     * This operation can block by default.
     * If RECV_NONBLOCKING is given, it will return immediately returning either TRUE if send or FALSE if not.
     *
     * @param Response $response
     * @param string $blocking
     * @return void
     */
    public function sendResponse(Response $response, $blocking = self::BLOCKING);
} 