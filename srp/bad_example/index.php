<?php

if ( !isset($_GET['fromDate']) ) {
  $fromDateTime = new DateTime();
  $fromDateTime->sub(new DateInterval('P30D'));
  $_GET['fromDate'] = $fromDateTime->format('Y-m-d');
}

if ( !isset($_GET['toDate']) ) {
  $toDateTime = new DateTime();
  $_GET['toDate'] = $toDateTime->format('Y-m-d');
}

$dataProvider = isset($_GET['foglalas']) ? $_GET['foglalas'] : [];
$foglalasokHUF = array();

foreach ( $dataProvider as $foglalas ) {
  $foglalasokHUF[$foglalas['jelDateClean']] = $foglalas;
}

$monthlyDataProvider = isset($_GET['monthly_foglalas']) ? $_GET['monthly_foglalas'] : [];
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




$this->getController()->render('foglalasokStatWithGraph', array(
  'chartHUF' => $chartHUF,
  'monthlyChartHuf' => $monthlyChartHuf,
));
