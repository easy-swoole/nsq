<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/23
 * Time: 10:32 AM
 */

require_once __DIR__ . '/vendor/autoload.php';

go(function () {
    $config = new \EasySwoole\Nsq\Config();
    $topic  = "topic.test";
    $hosts = $config->getNsqdUrl() ?: ['127.0.0.1:4150'];

    foreach ($hosts as $host) {
        $nsq = new \EasySwoole\Nsq\Nsq();
        for ($i = 0; $i < 10; $i++) {
            $msg = new \EasySwoole\Nsq\Message\Message();
            $msg->setPayload("test$i");
            $nsq->push(
                new \EasySwoole\Nsq\Connection\Producer($host, $config),
                $topic,
                $msg
            );
        }
    }
});
