<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\{
    ServerRequestInterface,
};

final class ServerRequest extends Request implements ServerRequestInterface
{
    private array $serverParams;
    private array $cookieParams = [];
    private array $queryParams;
    private array $uploadedFiles = [];

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
        $req = clone $this;
        $req->uploadedFiles = [];

        return $req;
    }

    public function getParsedBody(): null|array|object
    {
        return null;
    }

    public function withParsedBody($data)
    {
        
    }

    public function getAttributes()
    {
        
    }

    public function getAttribute($name, $default = null)
    {
        
    }

    public function withAttribute($name, $value)
    {
        
    }

    public function withoutAttribute($name)
    {
        
    }
}
