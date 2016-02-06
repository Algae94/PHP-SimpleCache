<?php

//namespace Gilbitron\Util;

/*
 * SimpleCache v1.4.1
 *
 * By Gilbert Pellegrom
 * http://dev7studios.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
class SimpleCache {

	// Path to cache folder (with trailing /)
	public $cache_path = 'cache/';
	// Length of time to cache a file (in seconds)
	public $cache_time = 3600;
        //percent cache variance randomization
        public $cache_time_percent_variance = .15;
	// Cache file extension
	public $cache_extension = '.cache';
	
	public function set_cache($label, $data)
	{
		file_put_contents($this->cache_path . $this->safe_filename($label) . $this->cache_extension, $data);
	}

	public function get_cache($label)
	{
		if($this->is_cached($label)){
			$filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;
			return file_get_contents($filename);
		}

		return false;
	}

	public function is_cached($label)
	{
		$filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;

		if(file_exists($filename) && (filemtime($filename) + $this->get_random_cache_length() >= time())) return true;

		return false;
	}

	//Helper function for retrieving data from url
	public function do_curl($url)
	{
		if(function_exists("curl_init")){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		} else {
			return file_get_contents($url);
		}
	}

	//Helper function to validate filenames
	private function safe_filename($filename)
	{
		return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
	}
        
        //Helper function to return cache length randomization
        private function get_random_cache_length()
        {
            $variance = round($this->cache_time*$this->cache_time_percent_variance);
            return rand($this->cache_time - $variance, $this->cache_time + $variance);
        }
}
