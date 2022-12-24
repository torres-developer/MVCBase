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
        if ($this->__isset($name)) {
            array_push($this->headers[(string) $name], $value);
        } else {
            $this->headers[(string) $name] = [$value];
        }
    }

    public function __get(string $name): mixed
    {
        //return $this->headers[(string) $name] ?? null;

        if (($header = $this->headers[(string) $name]) === null) {
            return null;
        }

        return $name . ": " . implode(", ", $header);
    }

    public function __isset(string $name): bool
    {
        return isset($this->headers[(string) $name]);
    }

    public function __unset(string $name): void
    {
        unset($this->headers[(string) $name]);
    }

    public function toArray(): array
    {
        return $this->headers;
    }
}
