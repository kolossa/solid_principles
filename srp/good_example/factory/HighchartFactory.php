<?php 

class HighchartFactory
{
	public function getChart()
	{
		$chart = new Highchart();

		$chart->chart->renderTo = $renderId;
		$chart->chart->type = 'column';
		$chart->chart->zoomType = 'x';
		$chart->chart->style->margin = '0 auto';
		$chart->chart->height = 240;

		$chart->title->text = $title;

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
		$chart->yAxis->title->text = $yTitle;
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
		$chart->series[] = array('name' => $label, 'data' => $data['normal'], 'type' => $type);

		return $chart;	
	}
}
