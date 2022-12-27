<?php

/**
 *
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

use TorresDeveloper\PdoWrapperAPI\Core\AbstractQueryBuilder;
use TorresDeveloper\PdoWrapperAPI\Core\Connection;

/**
 * Controller
 *
 * @author João Torres <torres.dev@disroot.org>
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class Controller
{
    protected readonly ?Connection $db;

    public function __construct(?Connection $db = null)
    {
        $this->db = $db;
    }

    final protected function getQueryBuilder(): AbstractQueryBuilder
    {
        if (!isset($this->db)) {
            throw new \RuntimeException();
        }

        return $this->db->getBuider();
    }
}
