<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

final class RequestBody
{
    private $request;
    private $body;
    private string $extension;

    public function __construct(Request $request, $body)
    {
        $this->request = $request;
        $this->body = $body;

        $this->extension = MIME::extensionFromMIME(
            $_SERVER["HTTP_CONTENT_TYPE"] ?? null
        );
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

