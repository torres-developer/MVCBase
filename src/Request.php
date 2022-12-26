<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\{
    RequestInterface,
    StreamInterface,
    UriInterface
};

final class Request implements RequestInterface
{
    use MessageTrait;

    private string $controller;
    private string $action;
    private array $parameters;

    private UriInterface $resource;

    private HTTPVerb $method;

    private string $requestTarget;

    public function __construct(
        UriInterface|string $resource = new URI("/"),
        HTTPVerb|string $method = HTTPVerb::GET,
        StreamInterface|\SplFileObject|string|null $body
            = new RequestBody(null),
        Headers $headers = new Headers()
    ) {
        if (is_string($resource)) {
            $resource = new URI($resource);
        }

        $this->resource = $resource;

        if (is_string($method)) {
            $method = HTTPVerb::from(mb_strtoupper($method));
        }

        $this->method = $method;

        if (!($body instanceof StreamInterface)) {
            $body = new RequestBody($body);
        }

        $this->body = $body;

        $this->headers = $headers;

        $this->findRoute($resource->getPath());

        if (!isset($headers->Host)) {
            $host = $resource->getHost()
                . ((($port = $resource->getPort()) === null) ? "" : ":$port");

            $headers->Host = $host;
        }

        $this->protocol = $_SERVER["SERVER_PROTOCOL"];
    }

    public function getRequestTarget(): string
    {
        if (!isset($this->requestTarget)) {
            $this->requestTarget = ($this->resource->getPath() ?: "")
                . (($query = $this->resource->getQuery()) ? "?$query" : "");
        }

        return $this->requestTarget;
    }

    public function withRequestTarget($requestTarget): static
    {
        if ($requestTarget === "*" && $this->method !== HTTPVerb::OPTIONS) {
            throw new \DomainException();
        }

        $matches = parse_url($requestTarget);

        if (isset($matches["host"], $matches["port"]) && $this->method === HTTPVerb::CONNECT) {
            $requestTarget = "$matches[host]:$matches[port]";
        }

        $req = clone $this;
        $req->requestTarget = $requestTarget ?: "/";

        return $req;
    }

    public function getMethod(): string
    {
        return (string) $this->method;
    }

    public function withMethod($method): static
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;

        try {
            $req->method = HTTPVerb::from($method);
        } catch (\ValueError $e) {
            throw new \InvalidArgumentException(previous: $e);
        }

        return $req;
    }

    public function getUri(): UriInterface
    {
        return $this->resource;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $req = clone $this;

        $host = $uri->getHost();

        if ($host && (!$preserveHost || !isset($req->headers->Host))) {
            $host .= ((($port = $uri->getPort()) === null) ? "" : ":$port");

            unset($req->headers->Host);
            $req->headers->Host = $host;
        }

        $req->resource = $uri;
    }

    public function getMethodHTTPVerb(): HTTPVerb
    {
        return $this->method;
    }

    public function withMethodHTTPVerb(HTTPVerb $method): static
    {
        $uri = clone $this;
        $uri->method = $method;

        return $uri;
    }

    private function findRoute(string $path): void
    {
        $path = $path ?: "/";

        $path = explode(
            "/",
            trim(filter_var($path, FILTER_SANITIZE_URL), "/\//")
        );

        $controller = $path[0] ?? null;
        $action = $path[1] ?? null;

        unset($path[0], $path[1]);

        $this->parameters = array_values($path);

        $controller ??= HOMEPAGE;
        $controller = explode("-", $controller);
        $controller = array_map(ucfirst(...), $controller);
        $this->controller = implode("", $controller) . "Controller";

        $this->action = $action ?? "index";
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
}
