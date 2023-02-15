<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<meta name='robots' content='noindex,follow' />


	<link rel="stylesheet" type="text/css" href="/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />

	<link rel="stylesheet" type="text/css" href="/css/main.css" />
	<link rel="stylesheet" type="text/css" href="/css/form.css" />
	<link rel="stylesheet" type="text/css" href="/css/adminstilus.css" />
	
	<link rel="apple-touch-icon" href="/custom_icon.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/touch-icon-ipad.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/touch-icon-iphone-retina.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/touch-icon-ipad-retina.png">

	<title>Foglalások grafikon</title>
</head>
<body>

<div class="container" id="page">

<h1>Foglalások grafikon</h1>


<?php

$criteria = new CDbCriteria;
$criteria->with = array('partner', 'utasok', 'ut', 'transactions', 'szhot', 'szamlat', 'szamlau', 'dijt', 'szlaf', 'szlai');
$criteria->group = 'SUBSTRING(t.tn_szam, 1, 8), t.p_valuta';
$criteria->compare('t.id', $_GET['id']);
$criteria->compare('t.reg_mod', $_GET['reg_mod']);
$criteria->compare('t.tn_szam', $_GET['tn_szam']);
$criteria->compare('t.poz_szam', $_GET['poz_szam'], true, 'AND', false);
$criteria->compare('t.poz_turn', $_GET['poz_turn'], true);
$criteria->compare('t.ut_kod', $_GET['ut_kod'], true);
$criteria->compare('t.plus_turn', $_GET['plus_turn']);
$criteria->compare('t.ki_date', $_GET['ki_date'], true);
$criteria->compare('t.be_date', $_GET['be_date'], true);
$criteria->compare('t.tour_oper', $_GET['tour_oper'], true);
if ( $_GET['poziLike'] != '' ) {
  $criteria->addCondition('t.poz_szam LIKE :poziLike');
  $criteria->params[':poziLike'] = $_GET['poziLike'];
}
if ( $_GET['poziNotLike'] != '' ) {
  $criteria->addCondition('t.poz_szam NOT LIKE :poziNotLike');
  $criteria->params[':poziNotLike'] = $_GET['poziNotLike'];
}

if ( $_GET['whoSold'] == 1 ) {
  $criteria->with[] = 'partner';
  $criteria->addCondition('partner.p_cegnev1 LIKE :partner');
  $criteria->params[':partner'] = '%test_firm%';
}
if ( $_GET['jelDateClean'] != null ) {
  $criteria->addCondition('substring(t.tn_szam, 1, 8) LIKE \'%' . str_replace('-', '', $_GET['jelDateClean']) . '%\'');
}

if ( !isset($_GET['fromDate']) ) {
  $fromDateTime = new DateTime();
  $fromDateTime->sub(new DateInterval('P30D'));
  $_GET['fromDate'] = $fromDateTime->format('Y-m-d');
}

if ( !isset($_GET['toDate']) ) {
  $toDateTime = new DateTime();
  $_GET['toDate'] = $toDateTime->format('Y-m-d');
}
$dataProvider = new CActiveDataProvider($this, array(
  'criteria' => $criteria,
  'pagination'=>false,
));
$foglalasokHUF = array();

foreach ( $dataProvider as $foglalas ) {
  $foglalasokHUF[$foglalas['jelDateClean']] = $foglalas;
}


$criteria = new CDbCriteria;
$criteria->with = array('partner', 'utasok', 'ut', 'transactions', 'szhot', 'szamlat', 'szamlau', 'dijt', 'szlaf', 'szlai');
$criteria->group = 'SUBSTRING(t.tn_szam, 1, 6), t.p_valuta';
$criteria->compare('t.id', $_GET['id']);
$criteria->compare('t.reg_mod', $_GET['reg_mod']);
$criteria->compare('t.tn_szam', $_GET['tn_szam']);
$criteria->compare('t.poz_szam', $_GET['poz_szam'], true, 'AND', false);
$criteria->compare('t.poz_turn', $_GET['poz_turn'], true);
$criteria->compare('t.ut_kod', $_GET['ut_kod'], true);
$criteria->compare('t.plus_turn', $_GET['plus_turn']);
$criteria->compare('t.ki_date', $_GET['ki_date'], true);
$criteria->compare('t.be_date', $_GET['be_date'], true);
$criteria->compare('t.tour_oper', $_GET['tour_oper'], true);
if ( $_GET['jelDateClean'] != null ) {
  $criteria->addCondition('substring(t.tn_szam, 1, 8) LIKE \'%' . str_replace('-', '', $_GET['jelDateClean']) . '%\'');
}
$monthlyDataProvider = new CActiveDataProvider($this, array(
  'criteria' => $criteria,
  'pagination'=>false,
));
$monthlyFoglalasokHUF = array();

