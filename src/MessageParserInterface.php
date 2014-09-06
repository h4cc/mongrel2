<?php



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
