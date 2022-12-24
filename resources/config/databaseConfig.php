<?php

/**
 * @author João Torres <torres.dev@disroot.org>
 *
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 *
 * @version 1.0.0
 *
 * Database configuration
 */

use TorresDeveloper\PdoWrapperAPI\Core\DataSourceName;
use TorresDeveloper\PdoWrapperAPI\Core\Credentials;
use TorresDeveloper\PdoWrapperAPI\MysqlConnection;

$user = "";
$password = "";
$credentials = Credentials::getCredentials($user, $password);

return [
    DEFAULT_DB => [
        "class" => MysqlConnection::class,
        "dsn" => new DataSourceName([
            "host" => "",
            "database" => "",
            "charset" => "utf8mb4"
        ], $credentials)
    ],
    "otherDB" => [
        "class" => MysqlConnection::class,
        "dsn" => new DataSourceName([
            "host" => "example.org",
            "database" => "topSecret"
        ], $credentials)
    ]
];
