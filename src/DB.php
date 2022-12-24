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

use TorresDeveloper\PdoWrapperAPI\Core\Connection;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class DB
{
    private readonly static array $credentials;

    private readonly string $id;

    public function __construct(string $id = DEFAULT_DB)
    {
        $this->id = $id;
    }

    public function getProxy(): ?Connection
    {
        if (!isset(static::$credentials)) {
            static::$credentials = require_once(DB_CONF);
        }

        $db = static::$credentials[$this->id];

        return new $db["class"]($db[$dsn]);
    }
}
