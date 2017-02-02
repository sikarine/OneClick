<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<div class="container">
    <br><br>
<?php
//error_reporting(E_ALL); 
//ini_set('display_errors', 1);
function cleanData($number, $dec_point=null) {
    if (empty($dec_point)) {
        $locale = localeconv();
        $dec_point = $locale['decimal_point'];
    }
    return floatval(str_replace($dec_point, '.', preg_replace('/[^\d'.preg_quote($dec_point).']/', '', $number)));
}
$result = file_get_contents($_FILES['report']['tmp_name']);

if((isset($_POST['plan'])) && ($_POST['plan'] > 0) && (isset($_POST['cost'])) && ($_POST['cost'] > 0))
{
    $margin = floatval($_POST['plan'])/floatval($_POST['cost']);
}
else
{
    $margin = 1;
}




//$result = iconv($in_charset = 'UTF-16LE' , $out_charset = 'UTF-8' , $csvData);

if (false === $result)
{
    throw new Exception('Input string could not be converted.');
}
$lines = explode(PHP_EOL, $result);
$array = array();
foreach ($lines as $line) {
    $pattern='/("[^",]+),([^"]*")/';
    $replacement='${1}$2';
    //echo $line."<br>";
    $line =  preg_replace($pattern, $replacement, $line);
    $array[] = explode(",",$line);
    //echo $line."<br>";
}

if (strpos($array[0][0], 'Ad report') !== false) {
    $report_type = 'google';
    $column_row = 1;
}

//echo "<pre>";
//var_dump($array); echo "</pre>"; exit();
//get column index
foreach($array[$column_row] as $index => $col)
{
    if($col == 'Ad')
    {
        $ads_index = $index;
    }

    if($col == 'Ad group')
    {
        $ads_group_index = $index;
    }

    if($col == 'Device')
    {
        $device_index = $index;
    }

    if($col == 'Ad')
    {
        $ads_index = $index;
    }

    if($col == 'Cost')
    {
        $cost_index = $index;
    }

    if($col == 'Impressions')
    {
        $impression_index = $index;
    }

    if($col == 'Clicks')
    {
        $click_index = $index;
    }
}

for($i=$column_row;$i>=0;$i--)
{
    unset($array[$i]);
}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<?php
foreach($array as $row)
{
    if(($row[$ads_index]<>"") && ($row[$ads_index]<>" --"))
    {
        

        if((cleanData($row[$cost_index]) > 0) || (cleanData($row[$impression_index]) > 0) || (cleanData($row[$click_index]) > 0))
        {
            if(!isset($data[$row[$ads_index]]))
            {
                $data[$row[$ads_index]]['Cost'] = 0;
                $data[$row[$ads_index]]['Impressions'] = 0;
                $data[$row[$ads_index]]['Clicks'] = 0;
            }
            $data[$row[$ads_index]]['Ad'] = $row[$ads_index];
            $data[$row[$ads_index]]['Cost'] += cleanData($row[$cost_index]);
            $data[$row[$ads_index]]['Impressions'] += cleanData($row[$impression_index]);
            $data[$row[$ads_index]]['Clicks'] += cleanData($row[$click_index]);
            $chart['xAxis'][$row[$ads_index]] = $row[$ads_index];
        }
    }
}
foreach($data as $index => $item)
{
    $chart['yAxis1'][] = round($item['Clicks']/$item['Impressions']*100,2);
    $chart['yAxis2'][] = round(round($item['Cost']*$margin,2)/$item['Clicks'],2);
}
ksort($data);
?>
<h3>Ads</h3>
<table class="table table-striped table-bordered">
    <tr>
        <th>Ads</th>
        <th>Cost</th>
        <th>Impressions</th>
        <th>Clicks</th>
        <th>Click-through Rate</th>
        <th>CPC</th>
    </tr>
    <?php
        $total_cost = 0;
        $total_impression = 0;
        $total_click = 0;
    ?>
    <?php foreach($data as $item): ?>
    <?php
        $total_cost += round($item['Cost']*$margin,2);
        $total_impression += $item['Impressions'];
        $total_click += $item['Clicks'];
    ?>
    <tr>
        <td><?php echo $item['Ad']; ?></td>
        <td class="text-right"><?php echo number_format(round($item['Cost']*$margin,2),2); ?></td>
        <td class="text-right"><?php echo number_format($item['Impressions'],0); ?></td>
        <td class="text-right"><?php echo number_format($item['Clicks'],0); ?></td>
        <td class="text-right"><?php echo round($item['Clicks']/$item['Impressions']*100,2); ?>%</td>
        <td class="text-right"><?php echo round(round($item['Cost']*$margin,2)/$item['Clicks'],2); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <th>Total</th>
        <td class="text-right"><?php echo number_format(round($total_cost,2),2); ?></td>
        <td class="text-right"><?php echo number_format($total_impression,0); ?></td>
        <td class="text-right"><?php echo number_format($total_click,0); ?></td>
        <td class="text-right"><?php echo round($total_click/$total_impression*100,2); ?>%</td>
        <td class="text-right"><?php echo round($total_cost/$total_click,2); ?></td>
    </tr>
