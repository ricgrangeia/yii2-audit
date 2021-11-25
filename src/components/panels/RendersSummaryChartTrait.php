<?php
/**
 * Created by shalvah
 * Date: 9/1/17
 * Time: 12:10 PM
 */

namespace bedezign\yii2\audit\components\panels;

/**
 * Trait RendersSummaryChartTrait
 * @package bedezign\yii2\audit\components\panels
 *
 * Used by audit panels or controllers which want to render a summary chart in their view
 */
trait RendersSummaryChartTrait
{
    /**
     * The name of the model for which the chart should be rendered
     *
     * @return string Fully namespaced class name
     */
    protected function getChartModel()
    {
        return static::className();
    }

    /**
     * Return audit data for the last seven days
     * to be rendered on the chart
     *
     * @return array
     */
    protected function getChartData($start = null, $end = null)
    {
        //initialise defaults (0 entries) for each day
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

        $panelModel = $this->getChartModel();
        $results = $panelModel::find()
            ->select(["COUNT(DISTINCT id) as count", "created AS day"])
            ->where(['between', 'created',
                $startDate,
                $endDate])
            ->groupBy("created")->indexBy('day')->column();

        // replace defaults with data from db where available
        foreach ($results as $date => $count) {
            $date = date('D: Y-m-d', strtotime($date));
            $defaults[$date] += $count;
        }
        return $defaults;
    }
}
