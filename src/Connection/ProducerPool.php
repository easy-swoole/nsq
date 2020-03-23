<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/20
 * Time: 10:06 AM
 */
namespace EasySwoole\Nsq\Connection;

class ProducerPool extends ConnectionPool
{
    /**
     * @return Connection|Producer
     * @throws \EasySwoole\Nsq\Exception\ConnectionException
     */
    protected function createObject()
    {
        return new Producer($this->host, $this->config);
    }
}
