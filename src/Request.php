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
    private URI $resource;

    private string $controller;
    private string $action;
    private array $parameters;

    private string $protocol;

    private HTTPVerb $method;

    private RequestBody $body;

    private Headers $headers;

    public function __construct(
        URI $resource = new URI("/"),
        HTTPVerb $method = HTTPVerb::GET,
        RequestBody $body = new RequestBody(null),
        Headers $headers = new Headers()
    ) {
        $this->resource = $resource;

        $this->method = $method;

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

    public function getProtocolVersion(): string
    {
        $matches = [];

        preg_match("/(?<v>\d+\.\d+)$/", $this->protocol, $matches);

        return $matches["v"];
    }

    public function withProtocolVersion($version): static
    {
        if (!is_string($version)) {
            throw new \InvalidArgumentException();
        }

        if (!preg_match("/^\d+\.\d+$/", $this->protocol)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;

        if (
            preg_replace("/\d+\.\d+$/", $version, $req->protocol)
            || !str_ends_with($req->protocol, $version)
        ) {
            throw new \RuntimeException();
        }

        return $req;
    }

    public function getHeaders(): array
    {
        return $this->headers->toArray();
    }

    public function hasHeader($name): bool
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        $keys = array_keys($this->getHeaders());

        foreach ($keys as $header) {
            if (strcmp($name, $header) === 0) {
                return true;
            }
        }

        return false;
    }

    public function getHeader($name): array
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        return array_filter(
            $this->headers->toArray(),
            fn($i) => strcmp($name, $i),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function getHeaderLine($name): string
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        return "$name: " . implode(
            ", ",
            array_merge(...$this->getHeader($name))
        );
    }

    public function withHeader($name, $value): static
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException();
        }

        if (is_string($value)) {
            $value = [$value];
        }

        $req = clone $this;

        unset($req->headers->$name);

        foreach ($value as $i) {
            $req->headers->$name = $i;
        }

        return $req;
    }

    public function withAddedHeader($name, $value): static
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException();
        }

        if (is_string($value)) {
            $value = [$value];
        }

        $req = clone $this;

        foreach ($value as $i) {
            $req->headers->$name = $i;
        }

        return $req;
    }


    public function withoutHeader($name): static
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;

        unset($req->headers->$name);

        return $req;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $req = clone $this;

        $req->body = $body;

        return $req;
    }

    public function getRequestTarget(): string
    {
        return $this->resource->getPath();
    }

    public function withRequestTarget($requestTarget): static
    {
        $req = clone $this;

        if ($requestTarget === "*") {
            if ($this->method !== HTTPVerb::OPTIONS) {
                throw new \InvalidArgumentException();
            }


        }

        $req->resource = $req->resource->withPath($requestTarget);

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
