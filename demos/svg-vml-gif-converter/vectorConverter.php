<?php

/************************************************************************************************/
//												//
//	-- Filename: vectorConverter.php							//
//												//
//	-- Description: VectorConverter class definition  	 				//
//      											//
//	-- vectorConverter functions:								//
//												//
//		__construct: class constructor 							//
//												//
//		convert($inputExt, $outputExt, $fileInput, $fileOutput): calls the right	//
//				function to convert properly the input file 			//
//												//
//	-- Author: Giorgio Massaro - massaro@cs.unibo.it					//
//												//
//	-- Date: 10 July 2007									//
//												//
//												//
/************************************************************************************************/



include_once("vml2svg.php");
include_once("vml2gif.php");
include_once("svg2vml.php");
include_once("gif2vml.php");
include_once("gif2svg.php");
include_once("svg2gif.php");




class VectorConverter
{
	var $outExt;
	var $inExt;
	var $inFile;
	var $outFile;
	var $result;

	function __construct()
	{
		$this->outExt = NULL;
		$this->inExt  = NULL;
		$this->inFile = NULL;
		$this->result = NULL;
		$this->outFile= NULL;
	}

	function convert($inputExt, $outputExt, $fileInput, $fileOutput)
	{
		$this->outExt = $outputExt;
		$this->inExt  = $inputExt;
		$this->inFile = $fileInput;
		$this->outFile= $fileOutput;
		
		if (strcmp($this->inExt, "vml") == 0)
		{
			if (strcmp($this->outExt,"svg") == 0)
				$this->result = vmlToSvg($this->inFile);
			else
			{
				$tmpImg = vmlToGif($this->inFile);
				imagegif($tmpImg, $this->outFile);
				$this->result = NULL;
			}
		}
		elseif (strcmp($this->inExt, "svg") == 0)
		{
			if (strcmp($this->outExt, "vml") == 0)
				$this->result = svgToVml($this->inFile);
			else
			{
				$tmpImg = svgToGif($this->inFile);
				imagegif($tmpImg, $this->outFile);
				$this->result = NULL;
			}
		}
		/*  GIF -> (VML || SVG) */
		elseif (strcmp($this->inExt, "gif") == 0)
		{
			if (strcmp($this->outExt, "vml") == 0)
				$this->result = gifToVml($this->inFile);
			else
				$this->result = gifToSvg($this->inFile);
		}

		return $this->result;
	}
}
?>
