<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\StreamInterface;

final class MessageBody implements StreamInterface
{
    private ?\SplFileObject $body;

    public function __construct(\SplFileObject|string|null $body)
    {
        if (is_string($body)) {
            $text = $body;

            $this->body = new \SplTempFileObject();
            //$this->body = new \SplFileObject("php://temp", "rw+");

            $this->write($text);
        } else {
            $this->body = $body;
        }
    }

    public function __toString(): string
    {
        try {
            return $this->getContents();
        } catch (\Exception) {
            return "";
        }
    }

    public function close(): void
    {
        $this->body = null;
    }

    public function detach(): ?\SplFileObject
    {
        $resource = $this->body;
        $this->body = null;

        return $resource;
    }

    public function getSize(): ?int
    {
        if ($this->body === null) {
            return null;
        }

        try {
            if (($size = $this->body->getSize()) === false) {
                throw new \RuntimeException();
            }
        } catch (\RuntimeException) {
            return null;
        }

        return $size;
    }

    public function tell(): int
    {
        if ($this->body === null) {
            throw new \RuntimeException("Could not tell");
        }

        if (($pos = $this->body->ftell()) === false) {
            throw new \RuntimeException();
        }

        return $pos;
    }

    public function eof(): bool
    {
        return $this->body && $this->body->eof();
    }

    public function isSeekable(): bool
    {
        return $this->body && $this->body->fseek(0, SEEK_CUR) === 0;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!is_int($offset) || !is_int($whence)) {
            throw new \InvalidArgumentException();
        }

        if ($this->body && $this->body->fseek($offset, $whence) === -1) {
            throw new \RuntimeException();
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->body && $this->body->isWritable();
    }

    public function write($string): int
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException();
        }

        if (!$this->body) {
            throw new \RuntimeException();
        }

        return $this->body->fwrite($string) ?: throw new \RuntimeException();
    }

    public function isReadable(): bool
    {
        return $this->body && $this->body->isReadable();
    }

    public function read($length): string
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException();
        }

        if (!$this->body) {
            throw new \RuntimeException();
        }

        return $this->body->fread($length) ?: "";
    }

    public function getContents(): string
    {
        if ($this->body === null) {
            throw new \RuntimeException();
        }

        $pos = $this->tell();

        $this->rewind();

        $contents = "";
        while (!$this->eof()) {
            $contents .= $this->read(64);
        }

        $this->seek($pos);

        return $contents;
    }

    public function getMetadata($key = null): mixed
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException();
        }

        if ($this->body === null) {
            return $key ? null : [];
        }

        $stats = $this->body->fstat();

        return $key ? $stats[$key] : $stats;
    }

    public function __destruct()
    {
        $this->body = null;
    }
}