</table>
<div id="ads" style="min-width: 310px; height: 400px; margin: 0 auto 100px"></div>
<script>
$(function () {
    Highcharts.chart('ads', {
        credits: {
            enabled: false
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Click-through Rate & Cost per Click'
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
            categories: [<?php echo "'".implode("','", $chart['xAxis'])."'"; ?>],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}%',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Click-through Rate',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'CPC',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        legend: {
            align: 'center',
            verticalAlign: 'bottom',
            x: 0,
            y: 0
        },
        series: [{
            name: 'Click-through Rate',
            type: 'column',
            yAxis: 1,
            data: [<?php echo implode(",", $chart['yAxis1']); ?>],
            dataLabels: {
                    enabled: true,
                    format: "{y}%"
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>',
                valueSuffix: ' %'
            }

        }, {
            name: 'CPC',
            type: 'scatter',
            data: [<?php echo implode(",", $chart['yAxis2']); ?>],
            dataLabels: {
                    enabled: true,
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>'
            }
        }]
    });
});
</script>
<?php
unset($data);
unset($chart);
foreach($array as $row)
{
    if(($row[$ads_group_index]<>"") && ($row[$ads_group_index]<>" --"))
    {
        
        if((cleanData($row[$cost_index]) > 0) || (cleanData($row[$impression_index]) > 0) || (cleanData($row[$click_index]) > 0))
        {
            if(!isset($data[$row[$ads_group_index]]))
            {
                $data[$row[$ads_group_index]]['Cost'] = 0;
                $data[$row[$ads_group_index]]['Impressions'] = 0;
                $data[$row[$ads_group_index]]['Clicks'] = 0;
            }
            $data[$row[$ads_group_index]]['Ad'] = $row[$ads_group_index];
            $data[$row[$ads_group_index]]['Cost'] += cleanData($row[$cost_index]);
            $data[$row[$ads_group_index]]['Impressions'] += cleanData($row[$impression_index]);
            $data[$row[$ads_group_index]]['Clicks'] += cleanData($row[$click_index]);
        }
        $chart['xAxis'][$row[$ads_group_index]] = $row[$ads_group_index];
    }
}
foreach($data as $index => $item)
{
    $chart['yAxis1'][] = round($item['Clicks']/$item['Impressions']*100,2);
    $chart['yAxis2'][] = round(round($item['Cost']*$margin,2)/$item['Clicks'],2);
}
ksort($data);
?>
<h3>Ad Group</h3>
<table class="table table-striped table-bordered">
    <tr>
        <th>Ad Group</th>
        <th>Cost</th>
        <th>Impressions</th>
        <th>Clicks</th>
        <th>Click-through Rate</th>
        <th>CPC</th>
    </tr>
    <?php
        $total_cost = 0;
        $total_impression = 0;
        $total_click = 0;
    ?>
    <?php foreach($data as $item): ?>
    <?php
        $total_cost += round($item['Cost']*$margin,2);
        $total_impression += $item['Impressions'];
        $total_click += $item['Clicks'];
    ?>
    <tr>
        <td><?php echo $item['Ad']; ?></td>
        <td class="text-right"><?php echo number_format(round($item['Cost']*$margin,2),2); ?></td>
        <td class="text-right"><?php echo number_format($item['Impressions'],0); ?></td>
        <td class="text-right"><?php echo number_format($item['Clicks'],0); ?></td>
        <td class="text-right"><?php echo round($item['Clicks']/$item['Impressions']*100,2); ?>%</td>
        <td class="text-right"><?php echo round(round($item['Cost']*$margin,2)/$item['Clicks'],2); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <th>Total</th>
        <td class="text-right"><?php echo number_format(round($total_cost,2),2); ?></td>
        <td class="text-right"><?php echo number_format($total_impression,0); ?></td>
        <td class="text-right"><?php echo number_format($total_click,0); ?></td>
        <td class="text-right"><?php echo round($total_click/$total_impression*100,2); ?>%</td>
        <td class="text-right"><?php echo round($total_cost/$total_click,2); ?></td>
    </tr>
