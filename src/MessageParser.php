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

use Guzzle\Parser\Cookie\CookieParser;

class MessageParser implements MessageParserInterface
{
    private $netstringDecoder;
    private $netstringEncoder;
    private $cookieParser;

    public function __construct()
    {
        $this->netstringDecoder = new \TNetstring_Decoder();
        $this->netstringEncoder = new \TNetstring_Encoder();
        $this->cookieParser = new CookieParser();
    }

    public function transformMessageToRequest($message)
    {
        list($uuid, $listener, $path, $remaining) = explode(' ', $message, 4);

        list($headers, $body) = $this->netstringDecoder->decode($remaining);
        $headers = json_decode($headers, true);

        $method = $headers['METHOD'];

        // Parse query string.
        parse_str(($headers['QUERY']) ? $headers['QUERY'] : '', $query);

        $server = $this->createServerValues($headers);

        // Cookies
        $cookies = array();
        if (isset($headers['cookie'])) {
            $cookiesData = $this->cookieParser->parseCookie($headers['cookie']);
            if ($cookiesData) {
                $cookies = $cookiesData['cookies'];
            }
        }

        // TODO Handle POST Values completely
        $post = array();
        if ('POST' == $headers['METHOD']) {
            // This does not yet work for file uploads :(
            parse_str($body, $post);
        }

        // TODO Handle FILES Values as far as possible.
        $files = array();

        return new Request(
            $uuid, $listener, $method, $path, $body, $query,
            $post, $files, $cookies, $headers, $server
        );
    }

    public function transformResponseToMessage(Response $response)
    {
        $listeners = $this->netstringEncoder->encode(implode(' ', $response->getListeners()));

        $content = $response->getContent();
        $headers = $response->getHeaders();

        // Ensure content-length is set, otherwise Mongrel2 will ignore our request.
        if (!isset($headers['content-length'][0])) {
            $headers['content-length'][] = strlen($content);
        }

        $headersString = '';
        foreach ($headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $headersString .= sprintf("%s %s\r\n", $name . ':', $value);
            }
        }

        // Building our own HTTP Response.
        $message = sprintf("%s %s HTTP/%s %d %s\r\n%s\r\n%s\r\n",
            $response->getUuid(),
            $listeners,
            $response->getHttpVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $headersString,
            $content
        );

        return $message;
    }

    private function createServerValues(array $headers)
    {
        $server = [];

        foreach ($headers as $key => $value) {
            // Need to remove underscores, because they are not "uppercase".
            if (ctype_upper(str_replace('_', '', $key))) {
                // These headers are given by mongrel2 only, no standard in sight here (?).
                $server['MONGREL2_' . $key] = $value;
            } else {
                // Creating PHP SAPI like headers.
                $key = strtoupper(str_replace('-', '_', $key));
                $server['HTTP_' . $key] = $value;
            }
        }

        // Adding some PHP SAPI values
        list($serverName, $serverPort) = explode(':', $headers['host']);
        $server['SERVER_NAME'] = $serverName;
        $server['SERVER_PORT'] = $serverPort;
        $server['SERVER_PROTOCOL'] = (isset($headers['VERSION'])) ? $headers['VERSION'] : 'HTTP/1.0';
        $server['REQUEST_URI'] = $headers['URI'];
        $server['REQUEST_METHOD'] = $headers['METHOD'];
        $server['SCRIPT_NAME'] = $headers['PATH'];
        $server['PHP_SELF'] = $headers['PATH'];
        $server['REQUEST_TIME_FLOAT'] = microtime(true);
        $server['REQUEST_TIME'] = time();

        // Some values are not given by Mongrel2, which would be given by Apache2 for example:
        // 'DOCUMENT_ROOT' => '/var/www',
        // 'REMOTE_ADDR' => '127.0.0.1',
        // 'REMOTE_PORT' => '45433',
        // 'SERVER_SOFTWARE' => 'PHP 5.5.9-1ubuntu4.3 Development Server',

        return $server;
    }
}
