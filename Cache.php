<?php



class Cache {

    protected $socket = null;
    protected $port ;
    protected $host;

    private static $instance = null;

    private function __clone() {}
    private function __construct(string $host = 'localhost',int $port = 11211 )
    {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    public static function init(string $host = 'localhost',int $port = 11211)
    {
        if (null === self::$instance)
        {
            self::$instance = new self($host, $port);
        }
        return self::$instance;
    }

    protected function connect()
    {
        $result = false;
        $error = 0;
        $errstr = '';
        $result = @fsockopen($this->host, $this->port, $error, $errstr);
        if ($result) {
            $this->socket = $result;
        } else {
            $errorMsg = "Connect error:" . PHP_EOL . "[$error] $errstr";
            throw new DomainException($errorMsg);
        }
    }

    public function set($key, $val, $expt = 0)
    {
        $valueString = serialize($val);
        $this->writeSocket(
            "set $key 0 $expt " . strlen($valueString)
        );
        $s = $this->writeSocket($valueString, true);
        if ('STORED' !== $s) {
            return false;
        }else{
            return true;
        }
    }

    public function get($key)
    {
        $this->writeSocket("get $key");
        $s = $this->readSocket();
        if (is_null($s) || 'VALUE' != substr($s, 0, 5)) {
            return null;
        } else {
            $s_result = '';
            $s = $this->readSocket();
            while ('END' != $s) {
                $s_result .= $s;
                $s = $this->readSocket();
            }
            return unserialize($s_result);
        }
    }

    public function delete($key)
    {
        $this->writeSocket("delete $key");
        $s = $this->readSocket();
        if ('DELETED' == $s) {
            return true;
        }else{
            return false;
        }
    }

    protected function readSocket()
    {
        if (is_null($this->socket)) {
            return null;
        }
        return trim(fgets($this->socket));
    }

    protected function writeSocket($cmd, $result = false)
    {
        if (is_null($this->socket)) {
            return false;
        }
        fwrite($this->socket, $cmd . "\r\n");
        if (true == $result) {
            return $this->readSocket();
        }
        return true;
    }

    public function __destruct()
    {
        fclose($this->socket);
    }

}