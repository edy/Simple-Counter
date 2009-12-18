<?php
/**
* Simple Counter
*
* @author Eduard Baun
* @copyright (c) 2009 Eduard Baun <eduard@baun.de>
* @license GPLv2
*/

class Counter {
	
	private $config = array(
		'counter' => 'counter.dat',
		'ips' => 'ips.dat'
	);
	
	private $counter = 0;	
	private $counted = FALSE;
	private $current_ip = '0.0.0.0';
	
	public function __construct($config = array())
	{
		$this->config = $config + $this->config;
		
		$this->check_ip();
		$this->load_counter();
		
		if ( ! $this->counted)
		{
			$this->increment();
		}
	}
	
	public function __toString()
	{
		return (string) $this->counter;
	}
	
	private function load_counter()
	{
		if (file_exists($this->config['counter']))
		{
			$this->counter = (int) file_get_contents($this->config['counter']);
		}
		else
		{
			if (is_writable('.'))
			{
				file_put_contents($this->config['counter'], 0);
			}
			else
			{
				exit('Could not create "'. $this->config['counter'] .'"');
			}			
		}
	}
	
	private function check_ip()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$this->current_ip = md5($ip);
		
		if ( ! file_exists($this->config['ips']))
		{
			if (is_writable('.'))
			{
				touch($this->config['ips']);
			}
			else
			{
				exit('Could not create "'. $this->config['ips'] .'"');
			}
		}

		if ($file = fopen($this->config['ips'], 'r'))
		{
			while($line = stream_get_line($file, 128, "\n"))
			{
				if (strpos($line, $this->current_ip) !== FALSE)
				{
					$this->counted = TRUE;

					break;
				}
			}

			fclose($file);
		}
		else
		{
			exit('Could not open "'. $this->config['ips'] .'"');
		}
		
		if ( ! $this->counted)
		{
			file_put_contents($this->config['ips'], $this->current_ip."\n", FILE_APPEND);
		}
	}
	
	private function increment()
	{
		file_put_contents($this->config['counter'], ++$this->counter);
	}
}
