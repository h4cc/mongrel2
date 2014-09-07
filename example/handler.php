<?php

use h4cc\Mongrel2\Handler;
use h4cc\Mongrel2\Transport;
use h4cc\Mongrel2\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$recv = 'tcp://127.0.0.1:9997';
$send = 'tcp://127.0.0.1:9996';

$handler = new Handler(new Transport($recv, $send));

while (true) {
    $request = $handler->receiveRequest();

    $response = new Response($request->getUuid(), [$request->getListener()]);

    $response->setContent('<pre>
Request: ' . print_r($request, true) . '
Response:' . print_r($response, true) . '
    </pre>');

    $handler->sendResponse($response);
}
