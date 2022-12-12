<?php

namespace Lumie\QuarterlySummary\Request;

class Request
{
    protected string $uri;

    protected array $param;

    protected array $get;

    protected array $post;

    public function __construct(string $uri, array $get = [], array $post = [])
    {
        $this->uri = $uri;
        $this->get = $get;
        $this->post = $post;
    }

    public function get($key)
    {
        return $this->get[$key] ?? null;
    }

    public function post($key)
    {
        return $this->post[$key] ?? null;
    }

    public function param($key)
    {
        return $this->param[$key] ?? null;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function setParameters(array $parameters)
    {
        if (!isset($this->param)) {
            $this->param = $parameters;
        }
    }
}
