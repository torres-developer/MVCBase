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
 * @author João Torres <torres.dev@disroot.org>
 * @copyright Copyright (C) 2022  João Torres
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 * @license https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 * @since 1.0.0
 * @version 1.0.0
 */

declare(encoding="UTF-8");
declare(strict_types=1);

if (!__NAMESPACE__) throw new \Exception("Define a PSR-4 namespace");

mb_internal_encoding("UTF-8");

if (ini_set("default_charset", "utf-8") === false)
    throw new \Exception("Could not set default_charset to utf-8, "
    . "please ensure it's set on your system!");

mb_http_output("UTF-8");

require_once __DIR__ . "/../config/config.php";

defined("ROOT") OR exit(1);

session_start();

error_reporting(!DEBUG ? 0 : DEBUG_LEVEL);

require_once __DIR__ . "/../vendor/autoload.php";

new \TorresDeveloper\MVC\MVC(__NAMESPACE__);

