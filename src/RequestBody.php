<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use Psr\Http\Message\StreamInterface;

final class RequestBody implements StreamInterface
{
    private \SplFileObject|string|null $body;
    private string $extension;

    public function __construct(\SplFileObject|string|null $body)
    {
        $this->body = $body;

        $this->extension = MIME::extensionFromMIME(
            $_SERVER["HTTP_CONTENT_TYPE"] ?? null
        );
    }

    public function __toString(): string
    {
        return $this->body;
    }

    public function close(): void
    {
        
    }

    public function detach()
    {

    }

    public function getSize(): ?int
    {
        return mb_strlen($this->body);
    }

    public function tell(): int
    {
        return 0;
    }

    public function eof(): bool
    {
        return true;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        
    }

    public function rewind()
    {
        
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write($string)
    {
        
    }

    public function isReadable()
    {
        
    }

    public function read($length)
    {
        
    }

    public function getContents()
    {
        
    }

    public function getMetadata($key = null)
    {
        
    }

    public function getBody()
    {
        if (!$this->extension) throw new \Error();

        $extension = $this->extension;

        return $this->$extension();
    }

    public function txt()
    {
        return $this->body;
    }

    public function json()
    {
        return json_decode($this->body);
    }

    public function searchParams()
    {
        parse_str($this->body, $result);

        return $result;
    }

    public function _POST()
    {
        return $_POST;
    }
}

