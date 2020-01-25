<?php
set_time_limit (600);
include_once('filemanager.inc');


function gifToSvg($file)
{
	if ($_SERVER['argc'] == 0)
	{
		$nome = $_FILES["file_htm"]["tmp_name"];
		$fdo=fopen($nome,"r");
		$gif_contents=fread($fdo,filesize($nome));  
		fclose ($fdo);
		save_file("",$file,$gif_contents);  
		$dim = getimagesize($nome);
		$title = $file;  
	}
	else
	{
		$dim = getimagesize($file);
		$title = realpath($file);
	}

    $output ="<?xml version=\"1.0\" standalone=\"no\"?>\n";
    $output.="<svg width=\"100%\" height=\"100%\" xmlns=\"http://www.w3.org/2000/svg\"\n"; 
    $output.="\t xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\">\n";  
    $output.="<desc>Svg con immagine GIF</desc>\n";
    $output.="<image width=\"".$dim[0]."\" height=\"".$dim[1]."\" xlink:href=\"".$title."\" >\n";
    $output.="<title>".$title."</title>\n";
    $output.="</image>";
    $output.="</svg>\n";

    if ($_SERVER['argc'] == 0)
    {  
	    $svg_name = time().".svg";
	    save_file("",$svg_name,$output);
    	    @header("Location: $svg_name");
    }
    else
    {
	   @header("Location: $file");	
	   return $output;
    }
}

if ($_SERVER['argc'] == 0)
{
	$base_shell_dir = substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/")+1);
	$gif_name = $_FILES["file_htm"]["name"];
	gifToSvg($gif_name); 
}
?>
