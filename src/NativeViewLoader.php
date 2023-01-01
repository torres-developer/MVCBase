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
 * NativeViewLoader
 *
 * @author João Torres <torres.dev@disroot.org>
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class NativeViewLoader extends ViewLoader
{
    public function load(
        string $template,
        iterable $data = [],
        bool $cache = true
    ): StreamInterface {
        if ($cached = $this->findInCache($template, $data)) {
            return $cached;
        }

        foreach ($data as $k => $v) {
            $$k = $v;
        }

        $render = $this->render(...);

        $templateFile = $this->findTemplate($template);

        if ($templateFile === null) {
            throw new \RuntimeException("No template found");
        }

        ob_start();

        require $templateFile;

        $buffer = ob_get_clean();

        if ($buffer === false) {
            throw new \RuntimeException("Could not generate a buffer");
        }

        $message = new MessageBody($buffer);

        if ($cache) {
            $this->cache($message, $template, $data);
        }

        return $message;
    }

    protected function render(string $template): string
    {
        $templateFile = $this->findTemplate($template);

        if ($templateFile === null) {
            throw new \RuntimeException("No template found");
        }

        ob_start();

        require $templateFile;

        $buffer = ob_get_clean();

        if ($buffer === false) {
            throw new \RuntimeException("Could not generate a buffer");
        }

        return $buffer;
    }

    public function findTemplate(string $template): ?string
    {
        $template = trim($template, DIRECTORY_SEPARATOR);

        function find(iterable $dirs, string $template): ?string
        {
            $newDirs = [];

            foreach ($dirs as $path) {
                foreach ($path as $file) {
                    $name = $file->getFilename();
                    $pathName = $file->getPathname();

                    if ($name === "." || $name === "..") {
                        continue;
                    }

                    if ($file->isDir()) {
                        $newDirs[] = new \DirectoryIterator($pathName);
                        continue;
                    }

                    if ($file->isFile() && $file->getExtension() === "php") {
                        if (
                            str_ends_with($pathName, $template)
                            || str_ends_with($pathName, "$template.php")
                        ) {
                            return $pathName;
                        }
                    }
                }
            }

            return $newDirs ? find($newDirs, $template) : null;
        }

        return find($this->templates, $template);
    }

    public function cache(
        StreamInterface $body,
        string $template,
        iterable $data
    ): void {
    }

    public function findInCache(
        string $template,
        iterable $data
    ): ?StreamInterface {
        return null;
    }
}
