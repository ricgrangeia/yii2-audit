<?php

namespace bedezign\yii2\audit\controllers;

use bedezign\yii2\audit\components\panels\RendersSummaryChartTrait;
use bedezign\yii2\audit\components\web\Controller;
use bedezign\yii2\audit\models\AuditEntry;
use bedezign\yii2\audit\models\AuditEntrySearch;
use bedezign\yii2\audit\models\AuditTrailSearch;
use Yii;

/**
 * DefaultController
 * @package bedezign\yii2\audit\controllers
 */
class DefaultController extends Controller
{
    use RendersSummaryChartTrait;

    /**
     * Module Default Action.
     * @return mixed
     */
    public function actionIndex()
    {
        $startDate = $endDate = null;
        $searchModel = new AuditEntrySearch();
        if(isset(Yii::$app->request->queryParams['AuditEntrySearch'])){
            $startDate = Yii::$app->request->queryParams['AuditEntrySearch']['start_date'];
            $endDate = Yii::$app->request->queryParams['AuditEntrySearch']['end_date'];
        }
        $chartData = $this->getChartData($startDate, $endDate);
        $searchTrailModel = new AuditTrailSearch(); 
        return $this->render('index', ['model' => $searchModel, 'chartData' => $chartData, 'trailModel' => $searchTrailModel]);
    }

    protected function getChartModel()
    {
        return AuditEntry::className();
    }
}
