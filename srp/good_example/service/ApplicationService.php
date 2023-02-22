<?php 

class ApplicationService
{
	public function execute()
	{
		//todo ez ne ChartRepository legyen, hanem bookingRepository
		$repository = new ChartRepository();
		$bookingData = $repository->getChartData($_GET['chartData']);
		
		//$monthlyChartData = $repository->getMonthlyChartData($_GET['monthlyChartData']); //todo ez megy a monthlyba

		//todo $startDate, $endDate, $periodInterval
		$defaultDataGenerator = HighchartDefaultPeriodGenerator($startDate, $endDate, $periodInterval);
		$dataConverter = new ChartDataConverter($defaultDataGenerator);
		//todo closure
		$chartData = $dataConverter->convert($bookingData, $closure);

		//todo dto feltöltés
		$dto = new HighchartFactoryDTO();
		$dto->setRenderId()
			->setTitle()
			->setYTitle()
			->setData($chartData)
			->setLabel()
			->setDateTimeLabelFormats()
			->setType()
			->setChartMax()
			->setTickInterval();

		$chartFactory = new HighchartFactory();
		$chart = $chartFactory->getChart($dto)

		return $chart;
		//todo újabb függvénybe mehet a monthly
	}
}
