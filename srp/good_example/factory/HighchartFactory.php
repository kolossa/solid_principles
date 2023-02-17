<?php 

class HighchartFactory
{
	public function getChart(HighchartFactoryDTO $dto)
	{
		$chart = new Highchart();

		$chart->chart->renderTo = $dto->getRenderId();
		$chart->chart->type = 'column';
		$chart->chart->zoomType = 'x';
		$chart->chart->style->margin = '0 auto';
		$chart->chart->height = 240;

		$chart->title->text = $dto->getTitle();

		$chart->xAxis->reversed = false;
		$chart->xAxis->title->enabled = true;
		$chart->xAxis->title->text = 'Dátum';
		$chart->xAxis->maxZoom = 86400000;
		$chart->xAxis->showLastLabel = true;
		if($dto->getTickInterval()){
			$chart->xAxis->tickInterval = $dto->getTickInterval();
		}
		$chart->xAxis->type = "datetime";
		$chart->xAxis->dateTimeLabelFormats = $dto->getDateTimeLabelFormats();

		$chart->yAxis->showFirstLabel = false;
		$chart->yAxis->title->text = $dto->getYTitle();
		$chart->yAxis->type = "linear";
		if ( !is_null($dto->getChartMax()) && $dto->getChartMax() > 0 ) {
			$chart->yAxis->max = $dto->getChartMax();
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
		$chart->series[] = array('name' => $dto->getLabel(), 'data' => $dto->getData()['normal'], 'type' => $dto->getType());

		return $chart;	
	}
}
