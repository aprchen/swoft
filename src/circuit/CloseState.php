<?php

namespace swoft\circuit;

/**
 * 关闭状态及切换(close)
 *
 * 1. 重置failCounter=0 successCount=0
 * 2. 操作失败，failCounter计数
 * 3. 操作失败一定计数，切换为open开启状态
 *
 * @uses      CloseState
 * @version   2017年07月02日
 * @author    lilin <lilin@ugirls.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 5.x {@link http://www.php.net/license/3_0.txt}
 */
class CloseState extends CircuitBreakerState
{
    function doCall($callback, $params = [], $fallback = null)
    {
        $data = false;
        try {
            $data = call_user_func_array($callback, $params);
        } catch (\Exception $e) {
            if($this->circuitBreaker->isClose()){
                $this->circuitBreaker->incFailCount();
            }
            $data = $this->circuitBreaker->$fallback($fallback);
        }

        $failCount = $this->circuitBreaker->getFailCounter();
        $swithToFailCount = $this->circuitBreaker->getSwithToFailCount();
        if($failCount >= $swithToFailCount && $this->circuitBreaker->isClose()){
            $this->circuitBreaker->swithToOpenState();
        }
        return $data;
    }
}