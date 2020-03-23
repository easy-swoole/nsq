<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/17
 * Time: 4:49 PM
 */
namespace EasySwoole\Nsq\Connection;

use EasySwoole\Pool\Config;

class ConnectionPoolConfig extends Config
{
    protected $autoPing=5;

    /**
     * @return mixed
     */
    public function getAutoPing()
    {
        return $this->autoPing;
    }

    /**
     * @param mixed $autoPing
     */
    public function setAutoPing($autoPing)
    {
        $this->autoPing = $autoPing;
    }
}
