<?php
include 'data.php';
$id=1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- 引入 ECharts 文件 -->
    <script src="js/echarts.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        body{padding-bottom:100px;}
        .boxItem{float:left;}
        .clearfix:after {
            content:"";
            display: block;
            clear:both;
        }
        .boxItem div:first-child{position: relative !important; top:0 !important;left:0 !important;}
        .boxItem .titItem{text-align: center; font-size:36px;}
        .boxItem div canvas{position: relative !important; top:0 !important;left:0 !important;}

        .boxtititem{padding:30px 0;margin-bottom:30px;}
        .boxtititem a{display: block;float: left; width:80px;line-height: 40px;background: cornflowerblue;color:#fff;margin-left:12px;text-align: center; border-radius:5px;text-decoration: none;}
        .boxtititem a.on, .boxtititem a:hover{background:palevioletred;}
    </style>
</head>
<body>
<div class="boxtititem clearfix">
    <a href="index.php">总</a>
    <a href="month.php">月</a>
    <a href="day.php" class="on">日</a>
</div>
 <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div class="clearfix ItemBoxAll">
    <?php foreach ($results as $key=>$result){?>
        <?php foreach ($result as $item => $value) {ksort($value['time_statistics']['day']); ?>
            <div class="boxItem">
                <div class="titItem"><?php echo $key;?></div>
                <div id="main<?php echo $id;?>"  style="width: 600px;height:400px;"></div>
                <script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var myChart = echarts.init(document.getElementById('main<?php echo $id;?>'));

                    data = [<?php  foreach($value['time_statistics']['day'] as $k=>$v){ ?><?php echo '["' . $k . '",' . $v . ']';?>,<?php }; ?>];

                    var dateList = data.map(function (item) {
                        return item[0];
                    });
                    var valueList = data.map(function (item) {
                        return item[1];
                    });
                    option = {
                        // Make gradient line here
                        visualMap: [{
                            show: false,
                            type: 'continuous',
                            seriesIndex: 0,
                            min: 0,
                            max: 400
                        }],

                        title: [{
                            left: 'center',
                            text: "<?php echo $item . '日代码统计' ?>"
                        }],
                        tooltip: {
                            trigger: 'axis'
                        },
                        xAxis: [{
                            data: dateList
                        }],
                        yAxis: [{
                            splitLine: {show: false}
                        }],
                        grid: [{
                            bottom: '60%'
                        }],
                        series: [{
                            type: 'line',
                            showSymbol: false,
                            data: valueList
                        }]
                    };

                    // 使用刚指定的配置项和数据显示图表。
                    myChart.setOption(option);
                </script>
            </div>
            <?php $id ++;} ?>
    <?php } ?>
</div>
</body>
</html>