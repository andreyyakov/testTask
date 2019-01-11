<?php

class Cache
{
    private $memcached;
    private static $instance = null;

    private function __clone() {}

    private function __construct($host, $port) {
        $this->memcached = new Memcached();
        $this->memcached->addServer($host, $port);
    }

    public static function init(string $host = 'localhost',int $port = 11211)
    {
        if (null === self::$instance)
        {
            self::$instance = new self($host, $port);
        }
        return self::$instance;
    }

    public function set(string $key, $value,int $expiration = 0): bool {
        return $this->memcached->set($key, $value, $expiration);
    }

    public function get(string $key) {
        return $this->memcached->get($key);
    }

    public function delete(string $key,int $time = 0): bool {
        return $this->memcached->delete($key, $time);
    }

}