<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

use TorresDeveloper\HTTPMessage\HTTPVerb;
use TorresDeveloper\HTTPMessage\Stream;

final class MIME
{
    private function __construct()
    {
    }

    public static function parseFromMIME(
        Stream $body,
        string $mime,
        HTTPVerb $method
    ): array|object|null {
        $content = $body->getContents();
        $body = new Stream($content);
        $parsedBody = null;

        switch ($mime) {
            case "application/x-www-form-urlencoded":
            case "multipart/form-data":
                if ($method === HTTPVerb::POST) {
                    $parsedBody = $_POST;
                } else {
                    $parsedBody = [];
                    parse_str($content, $parsedBody);
                }

                break;
            case "application/json":
                $parsedBody = json_decode($content);

                break;
            case "text/csv":
                $file = (new Stream($content))
                    ->detach();

                $parsedBody = [];

                while (!$file->eof()) {
                    $parsedBody[] = $file->fgetcsv();
                }
            case "text/tab-separeted-values":
                $file = (new Stream($content))
                    ->detach();

                $parsedBody = [];

                while (!$file->eof()) {
                    $parsedBody[] = $file->fgetcsv("\t");
                }
        }

        return $parsedBody;
    }
}
