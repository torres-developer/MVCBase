<?php

/**
 *    MVCBase - A base for a MVC.
 *    Copyright (C) 2022  João Torres
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package TorresDeveloper\\MVC
 * @author João Torres <torres.dev@disroot.org>
 * @copyright Copyright (C) 2022 João Torres
 * @license https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 * @license https://opensource.org/licenses/AGPL-3.0 GNU Affero General Public License version 3
 *
 * @since 1.0.0
 * @version 1.0.0
 */

declare(strict_types=1);

namespace TorresDeveloper\MVC;

use Psr\Http\Message\StreamInterface;

/**
 * ViewLoader
 *
 * @author João Torres <torres.dev@disroot.org>
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class ViewLoader
{
    /** @var \DirectoryIterator[] */
    protected array $templates;

    protected ?string $cache;

    final public function __construct(
        string|iterable $paths = [],
        ?string $cache = null
    ) {
        $this->templates = [];

        $this->addPath($paths);

        $this->cache = $cache;

        $this->init();
    }

    protected function init(): void
    {
    }

    abstract public function load(
        string $template,
        iterable $data = [],
        bool $cache = true
    ): StreamInterface;

    abstract public function render(string $template): void;

    abstract public function findTemplate(string $template): ?string;

    abstract public function cache(
        StreamInterface $body,
        string $template,
        iterable $data
    ): void;

    abstract public function findInCache(
        string $template,
        iterable $data
    ): ?StreamInterface;

    final public function addPath(
        string|\DirectoryIterator|iterable $paths
    ): void {
        if (!is_iterable($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $path) {
            if (!is_string($path) && !($path instanceof \DirectoryIterator)) {
                throw new \InvalidArgumentException(
                    "\$templates must be of type string or \Directory"
                );
            }

            if (is_string($path)) {
                $dir = new \DirectoryIterator($path);
            }

            $this->templates[] = $dir;
        }
    }

    final public function sortPaths(callable $test): void
    {
        $len = count($this->templates);

        do {
            $newLen = 0;

            for ($i = 1; $i <= $len; $i++) {
                if ($test($this->templates[$i], $this->templates[$i - 1])) {
                    swap($this->templates[$i], $this->templates[$i - 1]);
                    $newLen = $i;
                }
            }

            $len = $newLen;
        } while ($len > 1);

        function swap(string &$x, string &$y): void
        {
            $temp = $x;
            $x = $y;
            $y = $temp;
        }
    }
}
