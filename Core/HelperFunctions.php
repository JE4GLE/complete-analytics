<?php


namespace ScAnalytics\Core;


use RuntimeException;

/**
 * Class HelperFunctions. Useful helper functions.
 *
 * @author Jan-Nicklas Adler
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 * @copyright All Rights Reserved.
 */
class HelperFunctions
{

    /**
     * Returns the path to the project root folder, where the composer.json and the vendor directory are located. Does <b>not</b> include a trailing slash. Also works with unit tests.
     *
     * @return string Directory path of the project root
     */
    public static function getRoot(): string
    {
        $directory = __DIR__;
        $root = null;
        do {
            $directory = dirname($directory);
            $vendor = $directory . '/vendor';
            if (file_exists($vendor)) {
                $root = $directory;
            }
        } while (is_null($root) && $directory !== '/');
        if (!is_null($root)) {
            return $root;
        }
        throw new RuntimeException("Could not get project root directory");
    }

    /**
     * Locates the directory storing the assets. Also works with unit tests.
     *
     * @return string Directory path of the asset directory
     */
    public static function getAssetsDir(): string
    {
        $root = self::getRoot();
        if (file_exists($root . "/Assets")) {
            return $root . "/Assets";
        }

        if (file_exists($root . "/libraries/promatur/sc-analytics")) {
            return $root . "/libraries/promatur/sc-analytics";
        }

        throw new RuntimeException("Could not get asset directory");
    }

    /**
     * Checks, if the connection is secured over HTTPS. Works for most load balancers.
     * @return bool Boolean, if the connection uses HTTPS
     */
    public static function isHTTPS(): bool
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $isSecure = true;
        } elseif ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
            $isSecure = true;
        } elseif (!empty($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT']) === 443) {
            $isSecure = true;
        }
        return $isSecure;
    }

    /**
     * Gets the domain, to user is currently on.
     *
     * @return string Domain of the user
     */
    public static function getDomain(): string
    {
        $s = self::isHTTPS() ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"] ?? "HTTP/1.1");
        $protocol = explode('/', $sp)[0]. $s;
        return $protocol . "://" . ($_SERVER['SERVER_NAME'] ?? "UNKNOWN");
    }

    /**
     * Gets the full URL, to user is currently on.
     *
     * @return string Full URL
     */
    public static function getURL(): string
    {
        $requestURI = $_SERVER['REQUEST_URI'] ?? "";
        return self::getDomain() . $requestURI;
    }

    /**
     * Returns the IP of the user by accessing different methods. Value <b>cannot be trusted</b> by 100%. Supports CloudFlare.
     *
     * @return string|null IP of the current user
     */
    public static function getIpAddress(): ?string
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Checks if a string ends with another string.
     *
     * @param string $haystack Full string
     * @param string $needle Chars which should be at the end
     * @return bool True if the haystack ends with the needle
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

}