<?php

	class myCache {

		public $time = 60;
		public $dir = 'myCache';
		public $extension = 'html';
		public $startTime;
		public $endTime;
		public $cacheFile;
		public $write = true;

		public function __construct(){

			clearstatcache(); set_time_limit(86400); date_default_timezone_set("Europe/Istanbul");

			if ( !file_exists(dirname(__FILE__).'/'.$this->dir) ){
				mkdir(dirname(__FILE__).'/'.$this->dir, 0777);
			}

			$microTime = explode(' ', microtime());
			$this->startTime = $microTime[0] + $microTime[1];

			$this->cacheFile = dirname(__FILE__).'/'.$this->dir.'/'.md5($_SERVER['SCRIPT_NAME']).'.'.$this->extension;

			$changeTime = file_exists($this->cacheFile) ? filemtime($this->cacheFile) : 0;

			if ( time() - $this->time < $changeTime ){

				readfile($this->cacheFile);

				$this->write = false;

				exit();

			}else{

				if ( file_exists($this->cacheFile) ){ unlink($this->cacheFile); }
				ob_start();

			}

		}

		private function buffer($str){
			return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   ', '    '), '', $str);
		}

		private function writeCache($content){

			$content .= '<!-- myCache tarafından '.date('d.m.Y H:i:s').' tarihinde '.round($this->endTime - $this->startTime, 2).' milisaniyede oluşturuldu. -->';

			$cacheFileOpen = fopen($this->cacheFile, "w");
			fwrite($cacheFileOpen, $content);
			fclose($cacheFileOpen);

		}

		public function __destruct(){

			if ( $this->write ){

				$microTime = explode(' ', microtime());
				$this->endTime = $microTime[0] + $microTime[1];

				$this->writeCache($this->buffer(ob_get_contents()));

			}

			ob_end_flush();

		}

	}

?>