<?php 

class DefaultPeriodGenerator
{
	public function generate(\DateTime $startDate, \DateTime $endDate, \DateInterval $periodInterval):array
	{
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
			$normal[1] = $date;
			$chart['normal'][] = $normal;
		}

		return $chart;
	}
}
