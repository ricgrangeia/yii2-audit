<?php

use bedezign\yii2\audit\Audit;
use bedezign\yii2\audit\components\panels\Panel;
use dosamigos\chartjs\ChartJs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('audit', 'Summary');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('canvas {width: 100% !important;height: 400px;}');
?>
<div class="audit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-12 col-lg-12">
            <?php $form = ActiveForm::begin([
                'id' => 'entryform',
                'action' => [$this->context->action->id],
                'method' => 'get',
                'options' => [
                    'class' => 'form-inline'
                ],
            ]); ?>

            <?= $form->field($model, 'start_date', ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'style' => 'margin-right: 10px;']])
    			->textInput()->textInput(['placeholder' => "Start Date"])
                ->label(false)
    		?>

            <?= $form->field($model, 'end_date', 
                ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'style' => 'margin-right: 10px;']])
    			->textInput()->textInput(['placeholder' => "End Date"])
                ->label(false)
    		?>


            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'style' => 'margin-top: -10px']) ?>

            <?php ActiveForm::end(); ?>  
        </div>

        <div class="col-md-12 col-lg-12">
            <h2><?php echo Html::a(Yii::t('audit', 'Activity Log'), ['entry/index']); ?></h2>
            
            <div class="well">
                <?php

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
                        'labels' => array_keys($chartData),
                        'datasets' => [
                            [
                                'fillColor' => 'rgba(151,187,205,0.5)',
                                'strokeColor' => 'rgba(151,187,205,1)',
                                'pointColor' => 'rgba(151,187,205,1)',
                                'pointStrokeColor' => '#fff',
                                'data' => array_values($chartData),
                            ],
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>

        <?php
        
        foreach (Audit::getInstance()->panels as $panel) {
            /** @var Panel $panel */
            if($panel->getName() == 'Trails'){
                $request = null;
                if(isset(Yii::$app->request->queryParams['AuditTrailSearch'])){
                    $request = Yii::$app->request->queryParams['AuditTrailSearch'];
                }
                $chart = $panel->getChart($request);
            } else {
                $chart = $panel->getChart();
            }
            
            if (!$chart) {
                continue;
            }
            $indexUrl = $panel->getIndexUrl();
            ?>
            <div class="col-md-12 col-lg-12">
                <?php 
                $title = $panel->getName();
                if($panel->getName() == 'Trails'){
                    $title = Yii::t('audit', 'Database Log');
                }
                ?>
                <h2><?php echo $indexUrl ? Html::a($title, $indexUrl) : $title; ?></h2>
                <?php $form = ActiveForm::begin([
                    'id' => 'trailform',
                    'action' => [$this->context->action->id],
                    'method' => 'get',
                    'options' => [
                        'class' => 'form-inline'
                    ],
                ]); ?>

                <?= $form->field($trailModel, 'start_date', ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'style' => 'margin-right: 10px;']])
                    ->textInput()->textInput(['placeholder' => "Start Date"])
                    ->label(false)
                ?>

                <?= $form->field($trailModel, 'end_date', 
                    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'style' => 'margin-right: 10px;']])
                    ->textInput()->textInput(['placeholder' => "End Date"])
                    ->label(false)
                ?>


                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'style' => 'margin-top: -10px']) ?>

            <?php ActiveForm::end(); ?>  
                <div class="well">
                    <?php echo $chart; ?>
                </div>
            </div>
        <?php } ?>

    </div>

</div>

