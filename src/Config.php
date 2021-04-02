<?php

namespace RiVump\Util;

class Config
{
    /**
     * @var array
     */
    private static $configContainer = [];

    /**
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value): void
    {
        static::$configContainer[$key] = $value;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public static function get(string $key, $default = null)
    {
        return static::$configContainer[$key] ?? $default;
    }

    /**
     * @return array
     */
    public static function getAll(): array
    {
        return static::$configContainer;
    }

    /**
     * @param string $namespace
     * @param bool $withNamespace
     * @return array
     */
    public static function getByNamespace(string $namespace, bool $withNamespace = true): array
    {
        $result = [];

        foreach (static::$configContainer as $key => $value) {
            if (strpos($key, $namespace) === false) {
                continue;
            }

            $result[$withNamespace ? $key : str_replace($namespace.'.', '', $key)] = $value;
        }

        return $result;
    }

    /**
     * @param string|string[] $path
     */
    public static function load($path): void
    {
        if (is_string($path)) {
            $path = [$path];
        }

        foreach ($path as $file) {
            $config = require $file;
            if (!$config || !is_array($config)) {
                continue;
            }

            $explodedFilePath = explode('/', $file);
            $namespace = str_replace('.php', '', array_pop($explodedFilePath));

            foreach ($config as $key => $value) {
                static::set($namespace.'.'.$key, $value);
            }
        }
    }
}