<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

final class Headers
{
    private array $headers;

    public function __construct()
    {
        $this->headers = [];
    }

    public function __set(string $name, mixed $value): void
    {
        $name = mb_strtoupper($name);

        if ($this->__isset($name)) {
            array_push($this->headers[$name], $value);
        } else {
            $this->headers[$name] = [$value];
        }
    }

    public function __get(string $name): mixed
    {
        $name = mb_strtoupper($name);

        return $this->headers[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        $name = mb_strtoupper($name);

        return isset($this->headers[$name]);
    }

    public function __unset(string $name): void
    {
        $name = mb_strtoupper($name);

        unset($this->headers[$name]);
    }

    public function toArray(): array
    {
        return $this->headers;
    }
}
