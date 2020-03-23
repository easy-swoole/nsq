<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 2:17 PM
 */
namespace EasySwoole\Nsq;

use EasySwoole\Nsq\Connection\AbstractMonitor;
use EasySwoole\Nsq\Connection\Consumer;
use EasySwoole\Nsq\Connection\Producer;
use EasySwoole\Nsq\Message\Message;
use EasySwoole\Nsq\Wire\Packet;
use EasySwoole\Nsq\Wire\Unpack;
use Swoole\Coroutine;

class Nsq
{
    private $enableListen = false;

    /**
     * @param Producer $client
     * @param          $topic
     * @param Message  $msg
     * @return $this
     * @throws \Throwable
     */
    public function push(Producer $client, $topic, Message $msg)
    {
        $success = 0;
        $errors = array();

        try {
            $this->tryFunc(function (Producer $conn) use ($topic, $msg, &$success, &$errors) {
                $payload = $msg->getPayload();
                $payload = is_array($payload) ? Packet::mpub($topic, $payload) : Packet::pub($topic, $payload);
                $conn->send($payload);
                $frame = Unpack::getFrame($conn->receive());

                while (Unpack::isHeartbeat($frame)) {
                    $conn->send(Packet::nop());
                    $frame = Unpack::getFrame($conn->receive());
                }

                if (Unpack::isOk($frame)) {
                    $success++;
                } else {
                    $errors[] = $frame['error'];
                }
            }, $client, 2);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            throw new \RuntimeException(json_encode($errors));
        }

        return $this;
    }

    /**
     * Subscribe to topic/channel
     *
     * @param Consumer $client
     * @param          $callback
     * @param float    $breakTime
     * @param int      $maxCurrency
     * @throws \Throwable
     */
    public function subscribe(Consumer $client, $callback, $breakTime = 0.01, $maxCurrency = 128)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                '"callback" invalid; expecting a PHP callable'
            );
        }

        if (!$client->isConnected()) {
            $client->reconnect();
        }

        $this->enableListen = true;
        $running = 0;

        while ($this->enableListen) {
            if ($running >= $maxCurrency) {
                Coroutine::sleep($breakTime);
                continue;
            }

            if (!$client->isConnected()) {
                $client->reconnect();
            }

            $data = $client->receive();

            if ($data != false) {
                ++$running;
                try {
                    $this->pop($client, $callback, $data);
                } catch (\Throwable $throwable) {
                    throw $throwable;
                } finally {
                    --$running;
                }
            } else {
                continue;
            }
        }
    }

    /**
     * @param Consumer $client
     * @param          $callback
     * @param          $data
     * @return bool
     */
    public function pop(Consumer $client, $callback, $data)
    {
        // unpack message
        $frame = Unpack::getFrame($data);

        if (Unpack::isHeartbeat($frame)) {
            $client->send(Packet::nop());
        } elseif (Unpack::isOk($frame)) {
            return false;
        } elseif (Unpack::isError($frame)) {
            return false;
        } elseif (Unpack::isMessage($frame)) {
            $msg = new Message($frame);
            call_user_func($callback, $msg->toArray());
            $client->send(Packet::fin($msg->getId()));
            $client->send(Packet::rdy(1));
        } else {
            return false;
        }
    }

    public function stop()
    {
        $this->enableListen = false;
        return $this;
    }

    /**
     * @param callable        $func
     * @param AbstractMonitor $conn
     * @param int             $tries
     * @throws \Exception
     */
    private function tryFunc(callable $func, AbstractMonitor $conn, $tries = 1)
    {
        $lastException = null;
        for ($try = 0; $try <= $tries; $try++) {
            try {
                $func($conn);
                return;
            } catch (\Exception $e) {
                $lastException = $e;
                $conn->reconnect();
            }
        }
        if ($lastException) {
            throw $lastException;
        }
    }
}
