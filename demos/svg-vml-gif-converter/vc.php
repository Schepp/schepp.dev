<?php

/************************************************************************************************/
//												//
//	-- Filename: vc.php									//
//												//
//	-- Description: this script executes VectorConverter, that				//
//                      allows easy, automatic and reasonably good				//
//		        conversion between two vector graphic formats,				//
//		        SVG and VML, and GIF, from a command line. 				//
//      											//
//	-- Usage: PHP_INTERPRETER vc.php -(svg|vml|gif) fileIn fileOut				//
//		  										//
//		1) PHP_INTERPRETER: your php interpreter 					//
//												//
//		2) vc.php: name of this file							//
//												//
//		3) -svg, -vml, -gif : conversion respectively in SVG, VML and GIF 		//
//		 										//
//		4) fileIn: path of the file to convert (e.g. /home/user/image.svg)		//
//												//
//              5) fileOut:  path of the new file (or a writeable preexistent file) 		//
//				where conversion can be saved according to the  	        //
//				chosen option (e.g. php vc.php -svg image.vml image.svg)	//
//      -- Functions:										//
//												//
//		printArgError: prints if an argument is wrong					//
//												//
//		printUsageMessage: prints the usage of this script				//
//												//
//		extFileError: controls the argument file extension				//
//												//
//		main program: creates a VectorConverter object and calls			//
//				the VectorConverter class function "convert"			//
//				on this object							//
//												//
//	-- Author: Giorgio Massaro - massaro@cs.unibo.it					//
//												//
//	-- Date: 10 July 2007									//
//												//
//												//
/************************************************************************************************/

include_once("vectorConverter.php");

function printArgError($argument)
{
	print("VECTOR CONVERTER:\n");
	print("[ERROR]: $argument is wrong\n\n");
}

function printUsageMessage()
{
	print("VECTOR CONVERTER:\n");
	print("[USAGE]: PHP vc.php -(svg|vml|gif) fileInput fileOutput\n\n");	
}

/* Argument extension control */
function extFileError($type, $option, $file)
{
	$result = NULL;
	$subFile   =  substr($file, -3);
	$subOption =  substr($option, -3); 

	if ( strcmp($type,"in") == 0)
	{		
		if (strcmp($subFile, $subOption) == 0)
			$result = TRUE;
		else 
			$result = FALSE;
	}

	if ( strcmp($type,"out") == 0)
	{
		if (strcmp($subFile, $subOption) != 0)
			$result = TRUE;
		else
			$result = FALSE;
	}
	
	return $result;
}

/* Argument control */
function argControlError($argArray)
{
	$result = FALSE;	
	
	/* Option argument control */
	if( isset($argArray[1])and
		(strcmp($argArray[1],"-svg")!= 0) and 
		(strcmp($argArray[1],"-vml")!= 0) and 
		(strcmp($argArray[1],"-gif")!= 0))
	{
		printArgError($argArray[1]);
		$result = TRUE;	
	}

	/* Input file controls */

	if ( !(file_exists($argArray[2])) or (extFileError("in", $argArray[1], $argArray[2])) )
	{
		printArgError($argArray[2]);
		$result = TRUE;	
	}
	
	/* Output file controls */

	if (extFileError("out", $argArray[1], $argArray[3])) 
	{
		printArgError($argArray[3]);		
		$result = TRUE;	
	}
	
	return $result;
}


/******************/
/*      Main      */
/******************/

$inputExt   = NULL;
$outputExt  = NULL;
$fileInput  = NULL;
$fileOutput = NULL;


if ($argc != 4) 
{
	printUsageMessage();
	die();
}
else
{
	if (argControlError($argv))
	{
		die();
	}
	else
	{
		/* output file extension */
		$outputExt = substr($argv[1], -3);
		/* input file extension */
		$inputExt  = substr($argv[2], -3);
		$fileInput = $argv[2];
		$fileOutput = $argv[3];

		/* main call */
		$VC = new VectorConverter; 
		$result = $VC->convert($inputExt, $outputExt, $fileInput, $fileOutput);
		unset($VC);

		/* In ( * -> GIF ) conversion $result == NULL */
		if ($result != NULL)
		{	
			if (!file_exists($fileOutput) or is_writeable($fileOutput))
			{
				if (!$tmp = fopen($fileOutput, "w"))
				{
					echo "Cannot open file ($fileOutput)\n";
					exit;
				}
				
				if (fwrite($tmp, $result) === FALSE)
				{
					echo "Cannot write to file ($fileOutput)\n";
					exit;
				}
				echo "Success, conversion saved in ($fileOutput)\n";
				fclose($tmp);
			}
			else
			{
				echo "The file $fileOutput already exists or is not writable\n";
			}
		}
		else
				echo "Success, conversion saved in ($fileOutput) \n";
	
	}	
}

?>
