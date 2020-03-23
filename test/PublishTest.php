<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 3:31 PM
 */
namespace EasySwoole\Nsq\Test;

use EasySwoole\Nsq\Config;
use EasySwoole\Nsq\Connection\Producer;
use EasySwoole\Nsq\Lookup\Nsqlookupd;
use EasySwoole\Nsq\Message\Message;
use EasySwoole\Nsq\Nsq;
use PHPUnit\Framework\TestCase;

class PublishTest extends TestCase
{
    public $config;

    public $topic;

    public $hosts;

    /**
     * PublishTest constructor.
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     * @throws \EasySwoole\Nsq\Exception\LookupException
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->config = new Config();
        $this->topic  = "topic.test";
        $nsqlookup = new Nsqlookupd($this->config->getNsqdUrl());
        $this->hosts = $nsqlookup->lookupHosts($this->topic);
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @throws \Throwable
     */
    public function testPublish()
    {
        foreach ($this->hosts as $host) {
            $nsq = new Nsq();
            for ($i = 0; $i < 100; $i++) {
                $msg = new Message();
                $msg->setPayload("test$i");
                $nsq->push(
                    new Producer($host, $this->config),
                    $this->topic,
                    $msg
                );
            }
        }

        $this->assertTrue(true);
    }
}
