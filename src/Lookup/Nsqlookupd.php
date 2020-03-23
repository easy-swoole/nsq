<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 2:43 PM
 */
namespace EasySwoole\Nsq\Lookup;

use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Nsq\Exception\ConnectionException;
use EasySwoole\Nsq\Exception\LookupException;

/**
 * Represents nsqlookupd and allows us to find machines we need to talk to
 * for a given topic
 */
class Nsqlookupd implements LookupInterface
{
    /**
     * Hosts to connect to, incl. :port
     *
     * @var array
     */
    private $hosts;

    /**
     * Connection timeout, in seconds
     *
     * @var float
     */
    private $connectionTimeout;

    /**
     * Response timeout
     *
     * @var float
     */
    private $responseTimeout;

    /**
     * Constructor
     *
     * @param string|array $hosts Single host:port, many host:port with commas,
     *      or an array of host:port, of nsqlookupd servers to talk to
     *      (will default to localhost)
     * @param int $connectionTimeout In seconds
     * @param int $responseTimeout In seconds
     */
    public function __construct($hosts = null, $connectionTimeout = 1, $responseTimeout = 2)
    {
        if ($hosts === null) {
            $this->hosts = array('localhost:4161');
        } elseif (is_array($hosts)) {
            $this->hosts = $hosts;
        } else {
            $this->hosts = explode(',', $hosts);
        }
        $this->connectionTimeout = $connectionTimeout;
        $this->responseTimeout   = $responseTimeout;
    }

    /**
     * Lookup hosts for a given topic
     *
     * @param string $topic
     * @return array Should return array [] = host:port
     * @throws LookupException If we cannot talk to / get back invalid response
     *      from nsqlookupd
     *
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     * @throws ConnectionException
     */
    public function lookupHosts($topic)
    {
        $lookupHosts = array();

        foreach ($this->hosts as $host) {
            if (strpos($host, ':') === false) {
                $host .= ':4161';
            }

            $url = "http://{$host}/lookup?topic=" . urlencode($topic);

            $http = new HttpClient($url);

            if ($http) {
                $r = $http->get();
                if ($r->getErrCode()) {
                    throw new ConnectionException(
                        sprintf(
                            "Error connect nsqlookup , error : %s host: %s, topic : %s",
                            $r->getErrMsg(),
                            $host,
                            $topic
                        )
                    );
                }
                $r = json_decode($r->getBody(), true);

                // don't fail since we can't distinguish between bad topic and general failure
                if (!is_array($r)) {
                    throw new LookupException(
                        "Error talking to nsqlookupd via $url"
                    );
                }

                $producers = isset($r['producers']) ? $r['producers'] : array();
                foreach ($producers as $prod) {
                    if (isset($prod['address'])) {
                        $address = $prod['address'];
                    } else {
                        $address = $prod['broadcast_address'];
                    }
                    $h = "{$address}:{$prod['tcp_port']}";
                    if (!in_array($h, $lookupHosts)) {
                        $lookupHosts[] = $h;
                    }

                }
            }
        }

        return $lookupHosts;
    }
}
