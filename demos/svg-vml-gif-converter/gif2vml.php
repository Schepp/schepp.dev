<?php
set_time_limit (600);
include_once('filemanager.inc');
function gifToVml($file)
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

  $temp  = "<?xml version=\"1.0\"?>\n";
  $temp .= "<html xmlns:v = \"urn:schemas-microsoft-com:vml\"\n";
  $temp .= "\t xmlns=\"http://www.w3.org/1999/xhtml\">\n";  
  $temp .= "<head><title>GIF 2 VML</title>\n";
  $temp .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\" />\n";
  $temp .= "<object id=\"VMLRender\" classid=\"CLSID:10072CEC-8CC1-11D1-986E-00A0C955B42E\"></object>\n";
  $temp .= "<style>v\:* { BEHAVIOR: url(#VMLRender) }</style>\n";
  $temp .="</head><body>\n";    
  $temp .= "<v:group style=\"position: absolute; left: 0; top: 0; width: ".$dim[0]."; height: ".$dim[1]."; \"\n";
  $temp .="\t coordorigin=\"0 0\" coordsize=\"".$dim[0]." ".$dim[1]."\">\n";
  $temp .="<v:image style=\"position: absolute; left: 0; top: 0; width: ".$dim[0]."; height: ".$dim[1]."; \" \n";
  $temp .= "\t  title=\"".$title."\" src=\"".$title."\" />\n";
  $temp .="</v:group>\n";
  $temp .="</body></html>";
   
  if ($_SERVER['argc'] == 0)
  {
	echo $temp;	
  }
  else
  {
	return $temp;
  }
}

  if ($_SERVER['argc'] == 0)
  {
  	  $base_shell_dir = substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/")+1);
	  $gif_name = $_FILES["file_htm"]["name"];
	  gifToVml($gif_name);
  }
?>
