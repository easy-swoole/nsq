<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/20
 * Time: 9:54 AM
 */
namespace EasySwoole\Nsq\Connection;

use EasySwoole\Nsq\Config;
use EasySwoole\Nsq\Exception\ConnectionException;
use EasySwoole\Nsq\Wire\Packet;
use Swoole\Client;

class Producer extends AbstractMonitor
{
    /**
     * Nsq config
     *
     * @var Config
     */
    protected $config;

    /**
     * Nsqd host
     *
     * @var string
     */
    protected $host;

    /**
     * Producer constructor.
     * @param        $host
     * @param Config $config
     * @throws ConnectionException
     */
    public function __construct($host, Config $config)
    {
        $this->host   = $host;
        $this->config = $config;
        $this->connect();
    }

    /**
     * @return mixed|void
     * @throws ConnectionException
     */
    public function connect()
    {
        // init swoole client
        $this->client = new Client(SWOOLE_SOCK_TCP);

        // set swoole tcp client config
        $this->client->set($this->config->getClient());

        list($host, $port) = explode(':', $this->host);
        // connect nsq server
        if (!$this->client->connect($host, $port, 3)) {
            $connectStr = "tcp://{$host}:{$port}";
            throw new ConnectionException("Connect to Nsq server {$connectStr} failed: {$this->client->errMsg}");
        }

        $this->client->send(Packet::magic());

        $this->client->send(Packet::identify([
            'client_id'           => $host,
            'hostname'            => gethostname(),
            'user_agent'          => 'nsq-client',
            'heartbeat_interval'  => -1,
            'feature_negotiation' => true
        ]));

        $this->client->recv();
    }
}
