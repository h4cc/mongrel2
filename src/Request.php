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
 * Mongrel2 Request data.
 *
 * This objects will try to contain data like the PHP SAPI provides, like $_GET, $_POST, $_SERVER and so on.
 */
class Request
{
    /** @var  string Mongrel2 Request Id. */
    private $uuid;

    /** @var  string The Id of the current client. */
    private $listener;

    /** @var  string Requested path like "/foo/bar.html" */
    private $path;

    /** @var  string HTTP Method like GET or POST. */
    private $method;

    /** @var array $_GET values. */
    private $query;

    /** @var array $_POST values. */
    private $post;

    /** @var array $_FILES values. */
    private $files;

    /** @var array $_COOKIES values. */
    private $cookies;

    /** @var array Unchanged headers send by Mongrel2. */
    private $headers;

    /** @var array $_SERVER values. */
    private $server;

    /** @var  string Body of the HTTP Request. */
    private $body;

    function __construct($uuid, $listener, $method, $path, $body, array $query,
                         array $post, array $files, array $cookies, array $headers, array $server)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->listener = $listener;
        $this->method = $method;
        $this->path = $path;
        $this->server = $server;
        $this->uuid = $uuid;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    /**
     * @return array $_FILES values.
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return array $_COOKIES values.
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return array $_POST values.
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return array $_GET values.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string Body of the HTTP Request.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array Unchanged headers send by Mongrel2.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string The Id of the current client.
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return string HTTP Method like GET or POST.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string Requested path like "/foo/bar.html"
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array $_SERVER values.
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string Mongrel2 Request Id.
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}