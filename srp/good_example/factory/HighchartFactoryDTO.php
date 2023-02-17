<?php 

class HighchartFactoryDTO
{
	private $renderId;
	private $title;
	private $yTitle;
	private $data;
	private $label;
	private $dateTimeLabelFormats;
	private $type = '';
	private $chart_max = null;
	private $tickInterval = null;
	
	public function getRenderId()
	{
		return $this->renderId;
	}

	public function setRenderId($renderId)
	{
		$this->renderId=$renderId;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title=$title;
		return $this;
	}
	
	public function getYTitle()
	{
		return $this->yTitle;
	}

	public function setYTitle($yTitle)
	{
		$this->yTitle=$yTitle;
		return $this;
	}
	
	public function getData()
	{
		return $this->data;
	}

	public function setData($data)
	{
		$this->data=$data;
		return $this;
	}
	
	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label=$label;
		return $this;
	}
	
	public function getDateTimeLabelFormats()
	{
		return $this->dateTimeLabelFormats;
	}

	public function setDateTimeLabelFormats($dateTimeLabelFormats)
	{
		$this->dateTimeLabelFormats=$dateTimeLabelFormats;
		return $this;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type=$type;
		return $this;
	}
	
	public function getChartMax()
	{
		return $this->chart_max;
	}

	public function setChartMax($chart_max)
	{
		$this->chart_max=$chart_max;
		return $this;
	}
	
	public function getTickInterval()
	{
		return $this->tickInterval;
	}

	public function setTickInterval($tickInterval)
	{
		$this->tickInterval=$tickInterval;
		return $this;
	}
}
