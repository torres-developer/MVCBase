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

define("ROOT", true);
require_once "../vendor/psr/http-message/src/UriInterface.php";
require_once "./consts.php";

use Psr\Http\Message\UriInterface;

final class URI implements UriInterface
{
    public const DEFAULT_PORTS = [
        "http" => [80],
        "ftp" => [20, 21],
        "ssh" => [22],
        "smtp" => [25],
        "gopher" => [70],
        "pop3" => [110],
        "imap" => [143, 220],
        "irc" => [194],
        "https" => [443],
    ];

    private string $scheme;
    private ?string $user;
    private ?string $password;
    private string $host;
    private ?int $port;
    private string $path;
    private string $query;
    private string $fragment;

    public function __construct(string $uri)
    {
        $uri = trim($uri);

        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw new \DomainException();
        }

        [
            "scheme" => $scheme,
            "user" => $user,
            "pass" => $password,
            "host" => $host,
            "port" => $port,
            "path" => $path,
            "query" => $query,
            "fragment" => $fragment,
        ] = parse_url($uri);

        $this->scheme = $scheme;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
    }

    public function getScheme(): string
    {
        return strtolower($scheme ?? "");
    }

    public function getAuthority(): string
    {
        return (($userInfo = $this->getUserInfo()) === null ? "" : "$userInfo@")
            . $this->getHost()
            . (($port = $this->getPort()) === null ? "" : ":$port");
    }

    public function getUserInfo(): string
    {
        return $this->user
            ? $this->user . ($this->password === null ? "" : ":$this->password")
            : "";
    }

    public function getHost(): string
    {
        return strtolower($host ?? "");
    }

    public function getPort(): ?int
    {
        if (URI::DEFAULT_PORTS[$this->scheme] === null) {
            return $this->port;
        }

        return in_array($this->port, URI::DEFAULT_PORTS[$this->scheme])
            ? null
            : $this->port;
    }

    public function getPath(): string
    {
        return urlencode($this->path);
    }

    public function getQuery(): string
    {
        return "";
    }

    public function getFragment(): string
    {
        return "";
    }

    public function withScheme($scheme): static
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException();
        }

        $uri = clone $this;
        $uri->scheme = $scheme;

        return $uri;
    }

    public function withUserInfo($user, $password = null): static
    {
        if (!is_string($user)) {
            throw new \InvalidArgumentException();
        }

        if (!is_string($password) && $password !== null) {
            throw new \InvalidArgumentException();
        }

        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    public function withHost($host): static
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException();
        }

        $this->host = $host;

        return $this;
    }

    public function withPort($port): static
    {
        if (!is_int($port) && $port !== null) {
            throw new \InvalidArgumentException();
        }

        $this->port = $port;

        return $this;
    }

    public function withPath($path): static
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException();
        }
        
        $this->path = $path;

        return $this;
    }

    public function withQuery($query): static
    {
        if (!is_string($query)) {
            throw new \InvalidArgumentException();
        }
        
        $this->query = $query;

        return $this;
    }

    public function withFragment($fragment): static
    {
        if (!is_string($fragment)) {
            throw new \InvalidArgumentException();
        }
        
        $this->fragment = $fragment;

        return $this;
    }

    public function __toString()
    {
        return "";
    }
}

new URI("https://example.org/");
new URI("http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
new URI("ftp://ftp.is.co.za/rfc/rfc1808.txt");
new URI("http://www.ietf.org/rfc/rfc2396.txt");
new URI("ldap://[2001:db8::7]/c=GB?objectClass?one");
//new URI("mailto:John.Doe@example.com");
//new URI("news:comp.infosystems.www.servers.unix");
//new URI("tel:+1-816-555-1212");
new URI("telnet://192.0.2.16:80/");
//new URI("urn:oasis:names:specification:docbook:dtd:xml:4.1.2");
