<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/12
 * Time: 9:27 AM
 */
namespace EasySwoole\Nsq\Connection;

use EasySwoole\Nsq\Exception\ConnectionException;
use EasySwoole\Nsq\Exception\InvalidArgumentException;
use Swoole\Coroutine\Client as CoroutineClient;
use swoole_client;

class Connection
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port = -1;

    /**
     * @var swoole_client
     */
    protected $client;

    /**
     * Connection constructor.
     * @param $host
     * @param $port
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    /**
     * @return CoroutineClient|swoole_client
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function connect()
    {
        if (trim($this->host) === '') {
            throw new InvalidArgumentException("Cannot open null host.");
        }

        if ($this->port <= 0) {
            throw new InvalidArgumentException("Cannot open without port.");
        }

//        $settings = [
//            'open_length_check'     => 1,
//            'package_length_type'   => 'N',
//            'package_length_offset' => 0,
//            'package_body_offset'   => 4,
//            'package_max_length'    => 1024 * 1024 * 3,
//        ];

        $this->client = new CoroutineClient(SWOOLE_SOCK_TCP);
//        $this->client->set($settings);

        if (!$this->client->isConnected()) {
            $connected = $this->client->connect($this->host, $this->port);
            if (!$connected) {
                $connectStr = "tcp://{$this->host}:{$this->port}";
                throw new ConnectionException("Connect to Nsq server {$connectStr} failed: {$this->client->errMsg}");
            }
        }

        return $this->client;
    }

    /**
     * @param null|string $data
     * @return mixed
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function send($data = null)
    {
        if ($this->connect()) {
            $this->client->send($data);
            return $this->client->recv();
        }
        $connectStr = "tcp://{$this->host}:{$this->port}";
        throw new ConnectionException("Connect to Nsq server {$connectStr} failed: {$this->client->errMsg}");
    }

    /**
     * @param int $timeout
     * @return mixed
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function recv($timeout = -1)
    {
        if ($this->connect()) {
            $data = $this->client->recv($timeout);
            return $data;
        }
//        $connectStr = "tcp://{$this->host}:{$this->port}";
//        throw new ("Connect to Nsq server {$connectStr} failed: {$this->client->errMsg}");
    }

    public function close()
    {
        $this->client->close();
    }


    /**
     * @return CoroutineClient|swoole_client
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function reconnect()
    {
        @$this->client->close();
        return $this->client = $this->connect();
    }
}
