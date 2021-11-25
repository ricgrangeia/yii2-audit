<?php
/* @var $panel TrailPanel */

use bedezign\yii2\audit\models\AuditTrail;
use bedezign\yii2\audit\panels\TrailPanel;
use dosamigos\chartjs\ChartJs;

//initialise defaults (0 entries) for each day
// dd($chartData);
$defaults = [];
if(!$start && !$end) {
    $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
    $endDate =  date('Y-m-d 23:59:59');
    foreach (range(-6, 0) as $day) {
        $defaults[date('D: Y-m-d', strtotime($day . 'days'))] = 0;
    }
} else {
    $startDate = date('Y-m-d 00:00:00', strtotime($start));
    $endDate = date('Y-m-d 23:59:59', strtotime($end));
    $days = round(((strtotime($endDate) - strtotime($startDate))/ (60 * 60 * 24))-1);
    foreach (range(0, $days) as $day) {
        $defaults[date('D: Y-m-d', strtotime($startDate.'+' .$day . 'days'))] = 0;
    }
}

$results = AuditTrail::find()
    ->select(["COUNT(DISTINCT id) as count", "created AS day"])
    ->where(['between', 'created',
        $startDate,
        $endDate])
    ->groupBy("created")->indexBy('day')->column();

// format dates properly
foreach ($results as $date => $count) {
    $date = date('D: Y-m-d', strtotime($date));
    $defaults[$date] += $count;
}

// return $defaults;
// dd($defaults);
echo ChartJs::widget([
    'type' => 'bar',
    'options' => [
        'height' => '400',
    ],
    'clientOptions' => [
        'legend' => ['display' => false],
        'tooltips' => ['enabled' => false],
    ],
    'data' => [
        'labels' => array_keys($defaults),
        'datasets' => [
            [
                'fillColor' => 'rgba(255, 99, 71, 0.8)',
                'strokeColor' => 'rgba(255, 99, 71, 0.8)',
                'pointColor' => 'rgba(255, 99, 71, 0.8)',
                'pointStrokeColor' => '#fff',
                'data' => array_values($defaults),
            ],
        ],
    ]
]);
