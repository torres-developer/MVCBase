<?php

/**
 *
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    use ConnectionHolder;

    protected readonly ServerRequestInterface $req;
    protected readonly ResponseInterface $res;

    public function __construct(
        ServerRequestInterface $req,
        ResponseInterface $res
    ) {
        $this->req = $req;
        $this->res = $res;
    }

    public final function setDB(Connection $db): void
    {
        $this->db = $db;
    }

    public final function getResponse(): ResponseInterface
    {
        return $this->res;
    }
}
