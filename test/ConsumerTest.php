<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/20
 * Time: 1:39 PM
 */
namespace EasySwoole\Nsq\Test;

use EasySwoole\Nsq\Config;
use EasySwoole\Nsq\Connection\Consumer;
use EasySwoole\Nsq\Lookup\Nsqlookupd;
use EasySwoole\Nsq\Nsq;
use PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{
    /**
     * @var string
     */
    public $topic;

    /**
     * @var array
     */
    public $hosts;

    /**
     * ConsumerTest constructor.
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     * @throws \EasySwoole\Nsq\Exception\LookupException
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->topic    = "topic.test";
        $config         = new Config();

        $nsqlookup      = new Nsqlookupd($config->getNsqdUrl());
        $this->hosts    = $nsqlookup->lookupHosts($this->topic);

        parent::__construct($name, $data, $dataName);
    }

    /**
     * 内部实现 持续化消费
     */
    public function testSubscribe()
    {
        foreach ($this->hosts as $host) {
            $nsq = new Nsq();
            $nsq->subscribe(
                new Consumer($host, new Config(), $this->topic, 'data.consuming'),
                function ($item) {
                    var_dump($item['message']);
                }
            );
        }
        $this->assertTrue(true);
    }

    /**
     * 用户控制消费频率
     */
    public function testPop()
    {
        foreach ($this->hosts as $host) {
            $nsq = new Nsq();
            $consumer = new Consumer($host, new Config(), $this->topic, 'test.consuming');
            for ($i = 0; $i < 100; $i++) {
                $nsq->pop($consumer, function ($item) {
                    var_dump($item['message']);
                });
            }
        }
        $this->assertTrue(true);
    }
}