foreach ( $monthlyDataProvider as $foglalas ) {
  $monthlyFoglalasokHUF[substr($foglalas['jelDateClean'], 0, 7)] = $foglalas;
}

$periodInterval = new DateInterval('P1D');
$startDate = DateTime::createFromFormat('Y-m-d', $_GET['fromDate']);
$endDate = DateTime::createFromFormat('Y-m-d', $_GET['toDate']);
$endDate->add($periodInterval);

$period = new DatePeriod($startDate, $periodInterval, $endDate);

$chart = array(
  'normal' => array(),
);
foreach ( $period as $date ) {

  $year = $date->format('Y');
  $month = (int)$date->format('m') - 1; //ez kell a Date.UTC miatt
  $day = $date->format('d');

  $key = new HighchartJsExpr('Date.UTC(' . $year . ', ' . $month . ', ' . $day . ')');

  $normal = array();
  $normal[0] = $key;
  $normal[1] = function ($date, array $foglalasokHUF) {

    if ( isset($foglalasokHUF[$date->format('Y-m-d')]) ) {
      return (int)$foglalasokHUF[$date->format('Y-m-d')]->sumFizetendo;
    }
  
    return 0;
  };
  $chart['normal'][] = $normal;
}
$chartHUFData = $chart;

$periodInterval = new DateInterval('P1M');
$startDate = DateTime::createFromFormat('Y-m-d', $_GET['fromDate']);
$endDate = DateTime::createFromFormat('Y-m-d', $_GET['toDate']);
$endDate->add($periodInterval);

$period = new DatePeriod($startDate, $periodInterval, $endDate);

$chart = array(
  'normal' => array(),
);
foreach ( $period as $date ) {

  $year = $date->format('Y');
  $month = (int)$date->format('m') - 1; //ez kell a Date.UTC miatt
  $day = '01';

  $key = new HighchartJsExpr('Date.UTC(' . $year . ', ' . $month . ', ' . $day . ')');

  $normal = array();
  $normal[0] = $key;
  $normal[1] = function ($date, $monthlyFoglalasokHUF) {

    if ( isset($monthlyFoglalasokHUF[$date->format('Y-m')]) ) {
      return (int)$monthlyFoglalasokHUF[$date->format('Y-m')]->sumFizetendo;
    }
  
    return 0;
  };
  $chart['normal'][] = $normal;
}
$monthlyChartHufData = $chart;

$chart_max=null;
$chart = new Highchart();

$chart->chart->renderTo = 'priceContainer';
$chart->chart->type = 'column';
$chart->chart->zoomType = 'x';
$chart->chart->style->margin = '0 auto';
$chart->chart->height = 240;

$chart->title->text = 'Foglalások napi HUF';

$chart->xAxis->reversed = false;
$chart->xAxis->title->enabled = true;
$chart->xAxis->title->text = 'Dátum';
$chart->xAxis->maxZoom = 86400000;
$chart->xAxis->showLastLabel = true;
$chart->xAxis->type = "datetime";
$chart->xAxis->dateTimeLabelFormats = array(
  'day' => '%b. %e %a',
);

$chart->yAxis->showFirstLabel = false;
$chart->yAxis->title->text = 'Összeg';
$chart->yAxis->type = "linear";
if ( !is_null($chart_max) && $chart_max > 0 ) {
  $chart->yAxis->max = $chart_max;
  $chart->yAxis->tickPositioner = new HighchartJsExpr("function(){var positions=[],tick=Math.floor(this.dataMin);if(this.dataMax>1000000){for(var i=0;i<=this.dataMax;i+=500000){positions.push(i)}}else if(this.dataMax>100000){for(var i=0;i<=this.dataMax;i+=50000){positions.push(i)}}else{for(var i=0;i<=this.dataMax;i+=1000){positions.push(i)}}positions.push(this.dataMax);return positions}");
}


