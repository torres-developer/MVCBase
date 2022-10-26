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
            DB_HOST,
            DB_NAME,
            DB_CHARSET,
            DB_USERNAME,
            DB_PASSWORD
        );

        $controller = "$this->controllersNS\\{$this->request->getController()}";

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

