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
        .boxItem div{position: relative !important; top:0 !important;left:0 !important;}
        .boxItem .titItem{text-align: center; font-size:36px;}
        .boxItem div canvas{position: relative !important; top:0 !important;left:0 !important;}

        .boxtititem{padding:30px 0;margin-bottom:30px;}
        .boxtititem a{display: block;float: left; width:80px;line-height: 40px;background: cornflowerblue;color:#fff;margin-left:12px;text-align: center; border-radius:5px;text-decoration: none;}
        .boxtititem a.on, .boxtititem a:hover{background:palevioletred;}
    </style>
</head>
<body>
<div class="boxtititem clearfix">
    <a href="index.php" class="on">总</a>
    <a href="month.php">月</a>
    <a href="day.php">日</a>
</div>
 <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div class="clearfix ItemBoxAll">


    <?php foreach ($results as $key=>$result){?>
    <div class="boxItem">
    <div class="titItem"><?php echo $key;?></div>
    <div id="main<?php echo $id;?>"  style="width: 600px;height:400px;"></div>
    <script type="text/javascript">
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main<?php echo $id;?>'));
        // 指定图表的配置项和数据
        option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data:[<?php foreach($result as $item=>$value){ ?>'<?php echo $item ?>',<?php }; ?>]
            },
            series: [
                {
                    name:'访问来源',
                    type:'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '30',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data:[<?php foreach($result as $item=>$value){ ?><?php echo '{value:' . $value["all_lines"] . ',name:"' . $item. '"}' ?>,<?php }; ?>]
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
    </div>
    <?php $id ++;} ?>
</div>
</body>
</html>