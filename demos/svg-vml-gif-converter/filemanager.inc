<?php
   /******************************************************************************

   ISAPRESS : Librerie di ISA (da XanaWord)

   Release  27/3/2003
   Copyright: Angelo Di Iorio - diiorio@cs.unibo.it

   Tutti i diritti riservati. Ogni uso deve essere autorizzato.

   ******************************************************************************/

if (version_compare(PHP_VERSION,'5','>=')&&extension_loaded('xsl'))
 require_once('xslt-php4-to-php5.php');



    /***    Gets file modification time ***/
    function last_date($f)
        {
        global $base;

        if (existing($f))
            return filemtime(map_path($f));
        else return $base;
        }

    /***    Translates an address relative to Apache document-root into a filesystem location   ***/
    function map_path($f)
        {

	global $base_shell_dir;

        $slash="/";

	if (($f[0]=="/"))
		{
		$f=substr($f,1);
		$slash="";
		}

	$f=preg_replace("/\//","\\",$f);
	$mp="$base_shell_dir$slash$f";
	return ($mp);
        }

    /***    Checks whether a file exists   ***/
    function existing($f)
           {
              return file_exists(map_path($f));
           }

    /*** Reads a file   ***/
    function read_file($f)
    {
    	if (!isset($_SERVER['argc']) || $_SERVER['argc'] == 0)
		$filename = map_path($f);
	else
		$filename = $f;

	if (!$fd=fopen($filename,"r"))
		echo "Cannot read $filename\n";

	$contents=fread($fd,filesize($filename));
        fclose ($fd);

        return $contents;
    }

    /*** Writes a file   ***/
    function save_file($d,$f,$x)
        {

        $a=preg_split("/\//i",$d.$f);
        $dir="";

        //ATTENZIONE:PARTO DA 1 PERCHE' $a[0]=""

        for ($i=1;$i<(sizeof($a)-1);$i++)
            {
            $dir=$dir."/".$a[$i];
            if (!(existing($dir))) mkdir(map_path($dir));
            }

        $filename=map_path($d.$f);
        $fd=fopen($filename,"w");
        fwrite($fd,$x);
        fclose($fd);

        }


?>
