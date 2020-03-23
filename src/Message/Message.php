<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 4:04 PM
 */
namespace EasySwoole\Nsq\Message;

use EasySwoole\Spl\SplBean;

class Message extends SplBean
{
    /**
     * Message payload - string
     *
     * @var array|string
     */
    public $message = '';

    /**
     * Message ID; if relevant
     *
     * @var string|NULL
     */
    public $id = null;

    /**
     * How many attempts have been made; if relevant
     *
     * @var integer|NULL
     */
    public $attempts = null;

    /**
     * Timestamp - UNIX timestamp in seconds (incl. fractions); if relevant
     *
     * @var float|NULL
     */
    public $timestamp = null;

    /**
     * Get message payload
     *
     * @return array|string
     */
    public function getPayload()
    {
        return $this->message;
    }

    /**
     * Get message ID
     *
     * @return string|NULL
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get attempts
     *
     * @return integer|NULL
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Get timestamp
     *
     * @return float|NULL
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set message payload
     *
     * @param array|string $data
     * @return mixed|void
     */
    public function setPayload($data)
    {
        $this->message = $data;
    }

    /**
     * Set message ID
     *
     * @param $id
     * @return mixed|void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set attempts
     *
     * @param $attempts
     * @return mixed|void
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
    }

    /**
     * Set timestamp
     *
     * @param $ts
     * @return mixed|void
     */
    public function setTs($ts)
    {
        $this->timestamp = $ts;
    }
}
