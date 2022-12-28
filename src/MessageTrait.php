<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    private StreamInterface $body;

    private Headers $headers;

    private string $protocol;

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

        if (!preg_match("/^\d+\.\d+$/", $version)) {
            throw new \InvalidArgumentException();
        }

        $req = clone $this;

        if (preg_replace("/\d+\.\d+$/", $version, $req->protocol) === null) {
            throw new \RuntimeException();
        }

        return $req;
    }

    public function getHeaders(): array
    {
        return $this->headers->toArray();
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers->$name);
    }

    public function getHeader($name): array
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        return $this->headers->$name;
    }

    public function getHeaderLine($name): string
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        return "$name: " . implode($this->headers->$name);
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
            $req->headers->$name = (string) $i;
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
            $req->headers->$name = (string) $i;
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
}