/**
 * EREDETI JS
 * function() {
 * var s = '<span style="font-size:10px">' + Highcharts.dateFormat('%Y-%m-%d', this.x) + '</span><table>';
 * $.each(this.points, function(i, point) {
 * s += '<tr><td style="color:'+ point.series.color + ';padding:0">' + point.series.name + ': </td><td style="padding:0; text-align: right;"><b>' + Highcharts.numberFormat(this.y, 0) + ' db</b></td></tr>';
 * });
 * s += '</table>';
 * return s;
 * }*/
$chart->tooltip->formatter = new HighchartJsExpr("function(){var s='<span style=\"font-size:10px\">'+Highcharts.dateFormat('%Y-%m-%d',this.x)+'</span><table>';$.each(this.points,function(i,point){s+='<tr><td style=\"color:'+point.series.color+';padding:0\">'+point.series.name+': </td><td style=\"padding:0; text-align: right;\"><b>'+Highcharts.numberFormat(this.y,0)+' </b></td></tr>'});s+='</table>';return s}");
$chart->tooltip->shared = true;
$chart->tooltip->useHTML = true;

$chart->legend->enabled = false;

$chart->plotOptions->spline->marker->enable = false;
$chart->series[] = array('name' => 'Foglalás HUF', 'data' => $chartHUFData['normal'], 'type' => '');

$chartHUF = $chart;



$chart_max=null;
$chart = new Highchart();

$chart->chart->renderTo = 'monthlyChartHufContainer';
$chart->chart->type = 'column';
$chart->chart->zoomType = 'x';
$chart->chart->style->margin = '0 auto';
$chart->chart->height = 240;

$chart->title->text = 'Foglalások havi HUF';

$chart->xAxis->reversed = false;
$chart->xAxis->title->enabled = true;
$chart->xAxis->title->text = 'Dátum';
$chart->xAxis->maxZoom = 86400000;
$chart->xAxis->showLastLabel = true;
$chart->xAxis->tickInterval = 1000 * 3600 * 24 * 30; // 1 month
$chart->xAxis->type = "datetime";
$chart->xAxis->dateTimeLabelFormats = array(
  'month' => '%Y. %m ',
);

$chart->yAxis->showFirstLabel = false;
$chart->yAxis->title->text = 'Összeg';
$chart->yAxis->type = "linear";
if ( !is_null($chart_max) && $chart_max > 0 ) {
  $chart->yAxis->max = $chart_max;
  $chart->yAxis->tickPositioner = new HighchartJsExpr("function(){var positions=[],tick=Math.floor(this.dataMin);if(this.dataMax>1000000){for(var i=0;i<=this.dataMax;i+=500000){positions.push(i)}}else if(this.dataMax>100000){for(var i=0;i<=this.dataMax;i+=50000){positions.push(i)}}else{for(var i=0;i<=this.dataMax;i+=1000){positions.push(i)}}positions.push(this.dataMax);return positions}");
}


/**
 * EREDETI JS
 * function() {
 * var s = '<span style="font-size:10px">' + Highcharts.dateFormat('%Y-%m-%d', this.x) + '</span><table>';
 * $.each(this.points, function(i, point) {
 * s += '<tr><td style="color:'+ point.series.color + ';padding:0">' + point.series.name + ': </td><td style="padding:0; text-align: right;"><b>' + Highcharts.numberFormat(this.y, 0) + ' db</b></td></tr>';
 * });
 * s += '</table>';
 * return s;
 * }*/
$chart->tooltip->formatter = new HighchartJsExpr("function(){var s='<span style=\"font-size:10px\">'+Highcharts.dateFormat('%Y-%m',this.x)+'</span><table>';$.each(this.points,function(i,point){s+='<tr><td style=\"color:'+point.series.color+';padding:0\">'+point.series.name+': </td><td style=\"padding:0; text-align: right;\"><b>'+Highcharts.numberFormat(this.y,0)+' </b></td></tr>'});s+='</table>';return s}");
$chart->tooltip->shared = true;
$chart->tooltip->useHTML = true;

