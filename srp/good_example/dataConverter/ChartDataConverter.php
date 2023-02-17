<?php 

class ChartDataConverter
{
	private DefaultPeriodGenerator $defaultGenerator;
	
	public function __construct(DefaultPeriodGenerator $defaultGenerator)
	{
		$this->defaultGenerator = $defaultGenerator;
	}
	
	public function convert(array $foglalasok, Closure $closure):array
	{
		$defaultData = $this->defaultGenerator->generate();
		
		foreach($defaultData['normal'] as $key=>$data){
		
			$data[2] = $closure($data[1], $foglalasok);
			$defaultData['normal'][$key]=$data;
		}
		
		return $defaultData;
	}

}
