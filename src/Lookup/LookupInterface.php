<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2020/3/11
 * Time: 2:44 PM
 */
namespace EasySwoole\Nsq\Lookup;

interface LookupInterface
{
    public function lookupHosts($topic);
}
