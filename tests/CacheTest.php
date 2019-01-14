<?php
use PHPUnit\Framework\TestCase;
require_once '../Cache.php';

class CacheTest extends TestCase
{
    private $cache;
    private $testKey;
    private $testValue;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::init();
        $this->testKey = 'test_key';
        $this->testValue = 'test_value';
    }

    public function testSetValue() {
        $this->assertTrue($this->cache->set($this->testKey, $this->testValue));
    }

    public function testGetValue() {
        $result = $this->cache->get($this->testKey);
        $this->assertEquals($this->testValue, $result);
    }

    public function testDeleteValue() {
        $this->assertTrue($this->cache->delete($this->testKey));
        $this->assertNull($this->cache->get($this->testKey));
    }

    public function testSetWrongKey() {
        $this->assertFalse($this->cache->set('', $this->testValue));
    }
}