<?php
namespace Solar;

class Color
{
	public $r;
	public $g;
	public $b;

	public function __construct($r, $g, $b)
	{
		$this->r = intval($r % 0x100);
		$this->g = intval($g % 0x100);
		$this->b = intval($b % 0x100);
	}

	public function __toString()
	{
		return $this->ToCSS();
	}

	//
	public static function FromRGB($rgb) : self
	{
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		return new Color($r, $g, $b);
	}

	public function ToRGB() : int
	{
		return ($this->b << 16) + ($this->g << 8) + $this->r;
	}
	//

	public function ToHEX($hash = true) : string
	{
		$number = $this->ToRGB();
		$number = dechex($number);
		$hex = substr("000000", 0, 6 - strlen($number)).$number;
		$hex = strrev($hex);
		return $hash ? "#".$hex : $hex;
	}

	public static function FromHEX($hex) : self
	{
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		return new Color($r, $g, $b); // returns an array with the rgb values
	}

	//

	public function ToHSV() : array
	{
		$HSL = array();

		$var_R = ($this->r / 255);
		$var_G = ($this->g / 255);
		$var_B = ($this->b / 255);

		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;

		$V = $var_Max;
		$H = 0;

		if ($del_Max == 0)
		{
			$H = 0;
			$S = 0;
		}
		else
		{
			$S = $del_Max / $var_Max;

			$del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
			$del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
			$del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;

			if		($var_R == $var_Max) $H = $del_B - $del_G;
			else if ($var_G == $var_Max) $H = (1 / 3) + $del_R - $del_B;
			else if ($var_B == $var_Max) $H = (2 / 3) + $del_G - $del_R;

			if ($H < 0) $H++;
			if ($H > 1) $H--;
		}

		$HSL["H"] = $H;
		$HSL["S"] = $S;
		$HSL["V"] = $V;

		return $HSL;
	}

	public static function FromHSV(array $HSV) : self
	{
		$H = $HSV["H"];
		$S = $HSV["S"];
		$V = $HSV["V"];
		//
		$R = 0;
		$G = 0;
		$B = 0;
		//1
		$H *= 6;
		//2
		$I = floor($H);
		$F = $H - $I;
		//3
		$M = $V * (1 - $S);
		$N = $V * (1 - $S * $F);
		$K = $V * (1 - $S * (1 - $F));
		//4
		switch ($I) {
			case 0:
				list($R, $G, $B) = array($V, $K, $M);
				break;
			case 1:
				list($R, $G, $B) = array($N, $V, $M);
				break;
			case 2:
				list($R, $G, $B) = array($M, $V, $K);
				break;
			case 3:
				list($R, $G, $B) = array($M, $N, $V);
				break;
			case 4:
				list($R, $G, $B) = array($K, $M, $V);
				break;
			case 5:
			case 6: //for when $H=1 is given
				list($R, $G, $B) = array($V, $M, $N);
				break;
		}

		$R *= 255;
		$G *= 255;
		$B *= 255;

		return new self($R, $G, $B);
	}

	//

	public static function Lerp(Color $a, Color $b, float $r) : Color
	{
		$lerp = function($x, $y, $r){
			$delta = $y - $x;
			return $x + $delta * $r;
		};

		$a = $a->ToHSV();
		$b = $b->ToHSV();

		$H = $lerp($a["H"], $b["H"], $r);
		$S = $lerp($a["S"], $b["S"], $r);
		$V = $lerp($a["V"], $b["V"], $r);

		return Color::FromHSV(["H" => $H, "S" => $S, "V" => $V]);
	}

	public static function LerpLinear(Color $a, Color $b, float $r) : Color
	{
		$lerp = function($x, $y, $r){
			$delta = $y - $x;
			return $x + $delta * $r;
		};

		$_r = $lerp($a->r, $b->r, $r);
		$_g = $lerp($a->g, $b->g, $r);
		$_b = $lerp($a->b, $b->b, $r);

		/*$max_a = max($a->r, $a->g, $a->b);
		$max_b = max($b->r, $b->g, $b->b);
		$max_c = max($_r, $_g, $_b);
		$max = $lerp($max_a, $max_b, $r);
		$max_ratio = $max_c != 0 ? $max / $max_c : 1;*/
		$max_ratio = 1;

		return new Color($_r * $max_ratio, $_g * $max_ratio, $_b * $max_ratio);
	}

	//
	public function ToCSS() : string
	{
		$r = intval($this->r);
		$g = intval($this->g);
		$b = intval($this->b);
		return "rgb(".$r.", ".$g.", ".$b.")";
	}

	public static function FromNorm($n) : Color
	{
		if($n < 0)$n = 0;
		if($n > 1)$n = 1;

		$charge = round($n * 0xFF);
		return new Color($charge, $charge, $charge);
	}
}