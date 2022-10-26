<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

final class Request
{
    private string $controller;
    private string $action;
    private array $parameters;

    private HTTPVerb $method;

    private RequestBody $body;

    public function __construct(
        ?string $resource = "/",
        HTTPVerb $method = HTTPVerb::GET,
        $body = null
    ) {
        $this->method = $method;

        $this->calcInput($resource);
        $this->calcBody($body);
    }

    private function calcInput(?string $path): void
    {
        $path = $path ?: "/";
        $path = explode(
            "/",
            trim(filter_var($path, FILTER_SANITIZE_URL), "/\//")
        );

        @[$controller, $action] = $path;

        unset($path[0], $path[1]);
        $this->parameters = array_values($path);

        $controller ??= HOMEPAGE;
        $controller = explode("-", $controller);
        $controller = array_map("ucfirst", $controller);
        $this->controller = implode("", $controller) . "Controller";

        $this->action = $action ?? "index";
    }

    private function calcBody($body): void
    {
        $this->body = new RequestBody($this, $body);
    }

    public function getBody(): RequestBody
    {
        return $this->body;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getMethod(): HTTPVerb
    {
        return $this->method;
    }
}
