<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/23
 * Time: 10:38 AM
 */
require_once __DIR__ . '/vendor/autoload.php';

go(function () {
    $topic      = "topic.test";
    $config     = new \EasySwoole\Nsq\Config();
    $nsqlookup  = new \EasySwoole\Nsq\Lookup\Nsqlookupd($config->getNsqdUrl());
    $hosts      = $nsqlookup->lookupHosts($topic);
    foreach ($hosts as $host) {
        $nsq = new \EasySwoole\Nsq\Nsq();
        $nsq->subscribe(
            new \EasySwoole\Nsq\Connection\Consumer($host, $config, $topic, 'test.consuming'),
            function ($item) {
                var_dump($item['message']);
            }
        );
        $nsq->stop();
    }
});
