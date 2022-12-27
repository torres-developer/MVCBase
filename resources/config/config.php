<?php

/**
 * @author João Torres <torres.dev@disroot.org>
 *
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 *
 * @version 1.0.0
 *
 * Configuration
 *
 * @link https://wiki.owasp.org/index.php/Configuration Best Practices for a configuration
 *
 * ## Best Practices
 * ---
 * Turn off all unnecessary features by default
 */

/**
 * @var string ROOT Project / (root).
 */
define("ROOT", __DIR__ . "/..");

/**
 * @var string PUBLIC The DocumentRoot directory.
 */
define("PUBLIC", ROOT . "/public");

/**
 * @var string URI URI for your website.
 */
define("URI", "http://{$_SERVER["HTTP_HOST"]}/");

define("DB_CONF", __DIR__ . "/databaseConfig.php");
define("DEFAULT_DB", "default");

/**
 * @var bool DEBUG true to show errors, notices, warnings.
 */
define("DEBUG", true);

/**
 * @var int What errors to show.
 */
define("DEBUG_LEVEL", E_ALL);

/**
 * @var bool DEBUG_TRACE true to show the trace of the errors.
 */
define("DEBUG_TRACE", true);

/**
 * @var string CHARSET Default charset.
 */
define("CHARSET", "UTF-8");

/**
 * @var string HOMEPAGE Default Controller.
 */
define("HOMEPAGE", "Home");

/**
 * @var string PATH_SEARCH_PARAMETER Used for routing.
 */
define("PATH_SEARCH_PARAMETER", "path");
