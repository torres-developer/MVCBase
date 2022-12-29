<?php

/**
 *
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    protected readonly ServerRequestInterface $req;
    protected readonly ResponseInterface $res;

    protected readonly Connection $db;

    public function __construct(
        ServerRequestInterface $req,
        ResponseInterface $res
    ) {
        $this->req = $req;
        $this->res = $res;
    }

    final protected function getQueryBuilder(): AbstractQueryBuilder
    {
        if (!isset($this->db)) {
            throw new \RuntimeException();
        }

        return $this->db->getBuider();
    }

    public function setDB(Connection $db): void
    {
        $this->db = $db;
    }
}
