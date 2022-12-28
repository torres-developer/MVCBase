<?php

/**
 *    MVCBase - A base for a MVC.
 *    Copyright (C) 2022  João Torres
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package TorresDeveloper\\MVC
 * @author João Torres <torres.dev@disroot.org>
 * @copyright Copyright (C) 2022 João Torres
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 * @license https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 * @since 1.0.0
 * @version 1.0.0
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

/**
 * The basic system for the MVC.
 *
 * @author João Torres <torres.dev@disroot.org>
 *
 * @since 1.0.0
 * @version 1.0.0
 */
final class MVC
{
    /**
     * @var string An subnamespace on where to search the Controller.
     *
     * @see Controller
     */
    public const CONTROLLERS_SUBNAMESPACE_NAME = "\\Controllers";

    /**
     * @var \TorresDeveloper\MVC\ServerRequest The HTTP request that the server recieved
     */
    private ServerRequest $request;

    /**
     * @param string $ns The namespace to find the Controller. It will search the Controller at the subnamespace defined
     *                   in {@see MVC::CONTROLLERS_SUBNAMESPACE_NAME}.
     *
     * @see Controller
     *
     * @uses MVC::CONTROLLERS_SUBNAMESPACE_NAME
     * @uses MVC::createRequest()
     *
     * @throws \RuntimeException In case of a bad dsn string for the \PDO __construct.
     */
    public function __construct(string $ns)
    {
        $ns .= static::CONTROLLERS_SUBNAMESPACE_NAME;

        try {
            $this->request = $this->createRequest();
        } catch (\ValueError $e) {
            http_response_code(405);

            header("Allow: " . implode(", ", array_map(
                fn($i) => $i->value,
                HTTPVerb::cases()
            )));

            echo $e->getMessage() . PHP_EOL;

            exit(1);
        }

        var_dump($this->request);
        $controller = "$ns\\{$this->request->getController()}";

        if (!class_exists($controller)) {
            http_response_code(404);
            exit;
        }

        $this->deploy($controller, new Response(200));
    }

    private function createRequest(): ServerRequest
    {
        $uri = (($_SERVER["HTTP_HOST"] ?? "") . ($_SERVER["REQUEST_URI"] ?? ""));

        $uri = new URI($uri ?: $_GET[PATH_SEARCH_PARAMETER] ?? null, false);

        $method = HTTPVerb::from($_SERVER["REQUEST_METHOD"]);

        $body = new MessageBody(new \SplFileObject("php://input"));

        $serverParams = [
            "REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"] ?? null,
            "REMOTE_PORT" => $_SERVER["REMOTE_PORT"] ?? null,
            "SERVER_SOFTWARE" => $_SERVER["SERVER_SOFTWARE"] ?? null,
            "SERVER_PROTOCOL" => $_SERVER["SERVER_PROTOCOL"] ?? null,
            "SERVER_NAME" => $_SERVER["SERVER_NAME"] ?? null,
            "SERVER_PORT" => $_SERVER["SERVER_PORT"] ?? null,
            "REQUEST_URI" => $_SERVER["REQUEST_URI"] ?? null,
            "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"] ?? null,
            "QUERY_STRING" => $_SERVER["QUERY_STRING"] ?? null,
            "REQUEST_TIME_FLOAT" => $_SERVER["REQUEST_TIME_FLOAT"] ?? null,
            "REQUEST_TIME" => $_SERVER["REQUEST_TIME"] ?? null,
        ];

        $req = new ServerRequest(
            $uri,
            $method,
            $body,
            $this->createHeaders(),
            $serverParams
        );

        $req = $req->withCookieParams($_COOKIE)
            ->withUploadedFiles(array_map(File::from_FILES(...), $_FILES));

        if ($body->getContents()) {
            $req = $req->withParsedBody(MIME::parseFromMIME(
                $body,
                $_SERVER["HTTP_CONTENT_TYPE"] ?? "",
                $method
            ));
        }

        return $req;
    }

    private function createHeaders(): Headers
    {
        $headers = new Headers();

        $keys = array_keys($_SERVER);
        foreach ($keys as $k) {
            if (str_starts_with($k, "HTTP_")) {
                $header = strtr(mb_substr($k, 5), "_", "-");

                $headers->$header = $_SERVER[$k];
            }
        }

        return $headers;
    }

    public function deploy(string $controller, Response $response): Response
    {
        $class = new \ReflectionClass($controller::class);

        $action = $this->request->getAction();

        $method = $class->getMethod($action);

        if (!$method->getAttributes(Route::class)) {
            http_response_code(404);
            exit;
        }

        $returnType = $method->getReturnType();

        if (!($returnType instanceof \ReflectionNamedType) || $returnType->allowsNull()) {
            exit;
        }

        $returnType = $returnType->getName();

        if ($returnType !== Controller::class) {
            exit;
        }

        if ($returnType === Controller::class) {
            return $this->deploy(
                (new $controller())
                    ->{$action}($response, $this->request->getParameters())::class,
                $response
            );
        }

        $db = $class->getAttributes(DB::class);

        if ($methodDB = $method->getAttributes(DB::class)) {
            $db = $methodDB;
        }

        try {
            (new $controller(array_pop($db)->newInstance()->getProxy()))
                ->{$action}($response, $this->request->getParameters());
        } catch (\Error $e) {
            http_response_code(404);
            exit($e);
        }
    }
}