$chart->legend->enabled = false;

$chart->plotOptions->spline->marker->enable = false;
$chart->series[] = array('name' => 'Foglalás HUF', 'data' => $monthlyChartHufData['normal'], 'type' => "");

$monthlyChartHuf = $chart;




?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'foglalas-sum-stat-grid',
	'dataProvider' => $sumDataProvider,
	'ajaxUpdate' => false,
	'template' => "{items}",
	'columns' => array(
		array(
			'header' => 'összes foglalás',
			'type' => 'raw',
			'value' => function ($data) {
				return ManageRule::addThousandPoint($data['all_foglalás'], 'HUF');
			},
			'htmlOptions' => array('class' => 'numeric'),
		),
		array(
			'header' => 'HUF',
			'type' => 'raw',
			'value' => function ($data) {
				return ManageRule::addThousandPoint($data['HUF'], 'HUF');
			},
			'htmlOptions' => array('class' => 'numeric'),
		),
		array(
			'header' => '€',
			'type' => 'raw',
			'value' => function ($data) {
				if ( !isset($data['EUR']) ) {
					return;
				}

				return ManageRule::addThousandPoint($data['EUR'], 'EUR');
			},
			'htmlOptions' => array('class' => 'numeric'),
		),
		array(
			'header' => '$',
			'type' => 'raw',
			'value' => function ($data) {
				if ( !isset($data['USD']) ) {
					return;
				}

				return ManageRule::addThousandPoint($data['USD'], 'USD');
			},
			'htmlOptions' => array('class' => 'numeric'),
		),
	),
));

?>

<div class="search-form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'foglalas-stat-form',
		'enableAjaxValidation' => false,
		'method' => 'get',
		'action' => Yii::app()->createUrl($this->route),
	)); ?>

	<div class="row" style="display:inline-block;">
		<?php echo $form->labelEx($model, 'from'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'fromDate',
			'language' => 'hu',
			'options' => array(
				'dateFormat' => 'yy-mm-dd',
				'changeMonth' => 'true',
				'changeYear' => 'true',
				'showButtonPanel' => 'true',
			),
		));
		?>
	</div>

	<div class="row" style="display:inline-block;">
		<?php echo $form->labelEx($model, 'to'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'toDate',
			'language' => 'hu',
			'options' => array(
				'dateFormat' => 'yy-mm-dd',
				'changeMonth' => 'true',
				'changeYear' => 'true',
				'showButtonPanel' => 'true',
			),
		));
		?>
	</div>

	<div class="row" style="display:inline-block;">
		<?php echo $form->labelEx($model, 'poziLike'); ?>
		<?php echo $form->textField($model, 'poziLike'); ?>
	</div>

	<div class="row" style="display:inline-block;">
		<?php echo $form->labelEx($model, 'poziNotLike'); ?>
		<?php echo $form->textField($model, 'poziNotLike'); ?>
	</div>

	<div class="row" style="display:inline-block;">
		<?php echo $form->labelEx($model, 'whoSold'); ?>
		<?php echo $form->dropDownList($model, 'whoSold', array(
			0 => '-',
			1 => 'nincs partner',
			2 => 'van partner',
		)); ?>
	</div>

	<?php
	echo CHtml::submitButton('Szűrés');
	?>


</div>
<?php
echo '<div id="priceContainer"></div>';
echo '<div id="HUFCountContainer"></div>';
echo '<div id="EURContainer"></div>';
echo '<div id="EURCountContainer"></div>';
echo '<div id="monthlyChartHufContainer"></div>';
echo '<div id="monthlyChartHufCountContainer"></div>';
echo '<div id="monthlyChartEURContainer"></div>';
echo '<div id="monthlyChartEURCountContainer"></div>';

echo '<script type="text/javascript">';
echo $chartHUF->render("chart1");
echo $chartHUFCount->render("chart1");
echo $chartEUR->render("chart1");
echo $chartEURCount->render("chart1");
echo $monthlyChartHuf->render("chart1");
echo $monthlyChartEUR->render("chart1");
echo $monthlyChartHufCount->render("chart1");
echo $monthlyChartEURCount->render("chart1");
echo '</script>';

$this->endWidget();

</div>
</body>
</html>