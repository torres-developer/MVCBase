<?php

/**
 *
 */

namespace TorresDeveloper\MVC;

final class MVC
{
    private Request $request;

    private \TorresDeveloper\PdoWrapperAPI\PDO $dbh;

    public string $controllersNS;

    public Controller $controller;

    public function __construct(string $controllersNS)
    {
        $this->controllersNS = "$controllersNS\\Controllers";

        $this->request = new Request(
            $_GET[PATH_SEARCH_PARAMETER] ?? null,
            HTTPVerb::tryFrom($_SERVER["REQUEST_METHOD"]),
            file_get_contents("php://input")
        );

        $this->dbh = \TorresDeveloper\PdoWrapperAPI\PDO::getInstance(
            DB_HOST ?: "127.0.0.1",
            DB_NAME ?: "learnws",
            DB_CHARSET,
            DB_USERNAME ?: "mvcuser",
            DB_PASSWORD ?: "mvc1user!passwd"
        );

        $controller = "$this->controllersNS\\{$this->request->getController()}";

        var_dump(new \Marado\Test\Controllers\TestController());
        var_dump($controller);
        var_dump(class_exists($controller));

        if (!class_exists($controller)) {
            http_response_code(404);
            exit;
        }

        $this->controller = new $controller(
            $this->request->getParameters(),
            $this->request->getMethod(),
            $this->request->getBody()
        );

        $action = $this->request->getAction();

        if (!method_exists($this->controller, $action)) {
            http_response_code(404);
            exit;
        }

        try {
            $this->controller->{$action}($this->request->getParameters());
        } catch (\Error $e) {
            http_response_code(404);
            exit($e);
        }
    }

    public function deploy(): void
    {
        $this->request;
    }
}

