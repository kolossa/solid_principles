<?php 

class ChartDataConverter
{
	private HighchartDefaultPeriodGenerator $defaultGenerator;
	
	public function __construct(HighchartDefaultPeriodGenerator $defaultGenerator)
	{
		$this->defaultGenerator = $defaultGenerator;
	}
	
	public function convert(array $foglalasok, Closure $closure):array
	{
		//todo itt ne a default generátor-t használja, csak kapja meg a defaultData-t
		$defaultData = $this->defaultGenerator->generate();
		
		foreach($defaultData['normal'] as $key=>$data){
		
			$data[2] = $closure($data[1], $foglalasok);
			$defaultData['normal'][$key]=$data;
		}
		
		return $defaultData;
	}

}
