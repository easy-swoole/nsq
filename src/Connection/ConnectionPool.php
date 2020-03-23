<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/17
 * Time: 3:10 PM
 */
namespace EasySwoole\Nsq\Connection;

use EasySwoole\Pool\AbstractPool;
use EasySwoole\Pool;
use EasySwoole\Nsq;

class ConnectionPool extends AbstractPool
{
    protected $host;

    protected $topic;

    protected $channel;

    /**
     * @var \EasySwoole\Nsq\Config
     */
    protected $config;

    /**
     * ConnectionPool constructor.
     * @param Pool\Config $poolConfig
     * @param             $host
     * @param Nsq\Config  $config
     * @param string      $topic
     * @param string      $channel
     * @throws Pool\Exception\Exception
     */
    public function __construct(Pool\Config $poolConfig, $host, Nsq\Config $config, $topic = '', $channel = '')
    {
        parent::__construct($poolConfig);
        $this->host = $host;
        $this->config = $config;
        $this->topic = $topic;
        $this->channel = $channel;
    }

    protected function createObject()
    {
    }

    /**
     * @param $nsq
     * @return bool
     */
    public function itemIntervalCheck($nsq): bool
    {
        /*
         * 如果最后一次使用时间超过autoPing间隔
         */
        if ($this->getConfig()->getAutoPing() > 0 && (time() - $nsq->__lastUseTime > $this->getConfig()->getAutoPing())) {
            try {
                //执行一个ping
                $nsq->ping();
                //标记使用时间，避免被再次gc
                $nsq->__lastUseTime = time();
                return true;
            } catch (\Throwable $throwable) {
                //异常说明该链接出错了，return 进行回收
                return false;
            }
        } else {
            return true;
        }
    }
}
