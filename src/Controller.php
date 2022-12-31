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

    protected readonly ViewLoader $viewLoader;

    protected readonly ServerRequestInterface $req;
    protected ResponseInterface $res;

    public function __construct(
        ServerRequestInterface $req,
        ResponseInterface $res
    ) {
        $this->req = $req;
        $this->res = $res;
    }

    final protected function load(
        string $template,
        iterable $data = [],
        bool $cache = true,
        bool $overwrite = false
    ): void {
        if (!isset($this->viewLoader)) {
            throw new \RuntimeException("No loader");
        }

        $this->res = $this->res->withBody(
            $overwrite
                ? $this->viewLoader->load($template, $data, $cache)
                : new MessageBody(
                    (@$this->res->getBody()->getContents() ?? "")
                        . $this->viewLoader->load(
                            $template,
                            $data,
                            $cache
                        )->getContents()
                )
        );
    }

    final public function setDB(Connection $db): void
    {
        $this->db = $db;
    }

    final public function setViewLoader(ViewLoader $viewLoader): void
    {
        $this->viewLoader = $viewLoader;
    }

    final public function getResponse(): ResponseInterface
    {
        return $this->res;
    }
}
