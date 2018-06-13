<?php
/**
 * Created by PhpStorm.
 * User: raohongjun
 * Date: 2018/6/13
 * Time: 下午3:42
 */


#!/usr/bin/env php
use PHPMD\TextUI\Command;
require_once __DIR__."/vendor/autoload.php";

// 默认允许尽可能多的内存
if (extension_loaded('suhosin') && is_numeric(ini_get('suhosin.memory_limit'))) {
    $limit = ini_get('memory_limit');
    if (preg_match('(^(\d+)([BKMGT]))', $limit, $match)) {
        $shift = array('B' => 0, 'K' => 10, 'M' => 20, 'G' => 30, 'T' => 40);
        $limit = ($match[1] * (1 << $shift[$match[2]]));
    }
    if (ini_get('suhosin.memory_limit') > $limit && $limit > -1) {
        ini_set('memory_limit', ini_get('suhosin.memory_limit'));
    }
} else {
    ini_set('memory_limit', -1);
}


/**
 *  命令行界面还接受以下可选参数：
 *  --minimumpriority - 规则优先级阈值; 优先级较低的规则不会被使用。
 *  --reportfile- 将报告输出发送到指定的文件，而不是默认的输出目标STDOUT。
 *  --suffixes - 逗号分隔的有效源代码文件扩展名字符串，例如php，phtml。
 *  --exclude - 逗号分隔的用于忽略目录的模式字符串。
 *  --strict - 用@SuppressWarnings注释报告这些节点。
 *  --ignore-violations-on-exit - 即使发现任何违规，也会以零代码退出。
 */
$conf=[
    './phpmd',
    '/Users/raohongjun/PhpstormProjects/dpserviceplatform',
    'html',
    'codesize,design,unusedcode',
    '--reportfile',
    '/Users/raohongjun/PhpstormProjects/codereview/dpserviceplatform.html',
    '--exclude',
    'vendor/,Tests/,oss-sdk-php/'
];
// 运行命令行界面
exit(Command::main($conf));
