<?php
/**
 * Created by PhpStorm.
 * User: raohongjun
 * Date: 2018/6/7
 * Time: 上午11:51
 */

namespace Codereview;

class command
{
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function run()
    {
        $handle = popen($this->command, "r");

        $result = '';
        while (1) {
            $res = fgets($handle, 1024);
            if ($res)
                $result .= $res;
            else break;
        }
        pclose($handle);
        return $result;
    }

}