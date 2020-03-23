<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/20
 * Time: 9:55 AM
 */
namespace EasySwoole\Nsq\Connection;

use EasySwoole\Nsq\Config;
use EasySwoole\Nsq\Exception\ConnectionException;
use EasySwoole\Nsq\Wire\Packet;
use Swoole\Client;

class Consumer extends AbstractMonitor
{

    /**
     * Subscribe topic
     *
     * @var string
     */
    protected $topic;

    /**
     * Subscribe channel
     *
     * @var string
     */
    protected $channel;

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
     * Consumer constructor.
     * @param        $host
     * @param Config $config
     * @param        $topic
     * @param        $channel
     * @throws ConnectionException
     */
    public function __construct($host, Config $config, $topic, $channel)
    {
        $this->host = $host;
        $this->config = $config;
        $this->topic = $topic;
        $this->channel = $channel;
        $this->connect();
    }

    /**
     * @return mixed|void
     * @throws ConnectionException
     */
    public function connect()
    {
        // init swoole client
        $this->client = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP, SWOOLE_SOCK_SYNC);

        // set swoole tcp client config
        $this->client->set($this->config->getClient());

        list($host, $port) = explode(':', $this->host);
        // connect nsq server
        if (!$this->client->connect($host, $port, 3)) {
            $connectStr = "tcp://{$host}:{$port}";
            throw new ConnectionException("Connect to Nsq server {$connectStr} failed: {$this->client->errMsg}");
        }
        // send magic to nsq server
        $this->client->send(Packet::magic());

        // send identify params
//        $this->client->send(Packet::identify([
////            'client_id'           => $host,
////            'hostname'            => gethostname(),
//            'user_agent'          => 'nsq-client',
////            'heartbeat_interval'  => 2000,
//            'feature_negotiation' => false
//        ]));

        // sub nsq topic and channel
        $hn    = gethostname();
        $parts = explode('.', $hn);
        $this->client->send(Packet::sub($this->topic, $this->channel, $parts[0], $hn));

        // tell nsq server to be ready accept {n} data
        $this->client->send(Packet::rdy($this->config->getOptions()['rdy']));
    }
}
