<?php
include 'data.php';
$id = 1;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- 引入 ECharts 文件 -->
    <script src="js/echarts.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        body {  padding-bottom: 100px;  }

        .boxItem {  float: left;  }

        .clearfix:after {  content: "";  display: block;  clear: both;  }

        .boxItem div {  position: relative !important;  top: 0 !important;  left: 0 !important;  }

        .boxItem .titItem {  text-align: center;  font-size: 36px;  }

        .boxItem div canvas {  position: relative !important;  top: 0 !important;  left: 0 !important;  }

        .boxtititem {  padding: 30px 0;  margin-bottom: 30px;  }

        .boxtititem a {  display: block;  float: left;  width: 80px;  line-height: 40px;  background: cornflowerblue;  color: #fff;  margin-left: 12px;  text-align: center;  border-radius: 5px;  text-decoration: none;  }

        .boxtititem a.on, .boxtititem a:hover {  background: palevioletred;  }
    </style>
</head>
<body>
<div class="boxtititem clearfix">
    <a href="index.php">总</a>
    <a href="month.php" class="on">月</a>
    <a href="day.php">日</a>
    <a href="weekSummary.php">周总结</a>
</div>
 <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div class="clearfix ItemBoxAll">
    <?php foreach ($results as $key => $result) {
        $montharray = [];
        foreach ($result as $item => $value) {
            foreach ($value['time_statistics']['month'] as $k => $v) {
                $montharray[] = $k;
            }
            $montharray = array_unique($montharray);
            sort($montharray);
        }

        ?>
        <div class="boxItem">
            <div class="titItem"><?php echo $key; ?></div>
            <div id="main<?php echo $id; ?>" style="width: 900px;height:400px;"></div>
            <script type="text/javascript">
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(document.getElementById('main<?php echo $id;?>'));

                option = {
                    color: ['#FFB6C1', '#DC143C', '#8B008B', '#0000FF', '#2F4F4F', '#2E8B57', '#FFFF00', '#FF8C00', '#000000', '#BC8F8F'],
                    tooltip: {
                        trigger: 'axis',
                    },
                    legend: {
                        x: 'right',
                        data: [<?php foreach($result as $item=>$value){ ?>'<?php echo $item ?>',<?php }; ?>]
                    },
                    grid: {
                        left: '4%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        name: '月份',
                        boundaryGap: false,
                        data: [<?php foreach($montharray as $item=>$value){ ?>'<?php echo $value ?>',<?php }; ?>]
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value}'
                        },
                        name: '代码量',
                    },
                    series:
                        [
                            <?php foreach($result as $item=>$value){ ?>
                            {
                                name: '<?php echo $item ?>',
                                type: 'line',
                                data: [<?php foreach ($value['time_statistics']['month'] as $item => $value) {
                                    echo $value . ',';
                                }; ?>],
                            },
                            <?php }?>
                        ]
                };

                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            </script>
        </div>
        <?php $id++;
    } ?>

</div>
</body>
</html>