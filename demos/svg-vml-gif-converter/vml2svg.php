<?php

	include("filemanager.inc");
	


function vmlToSvg($nome)
{
	/* CLI request -> the argument number passed to php is greater than 0 */
	if ($_SERVER['argc'] == 0)
	{
		$cliReq = FALSE;
		$nome_upload = $_FILES["file_htm"]["name"];
	}
	else
		$cliReq = TRUE;

       $file   = @fopen($nome, "r");

       if (!$cliReq)
       {
		$output  = "<html><head><title>Conversione VML -> SVG: ".$nome_upload."</title>\n";
	        $output .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"vc.css\" />\n";
	        $output .= "<body><div id=\"logo\"><span class=titolo>University of Bologna <br/>Department of Computer Science</span></div>\n";
       }
       
       if($file)
       {
	       @fclose($file);
	       $xsl = "vml2svg.xsl";    
	       $fdo = fopen($nome,"r");
	       $xml_contents=fread($fdo,filesize($nome));
	       fclose ($fdo);
	       $from="/(<meta[^>]*[^\/]?)>/i";
	       $xml_contents=preg_replace($from,"$1/>",$xml_contents);
	       $from="/\/(\/>)/i";
	       $xml_contents=preg_replace($from,"$1",$xml_contents);
	       $xh = xslt_create();
	       $arguments=array('/_xml' =>$xml_contents,'/_xsl' => read_file($xsl));
	       $result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
	       xslt_free($xh);
	       
	       if (!$cliReq)
	       {
		       $ctime = time();
		       $svg_name = $ctime.".svg";
		       $vml_name = $ctime.".html";
	               save_file("",$vml_name,$xml_contents);
	               save_file("",$svg_name,$result);
		       
		       $output .= "<div id=\"main\">\n";
		       $output .= "<h2>Conversione <b>VML -&gt; SVG</b>: ".$nome_upload." </h2>\n";
		       $output .= "<table width=\"100%\">\n";
		       $output .= "<tr><td align=\"center\"><h3>VML</h3></td>";
		       $output .= "<td align=\"center\"><h3>SVG</h3></td></tr>\n";
		       $output .= "<tr><td align=\"center\" >\n";
		       $output .= "<object type=\"text/html\" data=\"".$vml_name."\" ";
		       $output .= " width=\"450\" height=\"400\" >";
		       $output .= "</object>\n";
		       $output .= "</td><td width=\"50%\" align=\"center\">\n";
		       $output .= "<object type=\"image/svg+xml\" data=\"".$svg_name."\" ";
		       $output .= " width=\"450\" height=\"400\" >";
		       $output .= "</object>\n";
		       $output .= "</td></tr>\n";	
		       $output .= "</table>\n";
		       $output .= "</div>\n";
		       $output .= "</body></html>";
		       
		       echo $output;
	       }
	       else
		       return $result;
       }
       else
       {
       		if (!$cliReq)
		{
			$output .= "Spiacenti, file ".$nome." non trovato</body></html>";
			echo $output;
		}
       }
}

/* this code is for a WEB request */
if ($_SERVER['argc'] == 0)
{
		$base_shell_dir = substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/")+1);
		$file_nome = $_FILES["file_htm"]["tmp_name"];
		vmlToSvg($file_nome);
}

?>
