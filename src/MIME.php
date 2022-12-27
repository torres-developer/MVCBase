<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

final class MIME
{
    private function __construct()
    {
    }

    public static function parseFromMIME(
        MessageBody $body,
        string $mime,
        HTTPVerb $method
    ): array|object|null {
        $content = $body->getContents();
        $body = new MessageBody($content);
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
                $file = (new MessageBody($content))
                    ->detach();

                $parsedBody = [];

                while (!$file->eof()) {
                    $parsedBody[] = $file->fgetcsv();
                }
            case "text/tab-separeted-values":
                $file = (new MessageBody($content))
                    ->detach();

                $parsedBody = [];

                while (!$file->eof()) {
                    $parsedBody[] = $file->fgetcsv("\t");
                }
        }

        return $parsedBody;
    }
}
