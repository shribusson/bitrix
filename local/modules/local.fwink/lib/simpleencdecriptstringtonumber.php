<?php


namespace Local\Fwink;


class SimpleEncDecriptStringToNumber
{
	public static function encrypt($input_str){

		$output_str = "";
		$charachters = str_split($input_str);

		for($i = 0 ; $i < sizeof($charachters) ; $i++){

			$output_str .= str_pad(ord($charachters[$i]), 3, "0", STR_PAD_LEFT);
		}
		return $output_str;
	}

	public static function decrypt($input_str){

		$input_str = preg_replace("/[^0-9]/", "", $input_str );
		$output_str = "";
		$charachters = str_split($input_str,3);

		for($i = 0 ; $i < sizeof($charachters) ; $i++){

			$output_str .= chr($charachters[$i]);
		}
		return $output_str;
	}

}
