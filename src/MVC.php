<?php

/**
 *        MVCBase - A base for a MVC.
 *        Copyright (C) 2022  João Torres
 *
 *        This program is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Affero General Public License as
 *        published by the Free Software Foundation, either version 3 of the
 *        License, or (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU Affero General Public License for more details.
 *
 *        You should have received a copy of the GNU Affero General Public License
 *        along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package TorresDeveloper\\MVC
 * @author João Torres <torres.dev@disroot.org>
 * @copyright Copyright (C) 2022  João Torres
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 * @license https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 * @since 1.0.0
 * @version 1.0.0
 */

namespace TorresDeveloper\MVC;

//use TorresDeveloper\PdoWrapperAPI\{
    //MySQLPDO,
    //Core\PDODataSourceName,
    //Core\PDOCredentials
//};

final class MVC
{
    private Request $request;

    //private MySQLPDO $dbh;

    //public Controller $controller;

    public function __construct(string $ns)
    {
        $ns = "$ns\\Controllers";

        try {
            $this->request = new Request(
                new URI($_GET[PATH_SEARCH_PARAMETER] ?? null),
                HTTPVerb::from($_SERVER["REQUEST_METHOD"]),
                new RequestBody(new \SplFileObject("php://input"))
            );
        } catch (\ValueError $e) {
            http_response_code(405);

            header("Allow: " . implode(", ", array_map(
                fn($i) => $i->value,
                HTTPVerb::cases()
            )));

            echo $e->getMessage() . PHP_EOL;

            exit(1);
        }

        /*
        $this->dbh = MySQLPDO::getInstance(
            new PDODataSourceName([
                "host" => DB_HOST,
                "database" => DB_NAME
            ], new PDOCredentials(
                DB_USERNAME,
                DB_PASSWORD
            ))
        );
         */

        $controller = "$ns\\{$this->request->getController()}";

        if (!class_exists($controller)) {
            http_response_code(404);
            exit;
        }

        $this->deploy($controller);
    }

    public function deploy(string $controller): void
    {
        $class = new \ReflectionClass($controller::class);

        $action = $this->request->getAction();

        $method = $class->getMethod($action);

        if (!$method->getAttributes(Route::class)) {
            http_response_code(404);
            exit;
        }

        $returnType = $method->getReturnType();

        if (
            !($returnType instanceof \ReflectionNamedType)
            || $returnType->allowsNull()
        ) {
            exit;
        }

        $returnType = $returnType->getName();

        if ($returnType !== Controller::class) {
            exit;
        }

        if ($returnType === Controller::class) {
            return $this->deploy((new $controller())
                ->{$action}($this->request->getParameters())::class
            );
        }

        $db = $class->getAttributes(DB::class);

        if ($methodDB = $method->getAttributes(DB::class)) {
            $db = $methodDB;
        }

        try {
            (new $controller(array_pop($db)->newInstance()->getProxy()))
                ->{$action}($this->request->getParameters());
        } catch (\Error $e) {
            http_response_code(404);
            exit($e);
        }
    }
}