</table>
<div id="ad-group" style="min-width: 310px; height: 400px; margin: 0 auto 100px"></div>
<script>
$(function () {
    Highcharts.chart('ad-group', {
        credits: {
            enabled: false
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Click-through Rate & Cost per Click'
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
            categories: [<?php echo "'".implode("','", $chart['xAxis'])."'"; ?>],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}%',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Click-through Rate',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'CPC',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        legend: {
            align: 'center',
            verticalAlign: 'bottom',
            x: 0,
            y: 0
        },
        series: [{
            name: 'Click-through Rate',
            type: 'column',
            yAxis: 1,
            data: [<?php echo implode(",", $chart['yAxis1']); ?>],
            dataLabels: {
                    enabled: true,
                    format: "{y}%"
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>',
                valueSuffix: ' %'
            }

        }, {
            name: 'CPC',
            type: 'scatter',
            data: [<?php echo implode(",", $chart['yAxis2']); ?>],
            dataLabels: {
                    enabled: true,
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>'
            }
        }]
    });
});
</script>
<?php
unset($data);
unset($chart);
foreach($array as $row)
{

    if(($row[$device_index]<>"") && ($row[$device_index]<>" --") && (strpos($row[$device_index], 'Total') === false))
    {
        
        if((cleanData($row[$cost_index]) > 0) || (cleanData($row[$impression_index]) > 0) || (cleanData($row[$click_index]) > 0))
        {
            if(!isset($data[$row[$device_index]]))
            {
                $data[$row[$device_index]]['Cost'] = 0;
                $data[$row[$device_index]]['Impressions'] = 0;
                $data[$row[$device_index]]['Clicks'] = 0;
            }
            $data[$row[$device_index]]['Ad'] = $row[$device_index];
            $data[$row[$device_index]]['Cost'] += cleanData($row[$cost_index]);
            $data[$row[$device_index]]['Impressions'] += cleanData($row[$impression_index]);
            $data[$row[$device_index]]['Clicks'] += cleanData($row[$click_index]);
            $chart['xAxis'][$row[$device_index]] = $row[$device_index];
        }
    }
}
foreach($data as $index => $item)
{
    $chart['yAxis1'][] = round($item['Clicks']/$item['Impressions']*100,2);
    $chart['yAxis2'][] = round(round($item['Cost']*$margin,2)/$item['Clicks'],2);
}
ksort($data);
?>

<h3>Placement</h3>
<table class="table table-striped table-bordered">
    <tr>
        <th>Placement</th>
        <th>Cost</th>
        <th>Impressions</th>
        <th>Clicks</th>
        <th>Click-through Rate</th>
        <th>CPC</th>
    </tr>
    <?php
        $total_cost = 0;
        $total_impression = 0;
        $total_click = 0;
    ?>
    <?php foreach($data as $item): ?>
    <?php
        $total_cost += round($item['Cost']*$margin,2);
        $total_impression += $item['Impressions'];
        $total_click += $item['Clicks'];
    ?>
    <tr>
        <td><?php echo $item['Ad']; ?></td>
        <td class="text-right"><?php echo number_format(round($item['Cost']*$margin,2),2); ?></td>
        <td class="text-right"><?php echo number_format($item['Impressions'],0); ?></td>
        <td class="text-right"><?php echo number_format($item['Clicks'],0); ?></td>
        <td class="text-right"><?php echo round($item['Clicks']/$item['Impressions']*100,2); ?>%</td>
        <td class="text-right"><?php echo round(round($item['Cost']*$margin,2)/$item['Clicks'],2); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <th>Total</th>
        <td class="text-right"><?php echo number_format(round($total_cost,2),2); ?></td>
        <td class="text-right"><?php echo number_format($total_impression,0); ?></td>
        <td class="text-right"><?php echo number_format($total_click,0); ?></td>
        <td class="text-right"><?php echo round($total_click/$total_impression*100,2); ?>%</td>
        <td class="text-right"><?php echo round($total_cost/$total_click,2); ?></td>
    </tr>
</table>
<div id="device" style="min-width: 310px; height: 400px; margin: 0 auto 100px"></div>
<script>
$(function () {
    Highcharts.chart('device', {
        credits: {
            enabled: false
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Click-through Rate & Cost per Click'
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
            categories: [<?php echo "'".implode("','", $chart['xAxis'])."'"; ?>],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}%',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Click-through Rate',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'CPC',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        legend: {
            align: 'center',
            verticalAlign: 'bottom',
            x: 0,
            y: 0
        },
        series: [{
            name: 'Click-through Rate',
            type: 'column',
            yAxis: 1,
            data: [<?php echo implode(",", $chart['yAxis1']); ?>],
            dataLabels: {
                    enabled: true,
                    format: "{y}%"
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>',
                valueSuffix: ' %'
            }

        }, {
            name: 'CPC',
            type: 'scatter',
            data: [<?php echo implode(",", $chart['yAxis2']); ?>],
            dataLabels: {
                    enabled: true,
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>'
            }
        }]
    });
});
</script>
</div>