<?php
/**
 * Created by PhpStorm.
 * User: raohongjun
 * Date: 2018/6/8
 * Time: 上午11:52
 */

include __DIR__."/vendor/autoload.php";

$gitdata=[
    "医生患者服务平台"=>"/Users/raohongjun/PhpstormProjects/dpserviceplatform",//医生患者服务平台
    "福瑞医生管理后台"=>"/Users/raohongjun/PhpstormProjects/agAppConsole",//福瑞医生管理后台
    "福瑞医生会员端"=>"/Users/raohongjun/PhpstormProjects/h5NewSystem",//福瑞医生会员端
    "福瑞海外医疗小程序服务层"=>"/Users/raohongjun/PhpstormProjects/appletsService",//福瑞海外医疗小程序服务层
    "问医生小程序"=>"/Users/raohongjun/PhpstormProjects/wxapp-wys",//问医生小程序
    "问医生小程序测试"=>"/Users/raohongjun/PhpstormProjects/wxapp-wys-test",//问医生小程序测试
    "权限服务"=>"/Users/raohongjun/PhpstormProjects/permissionService",//权限服务
    "日志服务"=>"/Users/raohongjun/PhpstormProjects/aglogsService",//日志服务
    "用户中心服务"=>"/Users/raohongjun/PhpstormProjects/userCenterService",//用户中心服务
    "脚本服务"=>"/Users/raohongjun/PhpstormProjects/scriptServices",//脚本服务
    "消息服务层"=>"/Users/raohongjun/PhpstormProjects/messageService",//消息服务层
    "统计服务层"=>"/Users/raohongjun/PhpstormProjects/statisticsService",//统计服务层
    "知识库服务层"=>"/Users/raohongjun/PhpstormProjects/loreService",//知识库服务层
    "搜索服务层"=>"/Users/raohongjun/PhpstormProjects/searchService",//搜索服务层
    "订单服务层"=>"/Users/raohongjun/PhpstormProjects/orderService",//订单服务层
    "OCR服务层"=>"/Users/raohongjun/PhpstormProjects/ocrService",//OCR服务层
    "核心服务层（关）"=>"/Users/raohongjun/PhpstormProjects/coreServices",//核心服务层（关）
    "肝病联盟管理后台"=>"/Users/raohongjun/PhpstormProjects/businessAdmin",//肝病联盟管理后台
    "肝病联盟公众号"=>"/Users/raohongjun/PhpstormProjects/h5UninSystem",//肝病联盟公众号
    "福瑞海外医疗小程序"=>"/Users/raohongjun/PhpstormProjects/wxapp_hwyl",//福瑞海外医疗小程序
    "302患者端demo"=>"/Users/raohongjun/PhpstormProjects/patientDemo",//302患者端demo
];
foreach ($gitdata as $k=>$v) {
    $git = new \Codereview\gitCount($v);
    $git->addExcludePath([
        "vendor/*",
        "thinkphp/*",
        "tests/*"
    ]);
    $git->addExcludeFileName([
        "composer"
    ]);
    //拉取更新
   $a= $git->runCommand( "cd " . $v . '&&  git log  --format=\'%aN\' | sort -u | while read name; do echo -en "$name\t"; git log --author="$name" --pretty=tformat: --numstat | awk \'{ add += $1; subs += $2; loc += $1 - $2 } END { printf "added lines: %s, removed lines: %s, total lines: %s\n", add, subs, loc }\' -; done' );
print_r($a);exit;
    $result[$k] = $git->analysis();
    echo $k;
}
file_put_contents("views/data.php",'<?php return $results ='.var_export( $result ,true).';?>');


