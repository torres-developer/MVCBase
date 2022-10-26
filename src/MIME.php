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

    public static function extensionFromMIME(?string $mime): string
    {
        $mime ??= "*/*";

        [$type, $subtype] = explode("/", $mime);

        switch ($type) {
            case "text":
                switch ($subtype) {
                    case "plain":
                        return "txt";
                }
            case "application": {
                switch ($subtype) {
                    case "json":
                        return "json";
                    case "x-www-form-urlencoded":
                        return "_POST";
                }
            }
            case "multipart":
                switch ($subtype) {
                    case "form-data":
                        return "_POST";
                }
        }

        return "";
    }
}

