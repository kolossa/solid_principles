<?php 

class DefaultPeriodGenerator
{
	private \DateTime $startDate;
	private \DateTime $endDate;
	private \DateInterval $periodInterval;
	
	public function __construct(\DateTime $startDate, \DateTime $endDate, \DateInterval $periodInterval)
	{
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->periodInterval = $periodInterval;
	}
	
	public function generate():array
	{
		$period = new DatePeriod($this->startDate, $this->periodInterval, $this->endDate);

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
