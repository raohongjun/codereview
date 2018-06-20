<?php
include 'data.php';
//当前日期
$sdefaultDate = date("Y-m-d");
//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
$first = 1;
//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
$w = date('w', strtotime($sdefaultDate));
//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
$week_start = date('Y-m-d', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
//本周结束日期
$week_end = date('Y-m-d', strtotime("$week_start +6 days"));
?>

<html>
<head>
    <meta charset="utf-8">
    <title>个人周代码报告</title>
    <style>
        .clearfix:after {  content: "";  display: block;  clear: both;  }

        .boxtititem {  padding: 30px 0;  margin-bottom: 30px;  }

        .boxtititem a {  display: block;  float: left;  width: 80px;  line-height: 40px;  background: cornflowerblue;  color: #fff;  margin-left: 12px;  text-align: center;  border-radius: 5px;  text-decoration: none;  }

        .boxtititem a.on, .boxtititem a:hover {  background: palevioletred;  }
    </style>
</head>
<body>
<div class="boxtititem clearfix">
    <a href="index.php">总</a>
    <a href="month.php">月</a>
    <a href="day.php">日</a>
    <a href="weekSummary.php" class="on">周总结</a>
</div>
<center><h1>个人本周代码统计报告</h1></center>
<table border="1" align="center" cellspacing="0" cellpadding="3">
<?php
    foreach ($results as $result => $item) {
        $i = 0;
        foreach ($item as $key => $value) {
            $count = 0;
            foreach ($value['time_statistics']['day'] as $k => $v) {
                if ($k >= $week_start && $k <= $week_end) {
                    $count += $v;
                }
            }
            if ($count > 0 && $i == 0) {
                $i++;
                echo '<tr bgcolor="lightgrey"> <th>' . $result . '</th><th>总留存代码数</th></tr><tr><td align="center">' . $key . '</td><td>' . $count . '</td></tr>';
            } elseif ($count > 0 && $i > 0) {
                echo '<tr><td align="center">' . $key . '</td><td>' . $count . '</td></tr>';
            }

        }
    }
?>
</table>
</body>
</html>