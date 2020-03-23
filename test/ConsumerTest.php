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

    public $nsq;

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
        $this->nsq      = new Nsq();
        parent::__construct($name, $data, $dataName);
    }

    /**
     * 内部实现 持续化消费
     * @throws \Throwable
     */
    public function testSubscribe()
    {
        foreach ($this->hosts as $host) {
            // 想要手动关闭，nsq 进程要唯一。类比 mysql 的 rollback
            $this->nsq->subscribe(
                new Consumer($host, new Config(), $this->topic, 'test.consuming'),
                function ($item) {
                    var_dump($item['message']);
                }
            );
        }
        $this->assertTrue(true);
    }

    public function testStop()
    {
        $this->nsq->stop();
    }
}
