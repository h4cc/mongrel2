<?php

namespace h4cc\Mongrel2;

/**
 * Mongrel2 Request data.
 */
class Request
{
    private $uuid;
    private $listener;
    private $path;
    private $method;
    private $query;
    private $post;
    private $files;
    private $cookies;
    private $headers;
    private $server;
    private $body;

    function __construct($uuid, $listener, $method, $path, $body, array $query, array $post, array $files, array $cookies, array $headers, array $server)
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

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post)
    {
        $this->post = $post;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
    public function getListener()
    {
        return $this->listener;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}