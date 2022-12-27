<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequest extends Request implements ServerRequestInterface
{
    private array $serverParams;
    private array $cookieParams = [];
    private array $queryParams;
    private array $uploadedFiles = [];

    private array|object|null $parsedBody;

    private array $attributes = [];

    public function __construct(
        UriInterface|string $resource = new URI("/"),
        HTTPVerb|string $method = HTTPVerb::GET,
        StreamInterface|\SplFileObject|string|null $body = new MessageBody(null),
        Headers $headers = new Headers(),
        array $serverParams = []
    ) {
        parent::__construct($resource, $method, $body, $headers);

        $this->serverParams = $serverParams;

        $this->findRoute($resource->getPath());
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): static
    {
        $req = clone $this;
        $req->cookieParams = [];

        return $req;
    }

    public function getQueryParams(): array
    {
        if (($queries = $this->queryParams) === null) {
            $queries = [];
            parse_str($this->resource->getQuery(), $queries);
        }

        return $queries;
    }

    public function withQueryParams(array $query): static
    {
        $req = clone $this;
        $req->queryParams = [];

        return $req;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): static
    {
        foreach ($uploadedFiles as $i) {
            if (!($i instanceof UploadedFileInterface)) {
                throw new \InvalidArgumentException();
            }
        }

        $req = clone $this;
        $req->uploadedFiles = [];

        return $req;
    }

    public function getParsedBody(): null|array|object
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): static
    {
        if (!is_array($data) && !is_object($data) && $data !== null) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;
        $req->parsedBody = $data;

        return $req;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null): mixed
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute($name, $value): static
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;
        $req->attributes[$name] = $value;

        return $req;
    }

    public function withoutAttribute($name): static
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;
        unset($req->attributes[$name]);

        return $req;
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
