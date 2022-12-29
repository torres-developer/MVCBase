<?php

/**
 *
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

use TorresDeveloper\PdoWrapperAPI\Core\AbstractQueryBuilder;
use TorresDeveloper\PdoWrapperAPI\Core\Connection;

/**
 * asdf
 *
 * @author João Torres <torres.dev@disroot.org>
 *
 * @since 1.0.0
 * @version 1.0.0
 */
trait ConnectionHolder
{
    protected readonly Connection $db;

    final protected function getQueryBuilder(): AbstractQueryBuilder
    {
        if (!isset($this->db)) {
            throw new \RuntimeException();
        }

        return $this->db->getBuider();
    }
}
