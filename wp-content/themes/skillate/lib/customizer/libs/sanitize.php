<?php

if(!class_exists('SKILLATE_THMC_Sanitize')):
	/**
	* 
	*/
	class SKILLATE_THMC_Sanitize
	{

		public static function switch_sntz( $value )
		{
			if ($value === 'on') {
				$value = true;
			} else {
				$value = false;
			}
			
			return $value;
		}

		public static function multi_checkbox($value)
		{
			if (!empty($value)) {
				$value = explode(',', $value);
			} else {
				$value = array();
			}
			
			
			return $value;
		}

		public static function multi_select($value)
		{
			if (is_array($value)) {
				return $value;
			}

			return array();
		}

		public static function multi_select_js($value)
		{			
			if (is_array($value)) {
				return $value;
			}

			return array();
		}
	}
endif;