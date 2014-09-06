<?php



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

        $server = $this->createServerValues($headers);

        $method = strtoupper($headers['METHOD']);

        // query string
        parse_str($headers['QUERY'], $query);

        // Cookies
        $cookies = array();
        if(isset($headers['cookie'])) {
            $cookiesData = $this->cookieParser->parseCookie($headers['cookie']);
            if($cookiesData) {
                $cookies = $cookiesData['cookies'];
            }
        }

        // TODO Handle POST Values completely
        $post = array();
        if('POST' == $headers['METHOD']) {
            // This does not yet work for file uploads :(
            parse_str($body, $post);
        }

        // TODO Handle FILES Values as far as possible.
        $files = array();

        return new Request($uuid, $listener, $method, $path, $body, $query, $post, $files, $cookies, $headers, $server);
    }

    public function transformResponseToMessage(Response $response)
    {
        $listeners = $this->netstringEncoder->encode(implode(' ', $response->getListeners()));

        $content = $response->getContent();
        $headers = $response->getHeaders();

        // Ensure content-length is set, otherwise Mongrel2 will ignore our request.
        if(!isset($headers['content-length'][0])) {
            $headers['content-length'][] = strlen($content);
        }

        $headersString = '';
        foreach ($headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $headersString .= sprintf("%s %s\r\n", $name.':', $value);
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
                // These headers are given by mongrel2 only, no standard in sight here.
                $server['MONGREL2_'.$key] = $value;
            }else{
                $key = strtoupper(str_replace('-', '_', $key));
                $server['HTTP_'.$key] = $value;
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

        return $server;

        /*
        $serverProtocol = (isset($headers['VERSION'])) ? $headers['VERSION'] : 'HTTP/1.0';
        list($serverName, $serverPort) = explode(':', $headers['host']);
        $requestUri = $headers['URI'];
        $requestMethod = $headers['METHOD'];
        $scriptName = $phpSelf = $headers['PATH'];
        $httpHost = $headers['host'];
        $httpUserAgent = ($headers['user-agent']) ? $headers['user-agent'] : null;
        $httpAccept = ($headers['accept']) ? $headers['accept'] : null;
        $httpAcceptLanguage = ($headers['accept-language']) ? $headers['accept-language'] : null;
        $httpAcceptEncoding = ($headers['accept-encoding']) ? $headers['accept-encoding'] : null;
        $httpConnection = ($headers['connection']) ? $headers['connection'] : null;

        // Predefine common $_SERVER values if possible, taking a php internal server as template.
        return array (
            //'DOCUMENT_ROOT' => '/var/www',
            //'REMOTE_ADDR' => '127.0.0.1', // Will be given by mongrel2
            //'REMOTE_PORT' => '45433', // not given by mongrel2
            //'SERVER_SOFTWARE' => 'PHP 5.5.9-1ubuntu4.3 Development Server',
            'SERVER_PROTOCOL' => $serverProtocol,
            'SERVER_NAME' => $serverName,
            'SERVER_PORT' => $serverPort,
            'REQUEST_URI' => $requestUri,
            'REQUEST_METHOD' => $requestMethod,
            'SCRIPT_NAME' => $scriptName,
            //'SCRIPT_FILENAME' => '/var/www/index.php',
            'PHP_SELF' => $phpSelf,
            'HTTP_HOST' => $httpHost,
            'HTTP_USER_AGENT' => $httpUserAgent,
            'HTTP_ACCEPT' => $httpAccept,
            'HTTP_ACCEPT_LANGUAGE' => $httpAcceptLanguage,
            'HTTP_ACCEPT_ENCODING' => $httpAcceptEncoding,
            'HTTP_CONNECTION' => $httpConnection,
            //'HTTP_CACHE_CONTROL' => 'max-age=0', // Somehow this header does not seem to be provided by mongrel2 (?)
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_TIME' => time(),
        );
        */
    }
}
