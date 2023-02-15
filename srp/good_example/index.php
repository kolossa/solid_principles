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
