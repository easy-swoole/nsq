<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 2:20 PM
 */
namespace EasySwoole\Nsq;

class Config
{
    /**
     * Nsqd host
     *
     * @var string
     */
    public $nsqdUrl = '127.0.0.1:4161';

    /**
     * Nsqlookup host
     *
     * @var string
     */
    public $nsqlookupUrl = '127.0.0.1:4150';

    /**
     * Nsq Config
     *
     * @var array
     */
    public $options = [
        'rdy' => 1,
        'cl'  => 1
    ];

    /**
     * Nsq identify
     *
     * @var array
     */
    public $identify = [
        'user_agent' => 'nsq-client',
    ];

    /**
     * Swoole Client Params
     *
     * @var array
     */
    public $client = [
        'options' => [
            'open_length_check'     => true,
            'package_max_length'    => 2048000,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,
            'package_body_offset'   => 4
        ]
    ];

    /**
     * @return string
     */
    public function getNsqdUrl()
    {
        return $this->nsqdUrl;
    }

    /**
     * @param string $nsqdUrl
     */
    public function setNsqdUrl($nsqdUrl)
    {
        $this->nsqdUrl = $nsqdUrl;
    }

    /**
     * @return string
     */
    public function getNsqlookupUrl()
    {
        return $this->nsqlookupUrl;
    }

    /**
     * @param string $nsqlookupUrl
     */
    public function setNsqlookupUrl($nsqlookupUrl)
    {
        $this->nsqlookupUrl = $nsqlookupUrl;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getIdentify()
    {
        return $this->identify;
    }

    /**
     * @param array $identify
     */
    public function setIdentify($identify)
    {
        $this->identify = $identify;
    }

    /**
     * @return array
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }
}
