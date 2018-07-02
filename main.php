<?php
#!/usr/bin/env php
/**
 * Created by PhpStorm.
 * User: raohongjun
 * Date: 2018/6/8
 * Time: 上午11:52
 */

include __DIR__ . "/vendor/autoload.php";

use PHPMD\TextUI\Command;

$gitdata = [
    "医生患者服务平台" => __DIR__ . "/../dpserviceplatform",//医生患者服务平台
    "福瑞医生管理后台" => __DIR__ . "/../agAppConsole",//福瑞医生管理后台
    "福瑞医生会员端" => __DIR__ . "/../h5NewSystem",//福瑞医生会员端
    "福瑞海外医疗小程序服务层" => __DIR__ . "/../appletsService",//福瑞海外医疗小程序服务层
    "问医生小程序" => __DIR__ . "/../wxapp-wys",//问医生小程序
    "问医生小程序测试" => __DIR__ . "/../wxapp-wys-test",//问医生小程序测试
    "权限服务" => __DIR__ . "/../permissionService",//权限服务
    "日志服务" => __DIR__ . "/../aglogsService",//日志服务
    "用户中心服务" => __DIR__ . "/../userCenterService",//用户中心服务
    "脚本服务" => __DIR__ . "/../scriptServices",//脚本服务
    "消息服务层" => __DIR__ . "/../messageService",//消息服务层
    "统计服务层" => __DIR__ . "/../statisticsService",//统计服务层
    "知识库服务层" => __DIR__ . "/../loreService",//知识库服务层
    "搜索服务层" => __DIR__ . "/../searchService",//搜索服务层
    "订单服务层" => __DIR__ . "/../orderService",//订单服务层
    "OCR服务层" => __DIR__ . "/../ocrService",//OCR服务层
    "核心服务层（关）" => __DIR__ . "/../coreServices",//核心服务层（关）
    "肝病联盟管理后台" => __DIR__ . "/../businessAdmin",//肝病联盟管理后台
    "肝病联盟公众号" => __DIR__ . "/../h5UninSystem",//肝病联盟公众号
    "福瑞海外医疗小程序" => __DIR__ . "/../wxapp_hwyl",//福瑞海外医疗小程序
    "302患者端demo" => __DIR__ . "/../patientDemo",//302患者端demo
    "通讯服务" => __DIR__ . "/../imservice",//通讯服务
];

foreach ($gitdata as $k => $v) {
    $git = new \Codereview\gitCount($v);
    $git->addExcludePath([
        "vendor/*",
        "thinkphp/*",
        "tests/*",
        "storage/*",
        "lang/*",
        "migrations/*",
        "assets/*",
    ]);
    $git->addExcludeFileName([
        "composer",
        "artisan",
        "Handler",
        "helper"
    ]);
    //拉取更新
    $git->runCommand("cd " . $v . "&& git pull");

    $result[$k] = $git->analysis();

    /**
     *  命令行界面还接受以下可选参数：
     *  --minimumpriority - 规则优先级阈值; 优先级较低的规则不会被使用。
     *  --reportfile- 将报告输出发送到指定的文件，而不是默认的输出目标STDOUT。
     *  --suffixes - 逗号分隔的有效源代码文件扩展名字符串，例如php，phtml。
     *  --exclude - 逗号分隔的用于忽略目录的模式字符串。
     *  --strict - 用@SuppressWarnings注释报告这些节点。
     *  --ignore-violations-on-exit - 即使发现任何违规，也会以零代码退出。
     */

    $conf = [
        'php main',
        $v,
        'html',
        'codesize,design,unusedcode',
        '--reportfile',
        './views/static/' . $k . '.html',
        '--exclude',
        'vendor/,Tests/,oss-sdk-php/,thinkphp/,tests/'
    ];
    // 代码分析
    Command::main($conf);
    echo $k;
}
file_put_contents("views/data.php", '<?php return $results =' . var_export($result, true) . ';?>');


