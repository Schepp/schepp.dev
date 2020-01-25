<?php

// Per un corretto funzionamento:
// - nella cartella in cui e' riposto questo script PHP e' necessaria la presenza di una
//      directoty FONT, in cui sono inseriti i font da utilizzare (in formato ttf).
//      La versione attuale del convertitore supporta i seguenti font: arial, times, verdana.
//


/* NOTE:
 - spesso si incontreranno porzioni di codice commentate, che iniziano con DEBUG, sono state 
    usate e si possono usare in fase di controllo. 
 - sono presenti altre porzioni di codice commentato realite a caratteristiche non gestite o 
    getite solo in parte ma in un modo non del tutto corretto
 - variabili schermo_x e schermo_y = risoluzione dello schermo in pixel (impostate a 750 400)
*/

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// DICHIARAZIONE FUNZIONI ///////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // COLORI: 
    // carica tutti i colori che sono utilizzati nel documento (quelli nella forma rgb(x,x,x) e #c1c2c3,
    //      memorizzati nell'array valore_colori.
    //      Inoltre carica i principali colori espressi mediante il nome.
    //      Tutti i colori allocati sono memorizzati nell'array colori da usare per riferirsi ad un
    //          particolare colore.
    //
    function carica_colori(&$image, $n_colori, $valore_colori){
	    
	//	global $colori;

      // imposto i colori definiti tramite nome
      $colori["black"]  =  imagecolorallocate($image, 0, 0, 0);
      $colori["aqua"]   =  imagecolorallocate($image, 0, 255, 255);  
      $colori["blue"]   =  imagecolorallocate($image, 0,0,255);
      $colori["brown"]  =  imagecolorallocate($image, 165,42,42);
      $colori["gray"]   =  imagecolorallocate($image, 128,128,128);
      $colori["green"]  =  imagecolorallocate($image, 0,128,0);
      $colori["grey"]   =  imagecolorallocate($image, 128,128,128);
      $colori["lime"]   =  imagecolorallocate($image, 0,255,0);
      $colori["maroon"] =  imagecolorallocate($image, 128,0,0);
      $colori["navy"]   =  imagecolorallocate($image, 0,0,128);
      $colori["orange"] =  imagecolorallocate($image, 255,165,0);
      $colori["pink"]   =  imagecolorallocate($image, 255,192,203);
      $colori["purple"] =  imagecolorallocate($image, 128,0,128);
      $colori["red"]    =  imagecolorallocate($image, 255,0,0);
      $colori["silver"] =  imagecolorallocate($image, 192,192,192);
      $colori["yellow"] =  imagecolorallocate($image, 255,255,0);
      $colori["white"]  =  imagecolorallocate($image, 255, 255, 255);
      
      // imposto tutti i colori utilizzati nel documenti nella 
      //    forma rgb(x,x,x) e #c1c2c3
      $j = 0;
      while($j < $n_colori){
        $colore = $valore_colori[$j];
        if(preg_match("/(.*#.*)/",$colore)){
         $val1 = preg_replace("/(#)(..)(.*)/","\$2",$colore);
         $val2 = preg_replace("/(#..)(..)(.*)/","\$2",$colore);      
         $val3 = preg_replace("/(#....)(..)(.*)/","\$2",$colore);
   
         $val1 = hexdec($val1);
         $val2 = hexdec($val2);
         $val3 = hexdec($val3);
              
         $colori[$colore]  =  imagecolorallocate($image, $val1, $val2, $val3);
        
        }
        elseif(preg_match("/(.*rgb.*)/",$colore)){

         $val1 = preg_replace("/(rgb\()(.*?)(,.*)/","\$2",$colore);
         $val2 = preg_replace("/(rgb\(.*,)(.*)(,.*)/","\$2",$colore);        
         $val3 = preg_replace("/(rgb\(.*,.*,)(.*)(\))/","\$2",$colore);
     
             $colori[$colore]  =  imagecolorallocate($image, $val1, $val2, $val3);
        }
    
        $j += 1;    
      }
     return ($colori); 
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CONVERTI:
    //      converte il valore passato in input (dotato eventualmente di 
    //       unita' di misura) nel corrispondente valore assoluto, cioe'
    //       in pixel (o user unit). 
    //      A questa funzione vengono passati due ulteriori parametri per
    //       calcolare il valore espresso in percentuale o nei casi in cui si
    //       riferisca alla dimensione del font
    //
    function converti($valore,$font, $perc){    
       if(preg_match("/(.*cm.*)/",$valore)){
              $valore = preg_replace("/(.*)(cm.*)/","\$1",$valore);
          $valore = $valore * 35.43307;
           }
       elseif(preg_match("/(.*in.*)/",$valore)){
              $valore = preg_replace("/(.*)(in.*)/","\$1",$valore);
          $valore = $valore * 96;
           }
       elseif(preg_match("/(.*px.*)/",$valore)){
              $valore = preg_replace("/(.*)(px.*)/","\$1",$valore);
           }
       elseif(preg_match("/(.*pt.*)/",$valore)){
              $valore = preg_replace("/(.*)(pt.*)/","\$1",$valore);
          $valore = $valore * 1.25;
           }
       elseif(preg_match("/(.*mm.*)/",$valore)){
              $valore = preg_replace("/(.*)(mm.*)/","\$1",$valore);
          $valore = $valore * 3.543307;
           }
       elseif(preg_match("/(.*pc.*)/",$valore)){
              $valore = preg_replace("/(.*)(pc.*)/","\$1",$valore);
          $valore = $valore * 15;
           }
       elseif(preg_match("/(.*em.*)/",$valore)){
              $valore = preg_replace("/(.*)(em.*)/","\$1",$valore);
          $valore = $valore * $font;
           }
       elseif(preg_match("/(.*%.*)/",$valore)){
          $valore = preg_replace("/(.*)(%.*)/","\$1",$valore);
          $valore = ($valore * $perc) / 100;
           }
           
       /* 
          altri casi:
             * ex (non gestito)
       */   

     return ($valore);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CONVERSIONE:
    // imposta gli attributi di conversione (transalte x e y, scale x e y, viewbox x e y)
    // In base ai valori degli elementi precedenti, valori di dimensionamento (viewbox, width e 
    //      height) e di trasformazioni (translate e scale, le altre non sono supportate),
    //      calcola i dimensionamenti e gli spostamenti da applicare all'elemento corrente.
    // 
    function conversione(&$tr_x, &$tr_y, &$sc_x, &$sc_y, &$vb_x, &$vb_y,
                 $n_transform, $tipo_transform, $translate_x, $translate_y, $translate_livello, 
                 $scale_x, $scale_y, $n_viewbox, $viewbox_x, $viewbox_y,$viewbox_livello, 
                 $w, $h, $n_wh, $wh_livello, $w_val, $h_val){


      ////////////////////////
      // gestione transform //
      ////////////////////////
      
      $i = 0;
      $i_tr = 0;
      $i_sc = 0;
      $i_rt = 0;
  

      $tr_x = 0; $tr_y = 0; 
      $sc_x = 1; $sc_y = 1;
      $rt = 0;

      /* DEBUG
      echo $n_transform." ";
      $g = 0;
      echo "<br />";
      while($g < $n_transform){
        echo $translate_x[$g]." (".$translate_livello[$g].") ";
        $g += 1;
      }
      */
  
      while($i < $n_transform){
         if($tipo_transform[$i] == "t"){
            $tr_x_temp = $translate_x[$i_tr];
            $tr_y_temp = $translate_y[$i_tr];
            $j = 0;
            
            // DEBUG
            // echo "VB: ".$n_viewbox." ".$viewbox_x[0]." ".$viewbox_livello[0]."<br />";
            // echo "WH: ".$n_wh." ".$w_val[0]." ".$wh_livello[0]."<br />";

            while(($j < $n_viewbox) and ($viewbox_livello[$j] < $translate_livello[$i_tr])){

                $w_temp = $w; $h_temp = $h;

                $k = 0;

                while(($k < $n_wh) and ($wh_livello[$k] <= $viewbox_livello[$j])){
                   $w_temp = $w_val[$k];
                   $h_temp = $h_val[$k];
                   $k += 1; 
                }
                
                $tr_x_temp *= ($w_temp / $viewbox_x[$j]);
                $tr_y_temp *= ($h_temp / $viewbox_y[$j]);   
                $j += 1;

            // DEBUG
            // echo "t_temp: ".$tr_x_temp."  W: ".$w_temp." vb: ".$viewbox_x[$j - 1]."  ";

            }
            
            $tr_x = $tr_x + ($tr_x_temp * $sc_x);
                $tr_y = $tr_y + ($tr_y_temp * $sc_y);

                    $i_tr += 1; 

         }
         if($tipo_transform[$i] == "s"){
                  $sc_x = $sc_x * $scale_x[$i_sc];
                  $sc_y = $sc_y * $scale_y[$i_sc];

              $i_sc += 1;   
         }
         if($tipo_transform[$i] == "r"){
            // rotazioni non supportate
            $i_rt += 1; 
         }

         $i += 1;
      }

      // DEBUG
      //echo "<br />-------------------------<br />TR_X: ".$tr_x."<br />";
      //echo "-------------------------<br />";


       ////////////////////////
       // gestione viewbox ////
       ////////////////////////
       $vb = $n_viewbox;
       $vb_x = 1; 
       $vb_y = 1;    


     /* DEBUG
      $u = 0;
      while($u < $n_viewbox){
        echo "vb_x: ".$viewbox_x[$u]." (".$viewbox_livello[$u].")<br />";
        $u += 1;
      }
      $u = 0;
      echo "W: ".$w." ";
      while($u < $n_wh){
        echo "v_val: ".$w_val[$u]." (".$wh_livello[$u].")<br />";
        $u += 1;
      }
      echo "<br />";
      echo "---------------------<br/>";
      */
      
       while($vb > 0){      
        $w_temp = $w; $h_temp = $h;
        
        $k = 0;
        while(($k < $n_wh) and ($wh_livello[$k] <= $viewbox_livello[$vb - 1])){
                   $w_temp = $w_val[$k];
                   $h_temp = $h_val[$k];
                   $k += 1; 
        }
    
        $vb_x *= ($w_temp / $viewbox_x[$vb - 1]);
            $vb_y *= ($h_temp / $viewbox_y[$vb - 1]);
        
        // DEBUG
        //echo ($w_temp / $viewbox_x[$vb - 1])." -- ";

        $vb -= 1;
       }

       // DEBUG
       //echo "vb_x: ".$vb_x."<br />----------------------<br />";

    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////// GESTIONE ANGOLI //////////////////////////////////
    
    // GESTIONE ANGOLI:
    //    serve per calcolari i valori di seno e coseno da applicare quando si 
    //      imposta lo spessore dei bordi, in quanto i bordi obliqui, devono essere
    //      ridotti rispetto al valore originale, in base all'angolo presente tra i
    //      due lati.
    //
    function gestione_angoli($x1,$x2,$y1,$y2,&$seno,&$coseno){
              $mod = 0;
              $lato1 = ($x2 - $x1);
              $lato2 = ($y2 - $y1);
              
              if((($lato1 < 0) and ($lato2 < 0)) or 
             (($lato1 > 0) and ($lato2 > 0))){
               $mod = 1;    
              }
              
              $lato1 = abs($lato1);
              $lato2 = abs($lato2);         

              if($lato2 != 0){ $angolo1 = atan($lato1/$lato2); }
              else{ $angolo1 = deg2rad(90); }
              if($lato1 != 0){ $angolo2 = atan($lato2/$lato1); }
              else{ $angolo2 = deg2rad(90); }
              if($mod == 0){
               $seno = sin($angolo1);
                   $coseno = cos($angolo1);
              }
              else{
               $seno = sin(deg2rad(180) - $angolo1);
               $coseno = cos(deg2rad(180) - $angolo1);
              }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CALCOLA_INVERSO:
    //  usata nell'ambito della gestione degli angoli
    //
    function calcola_inversione($new_x1,$new_x2,$new_y1,$new_y2,
                                &$x1,&$x2,&$y1,&$y2,&$inv){ 
        $inv = 1;
        
        $x1 = $new_x1;
        $x2 = $new_x2;
        $y1 = $new_y1;
        $y2 = $new_y2;

        if(($x1 < $x2) and ($y1 < $y2)){
            $inv = -1;  
        }
        if(($x1 == $x2) and ($y1 > $y2)){
            $inv = -1;  
        }
        if(($x1 < $x2) and ($y1 > $y2)){
            $inv = -1;  
        }
        if(($x1 < $x2) and ($y1 == $y2)){
            $inv = -1;  
        }
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////// Curva con 1 control point //////////////////////////////////

    // BEZIER3:
    //  funzione che calcola i punti della curva di bezier, con un solo punto di controllo,
    //      gli vengono passati i tre punti della curva (inizio, punto di controllo, fine) e 
    //      un valore da 0 a 1 e restituisce un punto della curva.
    //  Passandogli via via valori crescenti dell'intervallo [0,1] si otterranno i punti che
    //      approssimano la curva.
    //
    function bezier3($p1,$p2,$p3,$mu){
      $mu2 = $mu * $mu;
      $mum1 = 1 - $mu;
      $mum12 = $mum1 * $mum1;

      $p["x"] = $p1["x"] * $mum12 + 2 * $p2["x"] * $mum1 * $mu + $p3["x"] * $mu2;
      $p["y"] = $p1["y"] * $mum12 + 2 * $p2["y"] * $mum1 * $mu + $p3["y"] * $mu2;

      return $p;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////// Curva con 2 control point //////////////////////////////////

    // CUBICBEZIER:
    //  calcola i punti della curva di bezier con 2 punti di controllo.
    //
    function cubicbezier($p0,$p1,$p2,$p3,$mu){

    
      $c["x"] = 3 * ($p1["x"] - $p0["x"]);
      $c["y"] = 3 * ($p1["y"] - $p0["y"]);
      
      $b["x"] = 3 * ($p2["x"] - $p1["x"]) - $c["x"];
      $b["y"] = 3 * ($p2["y"] - $p1["y"]) - $c["y"];

      $a["x"] = $p3["x"] - $p0["x"] - $c["x"] - $b["x"];
      $a["y"] = $p3["y"] - $p0["y"] - $c["y"] - $b["y"];
    

      $p["x"] = $a["x"] * $mu * $mu * $mu + $b["x"] * $mu * $mu + $c["x"] * $mu + $p0["x"];
      $p["y"] = $a["y"] * $mu * $mu * $mu + $b["y"] * $mu * $mu + $c["y"] * $mu + $p0["y"];

      return $p;
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // GESTIONE_ATTRIBUTI_TESTO:
    //  imposta gli attributi di testo(x, y, font, colore, ...), per ogni elemento
    //      di testo (text, tspan, tref, ...)
    //
    
    function gestione_attributi_testo(&$testo, $n_testi, $riga, $font_val, $n_viewbox, $viewbox_x, $viewbox_y, $n_wh,
                              $w_val, $h_val, $w, $h, $colori,$perc_x,$perc_y){


         // gestione dx, dy, font, colore, x, y
         if(preg_match("/(.* fill=.*)/",$riga)){
           $color_temp = preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$color_temp)){
             $color_temp = preg_replace("/(.*)(\".*)/","\$1",$color_temp);           
           }
           $color_temp = preg_replace("/\s/","",$color_temp);
           
               $testo[$n_testi]["colore"] = $colori[$color_temp];
              }
          else{
           $testo[$n_testi]["colore"] = "none";

          }


          if(preg_match("/(.* font-size=.*)/",$riga)){
           $font_temp = preg_replace("/(.* font-size=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$font_temp)){
             $font_temp = preg_replace("/(.*)(\".*)/","\$1",$font_temp);             
           }
           $font_temp = preg_replace("/\s/","",$font_temp);
           
               $testo[$n_testi]["font"] = $font_temp;
              }
          else{
           $testo[$n_testi]["font"] = "none";

          }

         if(preg_match("/(.* font-family=.*)/",$riga)){
           $font_f = preg_replace("/(.* font-family=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$font_f)){
             $font_f = preg_replace("/(.*)(\".*)/","\$1",$font_f);           
           }
           $font_f = preg_replace("/\s/","",$font_f);
           
               $testo[$n_testi]["font_f"] = $font_f;
              }
          else{
           $testo[$n_testi]["font_f"] = "none";

          }

          if(preg_match("/(.* dx=.*)/",$riga)){
           $dx_temp = preg_replace("/(.* dx=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$dx_temp)){
             $dx_temp = preg_replace("/(.*)(\".*)/","\$1",$dx_temp);             
           }
          
           // prendi solo il primo valore!!//////////////
           // NB: non viene gestito il caso di dx con valori multipli
           if(preg_match("/(.*,.*)/",$dx_temp)){
            $dx_temp = preg_replace("/(.*?)(,.*)/","\$1",$dx_temp);
           }
           elseif(preg_match("/(.* .*)/",$dx_temp)){
            $dx_temp = preg_replace("/(.*?)( .*)/","\$1",$dx_temp);
           }
           ///////////////////////////////////////////////
           $dx_temp = preg_replace("/\s/","",$dx_temp);

           
           $dx_temp = converti($dx_temp, $font_val,$perc_x);
        
               $testo[$n_testi]["inc-x"] = $dx_temp;
              }
          else{
           $testo[$n_testi]["inc-x"] = 0;

          }

          if(preg_match("/(.* dy=.*)/",$riga)){
           $dy_temp = preg_replace("/(.* dy=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$dy_temp)){
             $dy_temp = preg_replace("/(.*)(\".*)/","\$1",$dy_temp);             
           }
           
           // prendi solo il primo valore!!//////////////
           // NB: non viene gestito il caso di dy con valori multipli
           if(preg_match("/(.*,.*)/",$dy_temp)){
            $dy_temp = preg_replace("/(.*?)(,.*)/","\$1",$dy_temp);
           }
           elseif(preg_match("/(.* .*)/",$dy_temp)){
            $dy_temp = preg_replace("/(.*?)( .*)/","\$1",$dy_temp);
           }
           ///////////////////////////////////////////////
           $dy_temp = preg_replace("/\s/","",$dy_temp);

           $dy_temp = converti($dy_temp, $font_val, $perc_y);


               $testo[$n_testi]["inc-y"] = $dy_temp;
              }
          else{
           $testo[$n_testi]["inc-y"] = 0;

          }
          
          if(preg_match("/(.* x=.*)/",$riga)){
           $x_temp = preg_replace("/(.* x=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$x_temp)){
             $x_temp = preg_replace("/(.*)(\".*)/","\$1",$x_temp);           
           }
           
           // prendi solo il primo valore!!//////////////
           // NB: non viene gestito il caso di x con valori multipli
           if(preg_match("/(.*,.*)/",$x_temp)){
            $x_temp = preg_replace("/(.*?)(,.*)/","\$1",$x_temp);
           }
           elseif(preg_match("/(.* .*)/",$x_temp)){
            $x_temp = preg_replace("/(.*?)( .*)/","\$1",$x_temp);
           }
           ///////////////////////////////////////////////
           
           $x_temp = preg_replace("/\s/","",$x_temp);
                    
           $x_temp = converti($x_temp, $font_val, $perc_x);
           

               $testo[$n_testi]["x"] = $x_temp;
              }
          else{
           $testo[$n_testi]["x"] = "none";

          }
          
          if(preg_match("/(.* y=.*)/",$riga)){
           $y_temp = preg_replace("/(.* y=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$y_temp)){
             $y_temp = preg_replace("/(.*)(\".*)/","\$1",$y_temp);           
           }
                   
           // prendi solo il primo valore!!//////////////
           // NB: non viene gestito il caso di y con valori multipli
           if(preg_match("/(.*,.*)/",$y_temp)){
            $y_temp = preg_replace("/(.*?)(,.*)/","\$1",$y_temp);
           }
           elseif(preg_match("/(.* .*)/",$y_temp)){
            $y_temp = preg_replace("/(.*?)( .*)/","\$1",$y_temp);
           }
           ///////////////////////////////////////////////
           $y_temp = preg_replace("/\s/","",$y_temp);


           $y_temp = converti($y_temp, $font_val, $perc_y);

               $testo[$n_testi]["y"] = $y_temp;
              }
          else{
           $testo[$n_testi]["y"] = "none";

          }
    }



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // PREDEF:
    //  gestisce le figure predefinite (rect, ellipse, circle, line, polygon, polyline)
    //      imposta i valori di dimensionamento (andando a considerare anche i valori
    //      degli elementi ancestor) ed in base ai valori di fill e stroke, procede
    //      alla visualizzazione.
    //
    function predef($nome, &$image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
            &$n_translate, &$n_rotate, &$n_scale,
            $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
            $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val, 
            $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate,  
            $scale_livello, $viewbox_livello, $transform_livello, $colori, $valore_font,
            $perc_x, $perc_y){
    
       // variabile X
       if(preg_match("/(.* x=.*)/",$riga)){
           $x = preg_replace("/(.* x=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$x)){
             $x = preg_replace("/(.*)(\".*)/","\$1",$x);
           }
           $x = converti($x, $valore_font, $perc_x);

       }
       else{
        $x = 0;
       }
       
       // variabile Y
       if(preg_match("/(.* y=.*)/",$riga)){
           $y = preg_replace("/(.* y=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$y)){
             $y = preg_replace("/(.*)(\".*)/","\$1",$y);
           }
               $y = converti($y, $valore_font, $perc_y);
           }
       else{
        $y = 0;
       }
       
       // variabile WIDTH
       if(preg_match("/(.* width=.*)/",$riga)){
           $rw = preg_replace("/(.* width=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$rw)){
             $rw = preg_replace("/(.*)(\".*)/","\$1",$rw);
           }
               $rw = converti($rw, $valore_font, $perc_x);
       }
       else{
        $rw = 0;
       }
       
       // variabile HEIGHT
       if(preg_match("/(.* height=.*)/",$riga)){
           $rh = preg_replace("/(.* height=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$rh)){
             $rh = preg_replace("/(.*)(\".*)/","\$1",$rh);
           }
           $rh = converti($rh, $valore_font, $perc_y);
       }
       else{
        $rh = 0;
       }

       // variabile cX: centro dell'ellisse
       if(preg_match("/(.* cx=.*)/",$riga)){
           $cx = preg_replace("/(.* cx=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$cx)){
             $cx = preg_replace("/(.*)(\".*)/","\$1",$cx);
           }
           $cx = converti($cx, $valore_font, $perc_x);
       }
       else{
        // valore di defaultt
        $cx = 0;
       }
       
       // variabile cY: centro dell'ellisse
       if(preg_match("/(.* cy=.*)/",$riga)){
           $cy = preg_replace("/(.* cy=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$cy)){
             $cy = preg_replace("/(.*)(\".*)/","\$1",$cy);
           }
           $cy = converti($cy, $valore_font, $perc_y);
       }
       else{
        $cy = 0;
       }
       
       // variabile rx: raggio dell'ellisse (o roundrect)
       if(preg_match("/(.* rx=.*)/",$riga)){
           $rx = preg_replace("/(.* rx=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$rx)){
             $rx = preg_replace("/(.*)(\".*)/","\$1",$rx);
           }
           $rx = converti($rx, $valore_font, $perc_x);
       }
       else{
        $rx = 0;
        if($nome == "rect"){ $rx = "none"; }
       }
       
       // variabile ry: raggio dell'ellisse (o roundrect)
       if(preg_match("/(.* ry=.*)/",$riga)){
           $ry = preg_replace("/(.* ry=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$ry)){
             $ry = preg_replace("/(.*)(\".*)/","\$1",$ry);
           }
           $ry = converti($ry, $valore_font, $perc_y);
       }
       else{
        $ry = 0;
        if($nome == "rect"){ $ry = "none"; }
       }
       
       // variabile r: raggio del cerchio
       if(preg_match("/(.* r=.*)/",$riga)){
           $r = preg_replace("/(.* r=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$ry)){
             $r = preg_replace("/(.*)(\".*)/","\$1",$r);
           }
           $r_x = $r;
           $r_y = $r;
           $r_x = converti($r_x, $valore_font, $perc_x);
           $r_y = converti($r_y, $valore_font, $perc_y);
         
       }
       else{
        $r = 0;
        $r_x = 0;
        $r_y = 0;
       }

       // variabile X1
       if(preg_match("/(.* x1=.*)/",$riga)){
           $x1 = preg_replace("/(.* x1=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$x1)){
             $x1 = preg_replace("/(.*)(\".*)/","\$1",$x1);
           }
           $x1 = converti($x1, $valore_font, $perc_x);
       }
       else{
        $x1 = 0;
       }
       
       // variabile Y1
       if(preg_match("/(.* y1=.*)/",$riga)){
           $y1 = preg_replace("/(.* y1=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$y1)){
             $y1 = preg_replace("/(.*)(\".*)/","\$1",$y1);
           }
           $y1 = converti($y1, $valore_font, $perc_y);
       }
       else{
        $y1 = 0;
       }

       // variabile X2
       if(preg_match("/(.* x2=.*)/",$riga)){
           $x2 = preg_replace("/(.* x2=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$x2)){
             $x2 = preg_replace("/(.*)(\".*)/","\$1",$x2);
           }
           $x2 = converti($x2, $valore_font, $perc_x);
       }
       else{
        $x2 = 0;
       }
       // variabile Y2
       if(preg_match("/(.* y2=.*)/",$riga)){
           $y2 = preg_replace("/(.* y2=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$y2)){
             $y2 = preg_replace("/(.*)(\".*)/","\$1",$y2);
           }
           $y2 = converti($y2, $valore_font, $perc_y);
       }
       else{
        $y2 = 0;
       }

       // variabile points
       if(preg_match("/(.* points=.*)/",$riga)){
           $pp = preg_replace("/(.* points=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$pp)){
             $pp = preg_replace("/(.*)(\".*)/","\$1",$pp);
           }
           $pp = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$pp);
           $n_punti = 0;
           while(preg_match("/(.*\d.*)/",$pp)){
                     $punti[$n_punti] = preg_replace("/(-?\d+\.?\d*)(\D?)(.*)/","\$1",$pp);
             $pp = preg_replace("/(-?\d+\.?\d*)(\D?)(.*)/","\$3",$pp);
             $n_punti += 1;
               }
       }
       else{
         $n_punti = 0;
       }
     

       conversione(&$tr_x, &$tr_y, &$sc_x, &$sc_y, &$vb_x, &$vb_y,
                 $n_transform, $tipo_transform, $translate_x, $translate_y, $translate_livello, 
                 $scale_x, $scale_y,
                 $n_viewbox, $viewbox_x, $viewbox_y,$viewbox_livello, 
                 $w, $h, $n_wh, $wh_livello, $w_val, $h_val);

       // impostiamo i valori con le trasformazioni
           
       $x = ($x * $sc_x * $vb_x) + $tr_x;
       $y = ($y * $sc_y * $vb_y) + $tr_y;
        
       $rx = $rx * $sc_x * $vb_x;
       $ry = $ry * $sc_y * $vb_y;

       $r_x = $r_x * $sc_x * $vb_x;
       $r_y = $r_y * $sc_y * $vb_y;


       $rw = $rw * $sc_x * $vb_x;
       $rh = $rh * $sc_y * $vb_y;
    
       // DEBUG
       //echo "VB: ".$vb_x. "x: ".$x." w: ".$rw."<br />";

       $r = $r * $sc_x * $vb_x;

       $cx = ($cx * $sc_x * $vb_x) + $tr_x;
       $cy = ($cy * $sc_y * $vb_y) + $tr_y;
        
       $x1 = ($x1 * $sc_x * $vb_x) + $tr_x;
       $y1 = ($y1 * $sc_y * $vb_y) + $tr_y;
       $x2 = ($x2 * $sc_x * $vb_x) + $tr_x;
       $y2 = ($y2 * $sc_y * $vb_y) + $tr_y;

       // DEBUG
       //echo "x1: ".$x1." y1: ".$y1." x2: ".$x2." y2: ".$y2."<br />";

       $tr_punti = 0;
       $pari = 0;
       while($tr_punti < $n_punti){
        if ($pari == 0){
            $punti[$tr_punti] = ($punti[$tr_punti] * $vb_x * $sc_x) + $tr_x;
            $pari = 1;
        }
        else{
            $punti[$tr_punti] = ($punti[$tr_punti] * $vb_y * $sc_y) + $tr_y;
            $pari = 0;
        }
        $tr_punti += 1;
       }

       // imposto le variabli per roundrect
       if(($nome == "rect") and  (($rx != "none") or  ($ry != "none"))){    
           if(($rx == "none") and ($ry == "none")){
            $rx = 0; $ry = 0;
           }
           elseif($rx == "none"){ $rx = $ry; }
           elseif($ry == "none"){ $ry = $rx; }

           if($rx > ($rw / 2)){ $rx = $rw / 2; }
           if($ry > ($rh / 2)){ $ry = $rh / 2; }
       
           $misure["x"] = $x; $misure["y"] = $y; $misure["w"] = $rw; $misure["h"] = $rh;
           $misure["rx"] = $rx; $misure["ry"] = $ry;
       }

  
       // FILL //////////
       if(preg_match("/(.* fill=\"none\".*)/",$riga)){ }
       else{
         if(preg_match("/(.* fill=.*)/",$riga)){
           $color_fill = preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$color_fill)){
             $color_fill = preg_replace("/(.*)(\".*)/","\$1",$color_fill);           
           } 
           $color_fill = preg_replace("/\s/","",$color_fill);
           // NB: i fill che si riferiscono a gradienti o altro vengono rappresentati con
           //   il colore grigio
           if(preg_match("/(url)/",$color_fill)){ $color_fill = "gray"; }
         }
         else{
           // da cercare fill negli elementi precedenti
           $color_fill = $fill[$n_fill - 1];  
         }

        // in base al tipo di elemento procedo alla visualizzazione della figura
        
           if($nome == "rect"){
            if(($rx == 0) and ($ry == 0)){
                    imagefilledrectangle($image,$x,$y,($x + $rw),($y + $rh),$colori[$color_fill]);
            }
            else{ // roundrect
                  gestione_roundrect($image,$misure,$color_fill,"none",0,$colori);
            }
           }
           elseif($nome == "ellipse"){
              imagefilledellipse($image,$cx,$cy,($rx * 2),($ry * 2),$colori[$color_fill]);
           }
           elseif($nome == "circle"){
              imagefilledellipse($image,$cx,$cy,($r_x * 2),($r_y * 2),$colori[$color_fill]);
           }
           elseif($nome == "polygon"){
              
              imagefilledpolygon($image,$punti,$n_punti / 2,$colori[$color_fill]);
           }
           elseif($nome == "polyline"){
              imagefilledpolygon($image,$punti,$n_punti / 2,$colori[$color_fill]);
           } 

       }
       
       // STROKE //////////////////////////
       if(preg_match("/(.* stroke=\"none\".*)/",$riga)){
       }

       else{
       
          if(preg_match("/(.* stroke=.*)/",$riga)){
           $color_stroke = preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$color_stroke)){
             $color_stroke = preg_replace("/(.*)(\".*)/","\$1",$color_stroke);           
           }
           $color_stroke = preg_replace("/\s/","",$color_stroke);
           // NB: gli stroke che si riferiscono a gradienti o altro vengono rappresentati con
           //   il colore grigio
           if(preg_match("/(url)/",$color_stroke)){ $color_stroke = "gray"; }

          }
          else{
             // da cercare stroke negli elementi precedenti
            $color_stroke = $stroke[$n_stroke - 1];
          }

          if(preg_match("/(.* stroke-width=.*)/",$riga)){
           $stroke_w = preg_replace("/(.* stroke-width=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$stroke_w)){
             $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);           
           }
           $stroke_w = preg_replace("/\s/","",$stroke_w);
           $stroke_w = converti($stroke_w, $valore_font, $perc_x);
          }
          else{
             // da cercare stroke negli elementi precedenti
            $stroke_w = $stroke_width["valore"][$stroke_width["n"] - 1];
          }        
         
          $stroke_w *= $vb_x * $sc_x; 
        
          if($color_stroke != "none"){
          
           // in base al tipo di elemento procedo alla visualizzazione del bordo 
           //    della figura. Visto che nelle funzioni di PHP non c'e' modo di
           //    specificare la dimensione dei bordi, si utilizza un trucchetto:
           //      vengono disegnati tanti bordi via via crescenti in modo da dare 
           //      l'illusione di un unico bordo della dimensione specificata.
           if($nome == "rect"){
              if(($rx == 0) and ($ry == 0)){
                  $j = - ($stroke_w / 2);
                  while($j < ($stroke_w / 2)){        
                     imagerectangle($image,$x + $j,$y + $j ,($x + $rw) - $j,
                            ($y + $rh) - $j, $colori[$color_stroke]);
                     $j += 0.01;
                  }
              }
              else{ // roundrect
                  gestione_roundrect($image,$misure,"none",$color_stroke,$stroke_w,$colori);
              }
           }
           elseif($nome == "ellipse"){
              $j = - ($stroke_w / 2);
              while($j < ($stroke_w / 2)){         
                 imageellipse($image,$cx, $cy,($rx * 2) - ($j * 2) ,
                      ($ry * 2) - ($j * 2),$colori[$color_stroke]);
                 $j += 0.01;
             
              }
           }
           elseif($nome == "circle"){
              $j = - ($stroke_w / 2);
              while($j < ($stroke_w / 2)){         
                imageellipse($image,$cx, $cy, ($r_x * 2) - ($j * 2),
                    ($r_y * 2) - ($j * 2) ,$colori[$color_stroke]);
                $j += 0.01;
              }

           }
           elseif($nome == "line"){
              gestione_angoli($x1,$x2,$y1,$y2,$seno,$coseno);
             
              $j = - ($stroke_w / 2);
              
              while($j <= ($stroke_w / 2)){       

                calcola_inversione($x1,$x2,$y1,$y2,
                           $x1_new,$x2_new,$y1_new,$y2_new,$inv);
                                        
                $y_val = $j * $inv * $seno ;
                $x_val = $j * $inv * $coseno;
                        
                    imageline($image,$x1_new + $x_val, $y1_new + $y_val,
                                 $x2_new + $x_val, $y2_new + $y_val,$colori[$color_stroke]);

                    $j += 0.01;
              }

           }   
           elseif($nome == "polygon"){            
                                                                       
              $k = - ($stroke_w / 2);          
              while($k < ($stroke_w / 2)){

                $j = 0;
                while ($j < $n_punti - 2){
            
                    // GESTIONE ANGOLI //
                    gestione_angoli($punti[$j],$punti[$j + 2],
                         $punti[$j + 1],$punti[$j + 3],$seno,$coseno);

                    calcola_inversione($punti[$j],$punti[$j + 2],$punti[$j + 1],$punti[$j + 3],
                                       $x1,$x2,$y1,$y2,$inv);
                           
                    $y_val = $k * $inv * $seno ;
                    $x_val = $k * $inv * $coseno;

                    // devo aver gia' disegnato una linea.
                
                    if($j > 0){
                    
                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori[$color_stroke]);
                    }
                    else{
                        $punto_iniziale_x = $x1 + $x_val;
                        $punto_iniziale_y = $y1 + $y_val;
                    }
                
                    imageline($image,$x1 + $x_val, $y1 + $y_val,
                              $x2 + $x_val, $y2 + $y_val,
                              $colori[$color_stroke]);

                    $j += 2;

                    $last_point_x = $x2 + $x_val;
                    $last_point_y = $y2 + $y_val;

                    if($j >= $n_punti - 2 ){
                                    
                        gestione_angoli($punti[$j],$punti[0], $punti[$j + 1],
                                        $punti[1],$seno,$coseno);
                                        
                        calcola_inversione($punti[$j],$punti[0],$punti[$j + 1],
                                           $punti[1],$x1,$x2,$y1,$y2,$inv);
                                                                              
                        $x_val = $k * $inv * $coseno;
                        $y_val = $k * $inv * $seno;

                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori[$color_stroke]);
                                  
                        imageline($image,$x1 + $x_val ,$y1 + $y_val,
                                  $x2 + $x_val,$y2 + $y_val,$colori[$color_stroke]);

                        imageline($image,$x2 + $x_val, $y2 + $y_val,
                                  $punto_iniziale_x, $punto_iniziale_y,
                                  $colori[$color_stroke]);
                    }
                }
             
                $k += 0.01;
              }                
           }   
           elseif($nome == "polyline"){

              $k = - ($stroke_w / 2);          
              while($k < ($stroke_w / 2)){

                $j = 0;
                while ($j < $n_punti - 2){
            
                    // GESTIONE ANGOLI //
                    gestione_angoli($punti[$j],$punti[$j + 2],
                                    $punti[$j + 1],$punti[$j + 3],$seno,$coseno);

                    calcola_inversione($punti[$j],$punti[$j + 2],$punti[$j + 1],$punti[$j + 3],
                                       $x1,$x2,$y1,$y2,$inv);
                           
                    $y_val = $k * $inv * $seno ;
                    $x_val = $k * $inv * $coseno;

                    // devo aver gia' disegnato una linea.                
                    if($j > 0){
                    
                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori[$color_stroke]);
                    }
                    else{
                        $punto_iniziale_x = $x1 + $x_val;
                        $punto_iniziale_y = $y1 + $y_val;
                    }

                    imageline($image,$x1 + $x_val, $y1 + $y_val,
                              $x2 + $x_val, $y2 + $y_val,
                              $colori[$color_stroke]);
    
                    $j += 2;

                    $last_point_x = $x2 + $x_val;
                    $last_point_y = $y2 + $y_val;

                    if($j >= $n_punti - 2 ){
                        if(($punti[0] == $punti[$j]) and ($punti[1] == $punti[$j + 1])){    
                    
                             gestione_angoli($punti[$j - 2],$punti[0], $punti[$j - 1],
                                             $punti[1],$seno,$coseno);
                                             
                             calcola_inversione($punti[$j - 2],$punti[0],$punti[$j - 1],
                                                $punti[1],$x1,$x2,$y1,$y2,$inv);
                                                                              
                             $x_val = $k * $inv * $coseno;
                             $y_val = $k * $inv * $seno;

                             imageline($image,$x2 + $x_val, $y2 + $y_val,
                                       $punto_iniziale_x, $punto_iniziale_y,
                                       $colori[$color_stroke]);
                        }
                    }
                }
                $k += 0.01;
              }           
           }   
           
          } // fine if stroke_color != none
       }
  
      }// fine funzione predef

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
      // GESTIONE_ROUNDERCT:
      //   serve per rappresentare un rettangolo con gli angoli arrotondati, viene gestito come un path.
      //   sono presenti 3 approssimazioni: utilizzando Achi (inserita come commento) e con curve (quella
      //            utilizzata) e mediante approssimazione con linee rette (commento)      
      // 
      function gestione_roundrect(&$image,$misure, $fill,$stroke,$stroke_w,$colori){
                 $x = $misure["x"]; $y = $misure["y"]; $w = $misure["w"]; $h = $misure["h"];
         $rx = $misure["rx"]; $ry = $misure["ry"];

        
         // impostiamo il path:
         $elemento_path["fill"]       = $fill;
         $elemento_path["stroke"]     = $stroke;
         $elemento_path["stroke_w"]   = $stroke_w;
        
         $elemento_path["n_comandi"]   = 9;
        
         // 1. MOVE TO (in altro a sx)  
         $elemento_path["comando"][0]   = "M";
         $elemento_path["n_valori"][0]  = 2;
         $elemento_path["valori"][0][0] = $x + $rx;
         $elemento_path["valori"][0][1] = $y;

         // 2. PRIMA LINEA (orizzontale alta) 
         $elemento_path["comando"][1]   = "H";
         $elemento_path["n_valori"][1]  = 1;
         $elemento_path["valori"][1][0] = $x + $w - $rx;

         // 3. PRIMO ARC (angolo alto dx)
         /*
         $elemento_path["comando"][2]   = "A";
         $elemento_path["n_valori"][2]  = 7;
         $elemento_path["valori"][2][0] = $rx;
         $elemento_path["valori"][2][1] = $ry;
         $elemento_path["valori"][2][2] = 0;
         $elemento_path["valori"][2][3] = 0;
         $elemento_path["valori"][2][4] = 1;
         $elemento_path["valori"][2][5] = $x + $w;
         $elemento_path["valori"][2][6] = $y + $ry;
         */
         // 3. PRIMO ARC (angolo alto dx): approssimato
         $elemento_path["comando"][2]   = "Q";
         $elemento_path["n_valori"][2]  = 4;
         $elemento_path["valori"][2][0] = $x + $w;
         $elemento_path["valori"][2][1] = $y ;

         $elemento_path["valori"][2][2] = $x + $w;
         $elemento_path["valori"][2][3] = $y + $ry;
         /*
         $elemento_path["comando"][2]   = "L";
         $elemento_path["n_valori"][2]  = 2;
         $elemento_path["valori"][2][0] = $x + $w;
         $elemento_path["valori"][2][1] = $y + $ry;
         */

         // 4. SECONDA LINEA (verticale dx) 
         $elemento_path["comando"][3]   = "V";
         $elemento_path["n_valori"][3]  = 1;
         $elemento_path["valori"][3][0] = $y + $h - $ry;

         // 5. SECONDO ARC (angolo basso dx)
         /*
         $elemento_path["comando"][4]   = "A";
         $elemento_path["n_valori"][4]  = 7;
         $elemento_path["valori"][4][0] = $rx;
         $elemento_path["valori"][4][1] = $ry;
         $elemento_path["valori"][4][2] = 0;
         $elemento_path["valori"][4][3] = 0;
         $elemento_path["valori"][4][4] = 1;
         $elemento_path["valori"][4][5] = $x + $w - $rx;
         $elemento_path["valori"][4][6] = $y + $h;
         */
         // 5. SECONDO ARC (angolo basso dx): approssimato
         $elemento_path["comando"][4]   = "Q";
         $elemento_path["n_valori"][4]  = 4;
         $elemento_path["valori"][4][0] = $x + $w;
         $elemento_path["valori"][4][1] = $y + $h;
         $elemento_path["valori"][4][2] = $x + $w - $rx;
         $elemento_path["valori"][4][3] = $y + $h;
         /*
         $elemento_path["comando"][4]   = "L";
         $elemento_path["n_valori"][4]  = 2;
         $elemento_path["valori"][4][0] = $x + $w - $rx;
         $elemento_path["valori"][4][1] = $y + $h;
         */
     
         // 6. TERZA LINEA (orizzontale bassa) 
         $elemento_path["comando"][5]   = "H";
         $elemento_path["n_valori"][5]  = 1;
         $elemento_path["valori"][5][0] = $x + $rx;

         // 7. TERZO ARC (angolo basso sx)
         /*
         $elemento_path["comando"][6]   = "A";
         $elemento_path["n_valori"][6]  = 7;
         $elemento_path["valori"][6][0] = $rx;
         $elemento_path["valori"][6][1] = $ry;
         $elemento_path["valori"][6][2] = 0;
         $elemento_path["valori"][6][3] = 0;
         $elemento_path["valori"][6][4] = 1;
         $elemento_path["valori"][6][5] = $x;
         $elemento_path["valori"][6][6] = $y + $h - $ry;
         */
         // 7. TERZO ARC (angolo basso sx): approssimato
         $elemento_path["comando"][6]   = "Q";
         $elemento_path["n_valori"][6]  = 4;
         $elemento_path["valori"][6][0] = $x;
         $elemento_path["valori"][6][1] = $y + $h;
         $elemento_path["valori"][6][2] = $x;
         $elemento_path["valori"][6][3] = $y + $h - $ry;

         /*
         $elemento_path["comando"][6]   = "L";
         $elemento_path["n_valori"][6]  = 2;
         $elemento_path["valori"][6][0] = $x;
         $elemento_path["valori"][6][1] = $y + $h - $ry;
         */

         // 8. QUARTA LINEA (verticale sx) 
         $elemento_path["comando"][7]   = "V";
         $elemento_path["n_valori"][7]  = 1;
         $elemento_path["valori"][7][0] = $y + $ry;

         // 9. QUARTO ARC (angolo alto sx)
         /*
         $elemento_path["comando"][8]   = "A";
         $elemento_path["n_valori"][8]  = 7;
         $elemento_path["valori"][8][0] = $rx;
         $elemento_path["valori"][8][1] = $ry;
         $elemento_path["valori"][8][2] = 0;
         $elemento_path["valori"][8][3] = 0;
         $elemento_path["valori"][8][4] = 1;
         $elemento_path["valori"][8][5] = $x + $rx;
         $elemento_path["valori"][8][6] = $y;
         */
         // 9. QUARTO ARC (angolo alto sx): approssimato

         $elemento_path["comando"][8]   = "Q";
         $elemento_path["n_valori"][8]  = 4;
         $elemento_path["valori"][8][0] = $x;
         $elemento_path["valori"][8][1] = $y;
         $elemento_path["valori"][8][2] = $x + $rx;
         $elemento_path["valori"][8][3] = $y;

         /*
         $elemento_path["comando"][8]   = "L";
         $elemento_path["n_valori"][8]  = 2;
         $elemento_path["valori"][8][0] = $x + $rx;
         $elemento_path["valori"][8][1] = $y;
         */


        visualizza_path(&$image,$elemento_path, 0, 0, 1, 1, 1 ,1 ,$colori);
         
      }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      // VISUALIZZA_PATH:
      //  funzione per la gestione dei path: gli viene passata una struttura (elemento_path) che contiene tutti i
      //      comandi e i rispettivi valori del path, questi vengono convertiti e tradotti in un insieme
      //      di punti che saranno rappresentati mediante la funzione per la visualizzazione di un poligono-
      //
      function visualizza_path(&$image,$elemento_path, $tr_x, $tr_y, $sc_x, $sc_y, $vb_x, $vb_y, $colori){

        $color_stroke = $elemento_path["stroke"];
        $color_fill   = $elemento_path["fill"];
        $stroke_w     = $elemento_path["stroke_w"];
        
        $j = 0;
        $last_point_x = $tr_x * $sc_x; // * $vb_x;
        $last_point_y = $tr_y * $sc_y; // * $vb_y;
        $first_point_x = $last_point_x;
        $first_point_y = $last_point_y;

        $start_x = 0;
        $start_y = 0;

        $path["n_punti"] = 2;
        $path["punti"][0] = $last_point_x;
        $path["punti"][1] = $last_point_y;
    
        while($j < $elemento_path["n_comandi"]){
          // andiamo per casi
          
          /////////////////// COMANDO M ///////////////////////
          if (($elemento_path["comando"][$j] == "m") or ($elemento_path["comando"][$j] == "M")){
            // da gestire caso m      
            if($path["n_punti"] > 2){
                visualizza_segmento_path($image,$path,$colori,$color_stroke,$stroke_w,$color_fill);
                $path["n_punti"] = 2;
                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;
            }
            
            if($elemento_path["n_valori"][$j] >= 2){
                $start_x = $elemento_path["valori"][$j][0];
                $start_y = $elemento_path["valori"][$j][1];
                
                if($elemento_path["comando"][$j] == "m"){
                    $first_point_x = ($elemento_path["valori"][$j][0] * $vb_x * $sc_x) 
                                   + $last_point_x;
                    $first_point_y = ($elemento_path["valori"][$j][1] * $vb_y * $sc_y) 
                                   + $last_point_y;
                }
                else{   
                    $first_point_x = ($elemento_path["valori"][$j][0] * $vb_x * $sc_x) + $tr_x;
                    $first_point_y = ($elemento_path["valori"][$j][1] * $vb_y * $sc_y) + $tr_y;
                }
                
                $last_point_x = $first_point_x;
                $last_point_y = $first_point_y;

                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;

                // DEBUG
                // echo "X: ".$path["punti"][0]." Y: ".$path["punti"][1]."<br />";

                $k = 2;
                while($k < $elemento_path["n_valori"][$j]){
                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "m"){
                         $x_agg = $start_x;
                         $y_agg = $start_y;
                   }
            
                   $x_val = (($elemento_path["valori"][$j][$k]     + $x_agg)  * $vb_x * $sc_x) + $tr_x;
                   $y_val = (($elemento_path["valori"][$j][$k + 1] + $y_agg)  * $vb_y * $sc_y) + $tr_y;
               
                   $np = $path["n_punti"];
                   $path["punti"][$np    ] = $x_val;
                   $path["punti"][$np + 1] = $y_val;
                   $path["n_punti"] += 2;
                           
                   $last_point_x = $x_val;
                   $last_point_y = $y_val;

                   $k += 2; 
                }
            }
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
          }
          
          /////////////////// COMANDO L ///////////////////////
          if (($elemento_path["comando"][$j] == "l") or ($elemento_path["comando"][$j] == "L")){
            $k = 0;
            while($k < $elemento_path["n_valori"][$j]){

               $x_agg = 0; $y_agg = 0;
               if($elemento_path["comando"][$j] == "l"){
                  $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
                  $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y);
               }

               $x_val = (($elemento_path["valori"][$j][$k]     + $x_agg) * $vb_x * $sc_x) + $tr_x;
               $y_val = (($elemento_path["valori"][$j][$k + 1] + $y_agg) * $vb_y * $sc_y) + $tr_y;
                      
               $np = $path["n_punti"];
               $path["punti"][$np    ] = $x_val;
               $path["punti"][$np + 1] = $y_val;
               $path["n_punti"] += 2;
               
               $last_point_x = $x_val;
               $last_point_y = $y_val;
           
               $k += 2; 
            }
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
          }
          
          /////////////////// COMANDO H ///////////////////////
          if (($elemento_path["comando"][$j] == "h") or ($elemento_path["comando"][$j] == "H")){
            $k = 0;
            while($k < $elemento_path["n_valori"][$j]){
               $x_agg = 0;
               if($elemento_path["comando"][$j] == "h"){
                  $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
               }
            
               $x_val = (($elemento_path["valori"][$j][$k] + $x_agg) * $vb_x * $sc_x) + $tr_x;
               $y_val = $last_point_y;

               $np = $path["n_punti"];
               $path["punti"][$np] = $x_val;
               $path["punti"][$np + 1] = $y_val;
               $path["n_punti"] += 2;
               
               $last_point_x = $x_val;
               $last_point_y = $y_val;
           
               $k += 1; 
            }
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
          }
          
          /////////////////// COMANDO V ///////////////////////
          if (($elemento_path["comando"][$j] == "v") or ($elemento_path["comando"][$j] == "V")){
            $k = 0;
            while($k < $elemento_path["n_valori"][$j]){
               $y_agg = 0;
               if($elemento_path["comando"][$j] == "v"){
                  $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y); 
               }
            
               $x_val = $last_point_x;
               $y_val = (($elemento_path["valori"][$j][$k] + $y_agg) * $vb_y * $sc_y) + $tr_y;
                
               $np = $path["n_punti"];
               $path["punti"][$np] = $x_val;
               $path["punti"][$np + 1] = $y_val;
               $path["n_punti"] += 2;
                   
               $last_point_x = $x_val;
               $last_point_y = $y_val;
           
               $k += 1; 
            }
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
          }
          
          /////////////////// COMANDO C ///////////////////////
          if (($elemento_path["comando"][$j] == "c") or ($elemento_path["comando"][$j] == "C")){
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            if($elemento_path["n_valori"][$j] >= 6){
                $k = 0;
                while($k < $elemento_path["n_valori"][$j]){
                   $old_last_x = $last_point_x;
                   $old_last_y = $last_point_y;
        
                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "c"){
                      $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
                      $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y);
                   }                   

                   $x_control_1 = (($elemento_path["valori"][$j][$k    ] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $y_control_1 = (($elemento_path["valori"][$j][$k + 1] + $y_agg) * $vb_y * $sc_y) + $tr_y;
                   $x_control_2 = (($elemento_path["valori"][$j][$k + 2] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $y_control_2 = (($elemento_path["valori"][$j][$k + 3] + $y_agg) * $vb_y * $sc_y) + $tr_y;

                   $last_point_x = (($elemento_path["valori"][$j][$k + 4] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $last_point_y = (($elemento_path["valori"][$j][$k + 5] + $y_agg) * $vb_y * $sc_y) + $tr_y;
    
                   $p1["x"] = $old_last_x;
                   $p1["y"] = $old_last_y;
                    
                   $p2["x"] = $x_control_1;
                   $p2["y"] = $y_control_1;
                
                   $p3["x"] = $x_control_2;
                   $p3["y"] = $y_control_2;
                   
                   $p4["x"] = $last_point_x;
                   $p4["y"] = $last_point_y;
                 
                   $cb = 0;
                   while($cb < 1){
                    
                     $punto = cubicbezier($p1, $p2, $p3, $p4,$cb);
                    
                     $np = $path["n_punti"];
                     $path["punti"][$np]     = $punto["x"];
                     $path["punti"][$np + 1] = $punto["y"];
                     $path["n_punti"] += 2;
 
                    $cb += 0.1;
                   }
                           
                   $k += 6;
                   
                }
            }
            // < 6 :  mancano dei punti !!
          }

          /////////////////// COMANDO S ///////////////////////
          if (($elemento_path["comando"][$j] == "s") or ($elemento_path["comando"][$j] == "S")){
            if($elemento_path["n_valori"][$j] >= 4){
                $k = 0;
                while($k < $elemento_path["n_valori"][$j]){

                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "s"){
                      $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
                      $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y);
                   }                   

                   $old_last_x = $last_point_x;
                   $old_last_y = $last_point_y;
                   
                   if(($elemento_path["comando"][$j -1] == "C") or ($elemento_path["comando"][$j -1]== "c")){
                     if(($x_control_2 != "") and ($y_control_2 != "")){
                       // reflection
                       $x_control_1 =  $old_last_x + $old_last_x - $x_control_2;
                       $y_control_1 =  $old_last_y + $old_last_y - $y_control_2;
                     }
                     else{
                        $x_control_1 = $old_last_x;
                        $y_control_1 = $old_last_y;
                     }
                   }
                   else{
                      $x_control_1 = $old_last_x;
                      $y_control_2 = $old_last_y;
                   }
                   
                   $x_control_2 = (($elemento_path["valori"][$j][$k    ] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $y_control_2 = (($elemento_path["valori"][$j][$k + 1] + $y_agg) * $vb_y  * $sc_y) + $tr_y;
               
                   $last_point_x = (($elemento_path["valori"][$j][$k + 2] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $last_point_y = (($elemento_path["valori"][$j][$k + 3] + $y_agg) * $vb_y * $sc_y) + $tr_y;

                   $p1["x"] = $old_last_x;
                   $p1["y"] = $old_last_y;
                    
                   $p2["x"] = $x_control_1;
                   $p2["y"] = $y_control_1;
                
                   $p3["x"] = $x_control_2;
                   $p3["y"] = $y_control_2;
                   
                   $p4["x"] = $last_point_x;
                   $p4["y"] = $last_point_y;
                 
                   $cb = 0;
                   while($cb < 1){
                    
                     $punto = cubicbezier($p1, $p2, $p3, $p4,$cb);
                    
                     $np = $path["n_punti"];
                     $path["punti"][$np]     = $punto["x"];
                     $path["punti"][$np + 1] = $punto["y"];
                     $path["n_punti"] += 2;
 
                     $cb += 0.1;
                   }
                   
                   $k += 4; 
                }
            }
            // < 4 :  mancano dei punti !!
          }

          /////////////////// COMANDO Q ///////////////////////
          if (($elemento_path["comando"][$j] == "q") or ($elemento_path["comando"][$j] == "Q")){
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            if($elemento_path["n_valori"][$j] >= 4){
                $k = 0;
                while($k < $elemento_path["n_valori"][$j]){

                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "q"){
                      $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
                      $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y);
                   }                   

                   $old_last_x = $last_point_x;
                   $old_last_y = $last_point_y;
                                   
                   $x_control_1 = (($elemento_path["valori"][$j][$k    ] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $y_control_1 = (($elemento_path["valori"][$j][$k + 1] + $y_agg) * $vb_y * $sc_y) + $tr_y;
               
                   $last_point_x = (($elemento_path["valori"][$j][$k + 2] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $last_point_y = (($elemento_path["valori"][$j][$k + 3] + $y_agg) * $vb_y * $sc_y) + $tr_y;
            
                   $p1["x"] = $old_last_x;
                   $p1["y"] = $old_last_y;
                    
                   $p2["x"] = $x_control_1;
                   $p2["y"] = $y_control_1;
                    
                   $p3["x"] = $last_point_x;
                   $p3["y"] = $last_point_y;

                   $cb = 0;
                   while($cb < 1){
                    
                     $punto = bezier3($p1, $p2, $p3, $cb);
                    
                     $np = $path["n_punti"];
                     $path["punti"][$np]     = $punto["x"];
                     $path["punti"][$np + 1] = $punto["y"];
                     $path["n_punti"] += 2;
 
                     $cb += 0.1;
                   }
                               
                   $k += 4; 
                }
            }
            // < 4 :  mancano dei punti !!
          }

          /////////////////// COMANDO T ///////////////////////
          if (($elemento_path["comando"][$j] == "t") or ($elemento_path["comando"][$j] == "T")){
            if($elemento_path["n_valori"][$j] >= 2){
                $k = 0;
                while($k < $elemento_path["n_valori"][$j]){

                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "t"){
                      $x_agg = ($last_point_x - $tr_x) / ($vb_x * $sc_x);
                      $y_agg = ($last_point_y - $tr_y) / ($vb_y * $sc_y);
                   }                   

                   $old_last_x = $last_point_x;
                   $old_last_y = $last_point_y;
                   
                   if(($elemento_path["comando"][$j -1] == "Q") or ($elemento_path["comando"][$j -1] == "q")){
                      
                     if(($x_control_1 != "") and ($y_control_1 != "")){
                       // reflection
                       $x_control_1 =  $old_last_x + $old_last_x - $x_control_1;
                       $y_control_1 =  $old_last_y + $old_last_y - $y_control_1;
                     }
                     else{
                        $x_control_1 = $old_last_x;
                        $y_control_1 = $old_last_y;
                     }
                   }
                   else{
                      $x_control_1 = $old_last_x;
                      $y_control_1 = $old_last_y;
                   }
                                   
                   $last_point_x = (($elemento_path["valori"][$j][$k    ] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $last_point_y = (($elemento_path["valori"][$j][$k + 1] + $y_agg) * $vb_y * $sc_y) + $tr_y;

                   $p1["x"] = $old_last_x;
                   $p1["y"] = $old_last_y;
                    
                   $p2["x"] = $x_control_1;
                   $p2["y"] = $y_control_1;
                    
                   $p3["x"] = $last_point_x;
                   $p3["y"] = $last_point_y;

                 
                   $cb = 0;
                   while($cb < 1){
                    
                     $punto = bezier3($p1, $p2, $p3, $cb);
                    
                     $np = $path["n_punti"];
                     $path["punti"][$np]     = $punto["x"];
                     $path["punti"][$np + 1] = $punto["y"];
                     $path["n_punti"] += 2;
 
                     $cb += 0.1;
                   }

                   $k += 2; 
                }
            }
            // < 2 :  mancano dei punti !!
          }

          /////////////////// COMANDO A ///////////////////////
                /////////////////////////////////////////
                // Approssimato con delle linee rette ///
                /////////////////////////////////////////
          ////////////////////////////////////////////////////
          if (($elemento_path["comando"][$j] == "a") or ($elemento_path["comando"][$j] == "A")){
            $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            
            if($elemento_path["n_valori"][$j] >= 7){
                $k = 0;
                while($k < $elemento_path["n_valori"][$j]){

                   $old_last_x = $last_point_x;
                   $old_last_y = $last_point_y;

                   $x_agg = 0; $y_agg = 0;
                   if($elemento_path["comando"][$j] == "a"){
                    $x_agg = ($old_last_x - $tr_x) / ($vb_x * $sc_x);
                    $y_agg = ($old_last_y - $tr_y) / ($vb_y * $sc_y);
                   }
                           
                   $x_rad          = $elemento_path["valori"][$j][$k    ] * $vb_x * $sc_x;
                   $y_rad          = $elemento_path["valori"][$j][$k + 1] * $vb_y * $sc_y;
                   $x_rotation     = $elemento_path["valori"][$j][$k + 2];
                   $flag_large_arc = $elemento_path["valori"][$j][$k + 3];
                   $flag_sweep     = $elemento_path["valori"][$j][$k + 4];
           
                   $last_point_x = (($elemento_path["valori"][$j][$k + 5] + $x_agg) * $vb_x * $sc_x) + $tr_x;
                   $last_point_y = (($elemento_path["valori"][$j][$k + 6] + $y_agg) * $vb_y * $sc_y) + $tr_y;

                   $np = $path["n_punti"];
                   $path["punti"][$np]     = $last_point_x;
                   $path["punti"][$np + 1] = $last_point_y;
                   $path["n_punti"] += 2;

                    
                   /* La porzione di codice che segue non e' corretta!! 
                   if($flag_large_arc == 0){
                    if($flag_sweep == 0){
                       $first_line_x = $old_last_x;
                       $first_line_y = $old_last_y;
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_x = $last_point_x;    
                       }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                        $first_line_y = $last_point_y;
                       }
    
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                            $first_line_x = $last_point_x;
                       }
                       else{
                           $first_line_y = $last_point_y;   

                       }
                       
                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $first_line_x;
                           $path["punti"][$np + 1] = $first_line_y;
                           $path["punti"][$np + 2] = $last_point_x;
                           $path["punti"][$np + 3] = $last_point_y;
                           $path["n_punti"] += 4;

                    }
                    else{
                       $first_line_x = $old_last_x;
                       $first_line_y = $old_last_y;
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_y = $last_point_y;    
                       }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                        $first_line_x = $last_point_x;
                       }
    
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                            $first_line_y = $last_point_y;
                       }
                       else{
                           $first_line_x = $last_point_x;   

                       }

                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $first_line_x;
                           $path["punti"][$np + 1] = $first_line_y;
                       $path["punti"][$np + 2] = $last_point_x;
                           $path["punti"][$np + 3] = $last_point_y;
                           $path["n_punti"] += 4;
                       
                    }
                   }
                   else{
                      if($flag_sweep == 0){          
                       $first_line_x = $old_last_x;
                       $first_line_y = $old_last_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_y -= ($last_point_y - $old_last_y);   
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_x -= ($last_point_x - $old_last_x);   
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $first_line_y -= ($last_point_y - $old_last_y);   
                       }
                       else{
                          $first_line_x -= ($last_point_x - $old_last_x);   
                       }
                       
                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $first_line_x;
                           $path["punti"][$np + 1] = $first_line_y;
                           $path["n_punti"] += 2;

                             
                       $second_line_x = $first_line_x;
                       $second_line_y = $first_line_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $second_line_x += (2 *($last_point_x - $old_last_x)); 
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $second_line_y += (2 * ($last_point_y - $old_last_y));    
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $second_line_x += (2 * ($last_point_x - $old_last_x));    
                       }
                       else{
                          $second_line_y += (2 * ($last_point_y - $old_last_y));    
                       }
                       
                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $second_line_x;
                           $path["punti"][$np + 1] = $second_line_y;
                           $path["n_punti"] += 2;
                                
                       $third_line_x = $second_line_x;
                       $third_line_y = $second_line_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $third_line_y += (2 *($last_point_y - $old_last_y));  
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $third_line_x += (2 * ($last_point_x - $old_last_x)); 
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $third_line_y += (2 * ($last_point_y - $old_last_y)); 
                       }
                       else{
                          $third_line_x += (2 * ($last_point_x - $old_last_x)); 
                       }
         
                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $third_line_x;
                           $path["punti"][$np + 1] = $third_line_y;
                       $path["punti"][$np + 2] = $last_point_x;
                           $path["punti"][$np + 3] = $last_point_y;
                           $path["n_punti"] += 4;
                       
                      }
                      else{  // 1  e  1
                           $first_line_x = $old_last_x;
                       $first_line_y = $old_last_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_x -= ($last_point_y - $old_last_y);   
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $first_line_y -= ($last_point_y - $old_last_y);   
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $first_line_x -= ($last_point_x - $old_last_x);   
                       }
                       else{
                          $first_line_y -= ($last_point_y - $old_last_y);   
                       }

                           $np = $path["n_punti"];
                       $path["punti"][$np]     = $first_line_x;
                           $path["punti"][$np + 1] = $first_line_y;
                           $path["n_punti"] += 2;
                                                 
                       $second_line_x = $first_line_x;
                       $second_line_y = $first_line_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $second_line_y += (2 *($last_point_y - $old_last_y)); 
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $second_line_x += (2 * ($last_point_x - $old_last_x));    
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $second_line_y += (2 * ($last_point_y - $old_last_y));    
                       }
                       else{
                          $second_line_x += (2 * ($last_point_x - $old_last_x));    
                       }
                       
                           $np = $path["n_punti"];
                       $path["punti"][$np]     = $second_line_x;
                           $path["punti"][$np + 1] = $second_line_y;
                           $path["n_punti"] += 2;
                                        
                       $third_line_x = $second_line_x;
                       $third_line_y = $second_line_y;
                       
                       if(($old_last_x >= $last_point_x) and ($old_last_y < $last_point_y)){
                          $third_line_x += (2 *($last_point_x - $old_last_x));  
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y < $last_point_y)){
                          $third_line_y += (2 * ($last_point_y - $old_last_y)); 
                           }
                       elseif(($old_last_x < $last_point_x) and ($old_last_y >= $last_point_y)){
                          $third_line_x += (2 * ($last_point_x - $old_last_x)); 
                       }
                       else{
                          $third_line_y += (2 * ($last_point_y - $old_last_y)); 
                       }
        
                       $np = $path["n_punti"];
                       $path["punti"][$np]     = $third_line_x;
                           $path["punti"][$np + 1] = $third_line_y;
                       $path["punti"][$np + 2] = $last_point_x;
                           $path["punti"][$np + 3] = $last_point_y;
                           $path["n_punti"] += 4;                                   
                      }
                   }  
                   */
                   $k += 7; 
                }
            }
            // < 7 :  mancano dei punti !!
          }

          /////////////////// COMANDO Z ///////////////////////          
          if (($elemento_path["comando"][$j] == "z") or ($elemento_path["comando"][$j] == "Z")){          
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
                
                if(($last_point_x != $first_point_x) or ($last_point_y != $first_point_y)){
                     $last_point_x = $first_point_x;
                     $last_point_y = $first_point_y;

                     $np = $path["n_punti"];
                     $path["punti"][$np]     = $last_point_x;
                     $path["punti"][$np + 1] = $last_point_y;
                     $path["n_punti"] += 2;
                }
          }
          $j += 1;
        }
        if($path["n_punti"] > 2){
            visualizza_segmento_path($image,$path,$colori,$color_stroke,$stroke_w,$color_fill);
        }
      }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      // VISUALIZZA_SEGMENTO_PATH:
      //    visualizza una porzione del path, trattandolo come fosse un poligono
      //
      function visualizza_segmento_path(&$image,$path,$colori,$color_stroke,$stroke_w,$color_fill){
        if($color_fill != "none"){
            if($path["n_punti"] > 2){
               $np = $path["n_punti"];
               $path["punti"][$np] = $path["punti"][0];
               $path["punti"][$np + 1] = $path["punti"][1];
               $path["n_punti"] += 2;

               imagefilledpolygon($image,$path["punti"],$path["n_punti"] / 2, $colori[$color_fill]);

               $path["n_punti"] -= 2;
            }
        
        }
        if($color_stroke != "none"){

              $k = - ($stroke_w / 2);
              while($k < ($stroke_w / 2)){

                $j = 0;
                while ($j < $path["n_punti"] - 2){

                    // GESTIONE ANGOLI //
                    gestione_angoli($path["punti"][$j],$path["punti"][$j + 2],
                                    $path["punti"][$j + 1],$path["punti"][$j + 3],$seno,$coseno);

                    calcola_inversione($path["punti"][$j    ],$path["punti"][$j + 2],
                                       $path["punti"][$j + 1],$path["punti"][$j + 3],
                                       $x1,$x2,$y1,$y2,$inv);                          
                
                    $y_val = $k * $inv * $seno ;
                    $x_val = $k * $inv * $coseno;

                    // devo aver gia' disegnato una linea.  
                    if($j > 0){
                    
                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori[$color_stroke]);
                    }
                    else{
                        $punto_iniziale_x = $x1 + $x_val;
                        $punto_iniziale_y = $y1 + $y_val;
                    }

                    imageline($image,$x1 + $x_val, $y1 + $y_val,
                              $x2 + $x_val, $y2 + $y_val,
                              $colori[$color_stroke]);
                        
                    $j += 2;

                    $last_point_x = $x2 + $x_val;
                    $last_point_y = $y2 + $y_val;

                    if($j >= $path["n_punti"] - 2 ){
                        if(($path["punti"][0] == $path["punti"][$j]) and 
                           ($path["punti"][1] == $path["punti"][$j + 1])){  
                                    
                            imageline($image,$last_point_x, $last_point_y,
                                      $punto_iniziale_x, $punto_iniziale_y,
                                      $colori[$color_stroke]);
                        }
                    }               
                }       
                $k += 0.01;
              }           
        }
      } // fine funzione visualizza_segmento_path


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      // GESTIONE_FILE:
      //    funzione principale che gestisce ogni riga del documento
      //
      function gestione_file(&$image, &$riga, &$n_viewbox, &$viewbox_x, &$viewbox_y, &$n_transform, 
                             &$n_translate, &$n_scale, &$n_rotate, &$livello, &$contenuto_defs, &$n_defs,
                             &$translate_x, &$translate_y, &$scale_x, &$scale_y, &$tipo_transform, 
                             &$n_stroke, &$n_fill, &$fill, &$stroke, &$fill_livello, &$stroke_livello,
                             &$stroke_width, &$w, &$h, &$n_wh, &$w_val, &$h_val, &$translate_livello, 
                             &$rotate_livello, &$rotate, &$scale_livello, &$viewbox_livello, &$transform_livello, 
                             &$wh_livello, $colori, $primo_svg, &$in_text, &$testo, &$n_testi, &$path_comando, 
                             &$path_valori, &$path_n_valori, &$path_n_comandi, &$x_text, &$y_text, &$n_font, &$font_size, 
                             &$font_livello, $url_base, &$font_f, &$in_textpath, &$text_a){                  


    /////////////////////////////////////////////
    // gestione valori percentuali //////////////   
    /////////////////////////////////////////////
    
    // imposto due variabili che contengono i valori a cui eventuali valori espressi in percentuale
    //   si riferiscono
    
    /// X ///////////////////////////////////////
    if($n_viewbox > 0){
        $perc_x = $viewbox_x[$n_viewbox - 1];
    }
    elseif($n_wh > 0){
        $perc_x = $w_val[$n_wh - 1];    
    }
    else{
        $perc_x = $w;
    }

    /// Y ///////////////////////////////////////
    if($n_viewbox > 0){
        $perc_y = $viewbox_y[$n_viewbox - 1];
    }
    elseif($n_wh > 0){
        $perc_y = $h_val[$n_wh - 1];    
    }
    else{
        $perc_y = $h;
    }
    ///////////////////////////////////////////
    //////////////////////////////////////////

    /////////////////////
    //// gestione TEXT //
    ////////////////////
    if($in_text == 1){
     
      if(preg_match("/(.*<\/text>.*)/",$riga)){
        // se mi trovo qui significa che e' finito il testo e posso 
        //  procedere alla visualizzazione
        
        $in_text = 0;
        
        conversione(&$tr_x, &$tr_y, &$sc_x, &$sc_y, &$vb_x, &$vb_y,
                 $n_transform, $tipo_transform, $translate_x, $translate_y, $translate_livello, 
                 $scale_x, $scale_y,
                 $n_viewbox, $viewbox_x, $viewbox_y,$viewbox_livello, 
                 $w, $h, $n_wh, $wh_livello, $w_val, $h_val);


       $x = $x_text;
       $y = $y_text;

       $x = ($x * $vb_x * $sc_x) + $tr_x;
       $y = ($y * $vb_y * $sc_y) + $tr_y;
        
      // gestione fill 
      $color_fill = $fill[$n_fill - 1];
    
    ////////////////////////////
    // FASE DI VISUALIZZAZIONE /
    ////////////////////////////

    // Impostiamo i valori:
    $j = 0;
    
    // gestisco ogni porzione di testo precedentemente memorizzata nell'array testo
    //  in questo ciclo vengono impostate le proprieta' relative al tipo di font, alla
    //  dimensione, al colore e al posizionamento di ogni porzione di testo, determinata
    //  in base ai testi precedenti e alla loro dimensione (comprensiva di un aggiustamento)
    //
    while($j <= $n_testi){
      $testo[$j]["valore"] = preg_replace("/(.*)(\n)(.*)/","\$1\$3",$testo[$j]["valore"]);
     
      // imposto un valore di spaziatura, servira' per approssimare la rappresentazione
      //   dei testi in base al tipo di font. Per ora sono supportati solo 3 tipi di font:
      //        Arial
      //        Verdana
      //        Times  
      $font_size_temp = $font_size[$n_font - 1] * $vb_x * $sc_x;
      $spaziatura = $font_size_temp;
      
      $font_family_temp = $font_f[$font_f["n"] - 1]["valore"];
     
      if((preg_match("/(Verdana)(.*)/",$font_family_temp)) or (preg_match("/(verdana)(.*)/",$font_family_temp))){
            $spaziatura /= 2;
      }
      elseif((preg_match("/(Arial)(.*)/",$font_family_temp)) or (preg_match("/(arial)(.*)/",$font_family_temp))){
            $spaziatura /= 2;
      }
      else{
            $spaziatura /= 2.6;
      }

      $spaziatura_old = $spaziatura;
      
      // Tolgo i doppi spazi
      while(preg_match("/(.*?)(\s)(\s)(.*?)/",$testo[$j]["valore"])){
      $testo[$j]["valore"] = preg_replace("/(.*?)(\s)(\s)(.*?)/","\$1\$2\$4",$testo[$j]["valore"]);
      }
      
      // testo contenuto nell'elemento text
      if($testo[$j]["tipo"] == "text"){
        $testo[$j]["colore"] = $colori[$color_fill];
        $testo[$j]["font"]   = $font_size[$n_font - 1];
        $testo[$j]["font_f"]   = $font_f[$font_f["n"] - 1]["valore"];

        $testo[$j]["x-val"]  = $x;
        $testo[$j]["y-val"]  = $y;

        // considero tutti i testi precedenti, per posizionare correttamente la porzione
        //   di testo corrente
        $k = 0;
        while($k < $j){
          if($testo[$k]["tipo"] == "text"){
            $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura_old;
          }    
          else{
    
            $spaziatura = $spaziatura_old;
            if($testo[$k]["font"] != "none"){   
                
               $font_size_temp = $testo[$k]["font"] * $vb_x * $sc_x; 
             
               $spaziatura = $font_size_temp;
                 
               $font_family_temp = $font_f[$font_f["n"] - 1]["valore"];
               
               if($testo[$k]["font_f"] != "none"){
                 $font_family_temp = $testo[$k]["font_f"];
               }
     
     
              if((preg_match("/(Verdana)(.*)/",$font_family_temp)) or 
                 (preg_match("/(verdana)(.*)/",$font_family_temp))){
                    $spaziatura /= 2;
              }
              elseif((preg_match("/(Arial)(.*)/",$font_family_temp)) or 
                 (preg_match("/(arial)(.*)/",$font_family_temp))){
                    $spaziatura /= 2;
              }
              else{
                    $spaziatura /= 2;
              }
              
             }
      
             // Gestione X  
             if($testo[$k]["tipo"] == "textPath"){
               // DEBUG
               //echo $testo[$k]["valore"]."<br />";
               $testo[$j]["x-val"] += $tr_x;
             }
             elseif($testo[$k]["x"] == "none"){
                if($testo[$k]["inc-x"] == 0){
                    $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura;
                }
                else{
                // DEBUG
                //  echo $testo[$j]["valore"]."(".$k.")<br />"; 
            
                $testo[$j]["x-val"] += (strlen($testo[$k]["valore"]) * $spaziatura) 
                                    + $testo[$k]["inc-x"] * ($vb_x * $sc_x);
                }
             }
             // x != none
             else{           
                $testo[$j]["x-val"] = ($testo[$k]["x"] * $vb_x * $sc_x) + $tr_x;
                if($testo[$k]["inc-x"] == 0){
                   $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura;
                }
                else{
                    $testo[$j]["x-val"] += (strlen($testo[$k]["valore"]) * $spaziatura) 
                                        + $testo[$k]["inc-x"] * ($vb_x * $sc_x);
                }
             }

             // Gestione Y  
             if($testo[$k]["tipo"] == "textPath"){
               $testo[$j]["y-val"] += $tr_y;    
             }
             elseif($testo[$k]["y"] == "none"){
                if($testo[$k]["inc-y"] != 0){
                    $testo[$j]["y-val"] = $testo[$k]["y-val"];
                }
             }
             
             // y != none
             else{
                if($testo[$k]["inc-y"] != 0){
                    $testo[$j]["y-val"] = $testo[$k]["y-val"];
                }
                else{
                    $testo[$j]["y-val"] = ($testo[$k]["y"] * $vb_y * $sc_y) + $tr_y; 
                }
             }  
          }
          $k += 1;
        }
      }
      
      // tipo != text
      else{
        if($testo[$j]["colore"] == "none"){
            $testo[$j]["colore"] = $colori[$color_fill];
        }
        if($testo[$j]["font"] == "none"){           
            $testo[$j]["font"] = $font_size[$n_font - 1];
            $l = $j;
            while($l > 0){
               if($testo[$l - 1]["tipo"] == "text"){
                $testo[$j]["font"] = $testo[$l - 1]["font"];
                $l = 0;
               }
               $l -= 1;
            }
        }
        if($testo[$j]["font_f"] == "none"){         
            $testo[$j]["font_f"] = $font_f[$font_f["n"] - 1]["valore"];
            $l = $j;
            while($l > 0){
               if($testo[$l - 1]["tipo"] == "text"){
                $testo[$j]["font_f"] = $testo[$l - 1]["font_f"];
                $l = 0;
               }
               $l -= 1;
            }
        }

        $testo[$j]["x-val"] = $x;
        $testo[$j]["y-val"] = $y;
        $k = 0;
        while($k < $j){
          if($testo[$k]["tipo"] == "text"){
            $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura_old;
          }
                      
          else{

            $spaziatura = $spaziatura_old;
            if($testo[$k]["font"] != "none"){   
                
               $font_size_temp = $testo[$k]["font"] * $vb_x * $sc_x; 
             
               $spaziatura = $font_size_temp;
                 
               $font_family_temp = $font_f[$font_f["n"] - 1]["valore"];
               
               if($testo[$k]["font_f"] != "none"){
                 $font_family_temp = $testo[$k]["font_f"];
               }
     
     
              if((preg_match("/(Verdana)(.*)/",$font_family_temp)) or 
                 (preg_match("/(verdana)(.*)/",$font_family_temp))){
                    $spaziatura /= 2;
              }
              elseif((preg_match("/(Arial)(.*)/",$font_family_temp)) or 
                 (preg_match("/(arial)(.*)/",$font_family_temp))){
                    $spaziatura /= 2;
              }
              else{
                    $spaziatura /= 2;
              }
              
             }

                          
             // Gestione X  
             if($testo[$k]["tipo"] == "textPath"){
               $testo[$j]["x-val"] = $tr_x;    
             } 
             elseif($testo[$k]["x"] == "none"){
                if($testo[$k]["inc-x"] == 0){
                   $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura;
                }
                else{
                    $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura 
                                        + $testo[$k]["inc-x"] * ($vb_x * $sc_x);
                }
             }
             // x != none
             else{
                $testo[$j]["x-val"] = ($testo[$k]["x"] * $vb_x * $sc_x) + $tr_x;
                if($testo[$k]["inc-x"] == 0){
                    $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura;
                }
                else{
                    $testo[$j]["x-val"] += strlen($testo[$k]["valore"]) * $spaziatura 
                                        + $testo[$k]["inc-x"] * ($vb_x * $sc_x);
                }
             }       

             // Gestione Y
             if($testo[$k]["tipo"] == "textPath"){           
               $testo[$j]["y-val"] = $tr_y; 
             }
             elseif($testo[$k]["y"] == "none"){
                if($testo[$k]["inc-y"] != 0){
                    $testo[$j]["y-val"] = $testo[$k]["y-val"];
                }
             }
             // y != none
             else{
                if($testo[$k]["inc-y"] != 0){            
                   $testo[$j]["y-val"] = $testo[$k]["y-val"];
                }
                else{
                    $testo[$j]["y-val"] = ($testo[$k]["y"] * $vb_y * $sc_y) + $tr_y; 
                }
             }  
          }
          $k += 1;
        }

        if($testo[$j]["x"] != "none"){
            $testo[$j]["x-val"] = ($testo[$j]["x"] * $vb_x * $sc_y) + $tr_x;
        }
        if($testo[$j]["y"] != "none"){
            $testo[$j]["y-val"] = ($testo[$j]["y"] * $vb_y * $sc_y) + $tr_y;
        }
    
                
        $testo[$j]["x-val"] += ($testo[$j]["inc-x"] * $vb_x * $sc_x);
        $testo[$j]["y-val"] += ($testo[$j]["inc-y"] * $vb_y * $sc_y);
        
      }
      
      $j += 1;
      
    } // fine gestione delle porzioni del testo
    
    // Qui inizia la fase vera e propria di visualizzazione
      $j = 0;

      // gestione dell'eventuale valore di text-anchor (solo per testi
      //        semplici, senza tspan, tref, ...)      
      $agg_text_anchor = 0;
      if($n_testi == 0){
        $len_testo = strlen($testo[0]["valore"]);
        $font_size_testo = $testo[0]["font"] * $vb_x * $sc_x;
        $font_f_testo = $testo[0]["font_f"];
  
        $ta = $text_a[$text_a["n"] - 1]["valore"];
        if($ta == "end"){
        $agg_text_anchor = - ($len_testo * $font_size_testo / 2);
            }
        elseif($ta == "middle"){
        $agg_text_anchor = - ($len_testo * $font_size_testo / 4);
        }
        
      }

      // visualizzo ogni porzione di testo
      while($j <= $n_testi){

        // carico il font opportuno
        $font_name_temp = $testo[$j]["font_f"];
        if((preg_match("/(Verdana)(.*)/",$font_name_temp)) or (preg_match("/(verdana)(.*)/",$font_name_temp))){
            $font_name = 'FONT/verdana.ttf';
        }
        elseif((preg_match("/(Arial)(.*)/",$font_name_temp)) or (preg_match("/(arial)(.*)/",$font_name_temp))){
            $font_name = 'FONT/arial.ttf';
        }
        else{
            $font_name = 'FONT/times.ttf';
        }

        // DEBUG
        //echo $testo[$j]["valore"]." (".$testo[$j]["font"].")<br />";
        
        $dim = $testo[$j]["font"] *  $vb_x * $sc_x;

        if($font_name == "FONT/times.ttf"){
            $dim /= 1.3;
        }
        elseif($font_name == "FONT/verdana.ttf"){
            $dim /= 1.5;
        }
        elseif($font_name == "FONT/arial.ttf"){
            $dim /= 1.4;
        }

        // DEBUG
        //$ll = strlen($testo[$j]["valore"]);
        //echo "TT_".$testo[$j]["valore"]."_TT (".$ll.")<br />";
        
        
        /////////////////////////////////////////////////////////////////////
        //////////////////  VISUALIZZAZIONE DEL TESTO  //////////////////////
        /////////////////////////////////////////////////////////////////////
        imagettftext($image, $dim, 0, $testo[$j]["x-val"] + $agg_text_anchor, $testo[$j]["y-val"],
                     $testo[$j]["colore"], $font_name, $testo[$j]["valore"]);
        /////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////

        $j += 1;
      }
      
      } // fine if /text 
     
     ////////////////////////////////////////////////
     // a questo punto sono dentro text
     
     // gestione tspan
     elseif(preg_match("/(.*<tspan.*)/",$riga)){

         if($in_textpath == "false"){
           $n_testi += 1;
           $testo[$n_testi]["tipo"]   = "tspan";
         
           $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
                
           gestione_attributi_testo($testo, $n_testi, $riga, $font_size[$n_font - 1], 
                                    $n_viewbox, $viewbox_x, $viewbox_y, $n_wh,$w_val, 
                                    $h_val, $w, $h, $colori, $perc_x, $perc_y); 
         }
         else{
            $testo[$n_testi]["valore"] .= preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
      }
      
      elseif(preg_match("/(.*<\/tspan.*)/",$riga)){
         if($in_textpath == "false"){
            $n_testi += 1;
            $testo[$n_testi]["tipo"]   = "text";
            $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
         else{
            $testo[$n_testi]["valore"] .= preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
      }

      // gestione a
      elseif(preg_match("/(.*<a.*)/",$riga)){
      
        if($in_textpath == "false"){
            $n_testi += 1;
            $testo[$n_testi]["tipo"]   = "tspan";
             
            $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
       
            gestione_attributi_testo($testo, $n_testi, $riga, $font_size[$n_font - 1], 
                                     $n_viewbox, $viewbox_x, $viewbox_y, $n_wh,$w_val, 
                                     $h_val, $w, $h, $colori, $perc_x, $perc_y);    
        }
        else{
          $testo[$n_testi]["valore"] .= preg_replace("/(.*)(>)(.*)/","\$3",$riga);
        }
      }
      
      elseif(preg_match("/(.*<\/a.*)/",$riga)){
         if($in_textpath == "false"){
            $n_testi += 1;
            $testo[$n_testi]["tipo"]   = "text";
            $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
         else{
            $testo[$n_testi]["valore"] .= preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
      }
        
      // gestione textPath  
      elseif(preg_match("/(.*<textPath.*)/",$riga)){
         $n_testi += 1;
         $in_textpath = "true";
         $testo[$n_testi]["tipo"] = "textPath";
         $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         
         gestione_attributi_testo($testo, $n_testi, $riga, $font_size[$n_font - 1], 
                                  $n_viewbox, $viewbox_x, $viewbox_y, $n_wh,$w_val, 
                                  $h_val, $w, $h, $colori, $perc_x, $perc_y);

         if(preg_match("/(.*href=.*)/",$riga)){
            $href = preg_replace("/(.*href=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$href)){
               $href = preg_replace("/(.*)(\".*)/","\$1",$href);
            }

            $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
            $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);

            // cerco il path riferito all'interno di defs
            // NB: il path potrebbe non essere in defs !!!   
            $j = 0;
            while ($j < $n_defs){
                $riga_temp = $contenuto_defs[$j];
                if(preg_match("/(.*id=\"$href\".*)/",$riga_temp)){
                    $nome_elemento = preg_replace("/(.*<)(\w+)(.*)/","\$2",$riga_temp);
                    $nome_elemento = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$nome_elemento);
            
                    if($nome_elemento == "path"){
               
                
                        if(preg_match("/(.* d=\".*)/",$riga_temp)){
                            $d = preg_replace("/(.* d=\")(.*)(\".*)/","\$2",$riga_temp);
                            while(preg_match("/(.*\".*)/",$d)){
                                $d = preg_replace("/(.*)(\".*)/","\$1",$d);
                            }
                            $d = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$d);
                
                            $path_comando_temp = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$2",$d);
                            $path_comando_temp = preg_replace("/\s/","",$path_comando_temp);
                            $d = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$3",$d);
                            $val_temp = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$1",$d);
                            
                            $k = 0;
                            while(preg_match("/(\d)/",$val_temp)){
                                $valori_t[$k] = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$2",$val_temp);
                                $val_temp  = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$3",$val_temp);
                                $k += 1;
                            }   
                
                            if($path_comando_temp == "M"){                  
                                $testo[$n_testi]["x"] = ($valori_t[0]);
                                $testo[$n_testi]["y"] = ($valori_t[1]);
                            }   
                            elseif($path_comando_temp == "m"){
                                $testo[$n_testi]["inc-x"] += $valori_t[0];
                                $testo[$n_testi]["inc-y"] += $valori_t[1];
                            }
                        }    
                 
                    }
                    $j = $n_defs;
                }   
                $j += 1;
            }
         }
      }

      elseif(preg_match("/(.*<\/textPath.*)/",$riga)){
         $n_testi += 1;
         $in_textpath = "false";
         $testo[$n_testi]["tipo"]   = "text";
         $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
      }
      
      
      elseif(preg_match("/(.*<tref.*)/",$riga)){

         if($in_textpath == "false"){   
            $n_testi += 1;
            $testo[$n_testi]["tipo"]  = "tref";
            $testo[$n_testi]["valore"] = "";               

            gestione_attributi_testo($testo, $n_testi, $riga, $font_size[$n_font - 1], 
                                     $n_viewbox, $viewbox_x, $viewbox_y, $n_wh,$w_val, 
                                     $h_val, $w, $h, $colori, $perc_x, $perc_y);
        }

         if(preg_match("/(.*href=.*)/",$riga)){
            $href = preg_replace("/(.*href=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$href)){
               $href = preg_replace("/(.*)(\".*)/","\$1",$href);
            }

            $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
            $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);

            // cerco il testo riferito all'interno di defs
            $j = 0;
            while ($j < $n_defs){
                $riga_temp = $contenuto_defs[$j];
                if(preg_match("/(.*id=\"$href\".*)/",$riga_temp)){
                    $nome_elemento = preg_replace("/(.*<)(\w+)(.*)/","\$2",$riga_temp);
                    $nome_elemento = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$nome_elemento);
            
                    if($nome_elemento == "text"){    
                       $fine_defs = 0;
                              
                       while($fine_defs == 0){
                          $testo[$n_testi]["valore"].= preg_replace("/(.*)(>)(.*)/","\$3",$riga_temp);
                          $j += 1;
                          $riga_temp = $contenuto_defs[$j];

                          if(preg_match("/(.*<\/text>.*)/",$riga_temp)){
                            $fine_defs = 1;
                            $j = $n_defs;
                          }
                       }
                    }                   
                }   
                $j += 1;
            }
         }

         if(preg_match("/(.*>.*)/",$riga)){
            if($in_textpath == "false"){
               $n_testi += 1;
               $testo[$n_testi]["tipo"]   = "text";
               $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
            }
            else{
                $testo[$n_testi]["valore"] .= preg_replace("/(.*)(>)(.*)/","\$3",$riga);
            }
         }
      }
      
      elseif(preg_match("/(.*<\/tref.*)/",$riga)){

         if($in_textpath == "false"){
             $n_testi += 1;
             $testo[$n_testi]["tipo"]   = "text";
             $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
         else{
            $testo[$n_testi]["valore"] = preg_replace("/(.*)(>)(.*)/","\$3",$riga);
         }
      }
      
      // testo contenuto in text (non in un sottoelemento)
      else{
        $testo[$n_testi]["valore"] .= $riga;
      }
      
      
    } // FINE GESITIONE TEXT
    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////


    ///////////////////////////////////
    ///  Aumento il livello dei TAG ///
    //////////////////////////////////
    if(preg_match("/(.*<\/.*)/",$riga)){
    }
    elseif(preg_match("/(.*<.*)/",$riga)){
        $livello += 1;
    }


    ///////////////////////////////////////////////////
    // gestione viewbox ///////////////////////////////
    //////////////////////////////////////////////////
    if(preg_match("/(.*<image .*)/",$riga)){ }
    // in teoria questi elementi non dovrebbero avere viewbox
    elseif(preg_match("/(.*<polyline.*)/",$riga)){ }
    elseif(preg_match("/(.*<polygon.*)/",$riga)){ }
    elseif(preg_match("/(.*<line.*)/",$riga)){ }
    elseif(preg_match("/(.*<circle.*)/",$riga)){ }
    elseif(preg_match("/(.*<ellipse.*)/",$riga)){ }
    elseif(preg_match("/(.*<rect.*)/",$riga)){ }

    elseif(preg_match("/(.*viewBox.*)/",$riga)){
        $vb = preg_replace("/(.*viewBox=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$vb)){
              $vb = preg_replace("/(.*)(\".*)/","\$1",$vb);
        }

       ///////////////////////////////////////////////////////////////////
       /////////// Gestione COORDORIGIN (primi 2 elementi di vb) /////////
       //////////////////////////////////////////////////////////////////
       
       $vb_temp_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$5",$vb);
       $vb_temp_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$7",$vb);
       
       
       // calcolo w e h
       if(preg_match("/(.* width=.*)/",$riga)){
         $w_temp = preg_replace("/(.* width=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$w_temp)){
           $w_temp = preg_replace("/(.*)(\".*)/","\$1",$w_temp);
         }
         $w_temp = converti($w_temp, $font_size[$n_font - 1], $perc_x);

       }
       else{
         $w_temp = converti("100%", $font_size[$n_font - 1], $perc_x);

       }
       
       if(preg_match("/(.* height=.*)/",$riga)){
         $h_temp = preg_replace("/(.* height=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$h_temp)){
           $h_temp = preg_replace("/(.*)(\".*)/","\$1",$h_temp);
         }
         $h_temp = converti($h_temp, $font_size[$n_font - 1], $perc_y);

       }
       else{
         $h_temp = converti("100%", $font_size[$n_font - 1], $perc_y);
       }
       
       
       $x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$vb);
       $x = $x * (- 1);
       $y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$vb);
       $y = $y * (- 1);


       $tipo_transform[$n_transform] = "t";
       $translate_x[$n_translate] = $x * ($w_temp / $vb_temp_x);
       $translate_y[$n_translate] = $y * ($h_temp / $vb_temp_y);
         
       $translate_livello[$n_translate] = $livello;
       $transform_livello[$n_transform] = $livello;

       $n_transform += 1;
       $n_translate += 1;          
      
       ///////////////////////////////////////////////////////////////////
       /////////// Gestione COORDSIZE (ultimi 2 elementi di vb) /////////
       //////////////////////////////////////////////////////////////////       
       
       $viewbox_x[$n_viewbox] = $vb_temp_x;
       $viewbox_y[$n_viewbox] = $vb_temp_y;

       $viewbox_livello[$n_viewbox] = $livello;
       $n_viewbox += 1;
    }

    ///////////////////////////////////////////////////
    // gestione transform ///////////////////////////////
    //////////////////////////////////////////////////
    if(preg_match("/(.*transform.*)/",$riga)){
      $n_transform_old = $n_transform;
      $tr = preg_replace("/(.*transform=\")(.*)(\".*)/","\$2",$riga);
      while(preg_match("/(.*\".*)/",$vb)){
            $tr = preg_replace("/(.*)(\".*)/","\$1",$tr);
      }
          
      //$n_transform = 0;
      while(preg_match("/(.*\).*)/",$tr)){
        $transform[$n_transform] =  preg_replace("/(.*?)(\()(.*?)(\))(.*)/","\$1\$2\$3\$4",$tr);
        
        $tr =  preg_replace("/(.*?)(\()(.*?)(\))(.*)/","\$5",$tr);
        $transform_livello[$n_transform] = $livello;
        $n_transform += 1;
        
      }
   
       //$i = $n_transform;
       $i = $n_transform_old;   

       while($i < $n_transform){
         
         ////////////////// translate ////////////////////////////////
         if(preg_match("/(.*)(translate)(.*)/",$transform[$i])){
           $tipo_transform[$i] = "t";
           $tr_temp = preg_replace("/(.*)(translate\()(.*)/","\$3",$transform[$i]);
           $tr_temp = preg_replace("/\)/","",$tr_temp);

           if(preg_match("/(.*)(,)(.*)/",$tr_temp)){
              $translate_x[$n_translate] = preg_replace("/(.*)(,)(.*)/","\$1",$tr_temp);
              $translate_x[$n_translate] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$translate_x[$n_translate]);
              $translate_y[$n_translate] = preg_replace("/(.*)(,)(.*)/","\$3",$tr_temp);
              $translate_y[$n_translate] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$translate_y[$n_translate]);

           }
           elseif(preg_match("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/",$tr_temp)){
                $translate_x[$n_translate] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/","\$2",$tr_temp);
                $translate_x[$n_translate] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$translate_x[$n_translate]);
                $translate_y[$n_translate] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/","\$4",$tr_temp);
                $translate_y[$n_translate] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$translate_y[$n_translate]);
           }
           elseif(preg_match("/(\s*)(-?\d+\.?\d*)(\s*)/",$tr_temp)){
              $translate_x[$n_translate] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s*)/","\$2",$tr_temp);
              $translate_x[$n_translate] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$translate_x[$n_translate]);
              $translate_y[$n_translate] = 0;
           }
           else{
             // caso errato
             $translate_x[$n_translate] = 0;
             $translate_y[$n_translate] = 0;
           }
        
           $translate_livello[$n_translate] = $livello;
           $n_translate += 1;
         }
         /////////// scale ///////////////////
         elseif(preg_match("/(.*)(scale)(.*)/",$transform[$i])){
           $tipo_transform[$i] = "s";
           $tr_temp = preg_replace("/(.*)(scale\()(.*)/","\$3",$transform[$i]);
           $tr_temp = preg_replace("/\)/","",$tr_temp);

           if(preg_match("/(.*)(,)(.*)/",$tr_temp)){
              $scale_x[$n_scale] = preg_replace("/(.*)(,)(.*)/","\$1",$tr_temp);
              $scale_x[$n_scale] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$scale_x[$n_scale]);
              $scale_y[$n_scale] = preg_replace("/(.*)(,)(.*)/","\$3",$tr_temp);
              $scale_y[$n_scale] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$scale_y[$n_scale]);
           }
           elseif(preg_match("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/",$tr_temp)){
              $scale_x[$n_scale] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/","\$2",$tr_temp);
              $scale_x[$n_scale] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$scale_x[$n_scale]);
              $scale_y[$n_scale] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)(\s*)/","\$4",$tr_temp);
              $scale_y[$n_scale] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$scale_y[$n_scale]);
           }
           elseif(preg_match("/(\s*)(-?\d+\.?\d*)(\s*)/",$tr_temp)){
              $scale_x[$n_scale] = preg_replace("/(\s*)(-?\d+\.?\d*)(\s*)/","\$2",$tr_temp);
              $scale_x[$n_scale] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$scale_x[$n_scale]);
              $scale_y[$n_scale] = $scale_x[$n_scale];
           }
           else{
             // caso errato
             $scale_x[$n_scale] = 1; // ?????
             $scale_y[$n_scale] = 1; 
           }
           
           $scale_livello[$n_scale] = $livello;
           $n_scale += 1;
         }
         ////////////////////////// rotate //////////////////////////
         elseif(preg_match("/(.*)(rotate)(.*)/",$transform[$i])){
           $tipo_transform[$i] = "r";

           // impostato il valore ma non viene gestito
           $rotate[$n_rotate] = preg_replace("/(.*)(rotate\()(.*)/","\$3",$transform[$i]);
           $rotate_livello[$n_rotate] = $livello;
           $n_rotate += 1;

         }
         else{
           $tipo_transform[$i] = "a";
           // altrimenti: SKewX, SkewY, Matrix: non gestiti
         }
         $i += 1;
       }
      
      /* DEBUG
        echo "<br /><br />TRANSLATE: <br />";
        $c = 0;
        while($c < $n_translate){
            echo $c." - x: ".$translate_x[$c];
            echo " - y: ".$translate_y[$c]." ";
            echo " livello: ".$translate_livello[$c]."<br />";
            $c += 1;
        }
       
        echo "<br /><br />SCALE: <br />";
        $c = 0;
        while($c < $n_scale){
            echo $c." - x: ".$scale_x[$c];
            echo " - y: ".$scale_y[$c]."  ";
            echo " livello: ".$scale_livello[$c]."<br />";
            $c += 1;
        }
       */
       
       /* DEBUG
        echo "ROTATE: ";
        $c = 0;
        while($c < $n_rotate){
            echo $c." ".$rotate[$c]." \n";
            $c += 1;
         }
       */
    }

    // DEBUG
    //echo $n_font."  ".$font_s[$n_font]." cc ".$font_livello[$n_font]." l: ".$livello."<br />";
     
    ///////////////////////////////////////////////////
    // gestione dimensione font  //////////////////////
    //////////////////////////////////////////////////
    if((preg_match("/(.* font-size=.*)/",$riga) and ($in_text == 0))){
        $f = preg_replace("/(.* font-size=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$f)){
              $f = preg_replace("/(.*)(\".*)/","\$1",$f);
        }
        $f = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$f);
       
        // DEBUG
        //echo $f." (".$livello.")<br />";
        //echo $n_font."<br />";

        $font_size[$n_font] = $f;
        $font_livello[$n_font] = $livello;

        $n_font += 1;

    }
    
    ///////////////////////////////////////////////////
    // gestione font family      //////////////////////
    //////////////////////////////////////////////////
    if((preg_match("/(.* font-family=.*)/",$riga)) and  ($in_text == 0)){
       $ff = preg_replace("/(.* font-family=\")(.*)(\".*)/","\$2",$riga);
       while(preg_match("/(.*\".*)/",$ff)){
              $ff = preg_replace("/(.*)(\".*)/","\$1",$ff);
       }
       $ff = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$ff);

       $n_ff = $font_f["n"];

       $font_f[$n_ff]["valore"]  = $ff;
       $font_f[$n_ff]["livello"] = $livello;

       $font_f["n"] += 1;
       
    }

    ///////////////////////////////////////////////////
    // gestione text anchor     //////////////////////
    //////////////////////////////////////////////////
    if((preg_match("/(.* text-anchor=.*)/",$riga)) and  ($in_text == 0)){
       $ta = preg_replace("/(.* text-anchor=\")(.*)(\".*)/","\$2",$riga);
       while(preg_match("/(.*\".*)/",$ta)){
              $ta = preg_replace("/(.*)(\".*)/","\$1",$ta);
       }
       $ta = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$ta);

       $n_ta = $text_a["n"];

       $text_a[$n_ta]["valore"]  = $ta;
       $text_a[$n_ta]["livello"] = $livello;

       $text_a["n"] += 1;
    }

    //////////////////////////////////////////////////
    // gestione TEXT  ///////////////////////////////
    //////////////////////////////////////////////////
    if(preg_match("/(.*<text .*)/",$riga)){
      $in_text = 1;
      $testo[0]["valore"] = preg_replace("/(.*>)(.*)/","\$2",$riga);
      $testo[0]["tipo"]   = "text";
      $testo[0]["font"]   = $font_size[$n_font - 1];
      $testo[0]["font_f"] = $font_f[$font_f["n"] - 1]["valore"];

      //$testo[0]["x"] = 0;
      //$testo[0]["y"] = 0;
      $n_testi = 0;
      
      $x_text = 0;
      $y_text = 0;


      if((preg_match("/(.* x=.*)/",$riga)) or (preg_match("/(.* y=.*)/",$riga))){
         if(preg_match("/(.* x=.*)/",$riga)){
            $x = preg_replace("/(.* x=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$x)){
                $x = preg_replace("/(.*)(\".*)/","\$1",$x);
            }
            $x = converti($x, $font_size[$n_font - 1], $perc_x);

        }
        else{
            $x = 0;
        }
       
        if(preg_match("/(.* y=.*)/",$riga)){
            $y = preg_replace("/(.* y=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$y)){
                $y = preg_replace("/(.*)(\".*)/","\$1",$y);
            }
            $y = converti($y, $font_size[$n_font - 1], $perc_y);         
        }
        else{
             $y = 0;
        }
     
        $x_text = $x;
        $y_text = $y;

      }
    }

    ////////////////////////////////////////////////////////////////////
    // gestione stoke - fill (e stroke-width)  ////////////////////////
    ///////////////////////////////////////////////////////////////////
    if(preg_match("/(.* stroke=.*)/",$riga)){
      if(preg_match("/(.* stroke=\"none\".*)/",$riga)){
        $stroke_livello[$n_stroke] = $livello;
        $stroke[$n_stroke] = "none";
        $n_stroke += 1;
      }
      elseif(preg_match("/(.* stroke=\"url.*)/",$riga)){
        $stroke_livello[$n_stroke] = $livello;
        $stroke[$n_stroke] = "gray";
        $n_stroke += 1;
      }
      else{
        $str = preg_replace("/(.*stroke=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$str)){
              $str = preg_replace("/(.*)(\".*)/","\$1",$str);
        }
        $str = preg_replace("/\s/","",$str);

        $stroke_livello[$n_stroke] = $livello;
        $stroke[$n_stroke] = $str;
        $n_stroke += 1;
      }   
    }
    
    if(preg_match("/(.* fill=.*)/",$riga)){
     if(preg_match("/(.* fill=\"none\".*)/",$riga)){
        $fill_livello[$n_fill] = $livello;
        $fill[$n_fill] = "none";
        $n_fill += 1;
      }
      elseif(preg_match("/(.* fill=\"url.*)/",$riga)){
        $fill_livello[$n_fill] = $livello;
        $fill[$n_fill] = "gray";
        $n_fill += 1;
      }
      else{
        $fll = preg_replace("/(.*fill=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$fll)){
              $fll = preg_replace("/(.*)(\".*)/","\$1",$fll);
        }
        $fll = preg_replace("/\s/","",$fll);

        $fill_livello[$n_fill] = $livello;
        $fill[$n_fill] = $fll;
        $n_fill += 1;
      }   
    }
    
    if(preg_match("/(.* stroke-width=.*)/",$riga)){
        $str_w = preg_replace("/(.* stroke-width=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$str_w)){
              $str_w = preg_replace("/(.*)(\".*)/","\$1",$str_w);
        }
        $str_w = preg_replace("/\s/","",$str_w);
        $str_w = converti($str_w,$font_size[$n_font - 1], $perc_x);

        $stroke_width["livello"][$stroke_width["n"]] = $livello;
        $stroke_width["valore"][$stroke_width["n"]] = $str_w;
        $stroke_width["n"] += 1;
    }

    
    ////////////////////////////////////////////////
    // gestione RECT ///////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<rect.*)/",$riga)){
     
     predef("rect", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform,
        $n_translate, $n_rotate, $n_scale,
        $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
        $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val, 
        $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate,
        $scale_livello, $viewbox_livello, $transform_livello , $colori, $font_size[$n_font - 1],
        $perc_x, $perc_y);
    }

    ////////////////////////////////////////////////
    // gestione ELLIPSE ////////////////////////////
    ////////////////////////////////////////////////    
    elseif(preg_match("/(.*<ellipse.*)/",$riga)){   
      predef("ellipse", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
         $n_translate, $n_rotate, $n_scale,
         $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
         $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val, 
         $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate,
         $scale_livello, $viewbox_livello, $transform_livello , $colori, $font_size[$n_font - 1],
         $perc_x, $perc_y);   
    }

    ////////////////////////////////////////////////
    // gestione CIRCLE ////////////////////////////
    ////////////////////////////////////////////////    
    elseif(preg_match("/(.*<circle.*)/",$riga)){    
      predef("circle", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
         $n_translate, $n_rotate, $n_scale,
         $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
         $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val, 
         $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate, 
         $scale_livello, $viewbox_livello, $transform_livello , $colori, $font_size[$n_font - 1],
         $perc_x, $perc_y);   
    }

    ////////////////////////////////////////////////
    // gestione LINE ////////////////////////////
    ////////////////////////////////////////////////    
    elseif(preg_match("/(.*<line.*)/",$riga)){
      predef("line", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
         $n_translate, $n_rotate, $n_scale,
         $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
         $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val,
         $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate, 
         $scale_livello, $viewbox_livello, $transform_livello , $colori, $font_size[$n_font - 1],
         $perc_x, $perc_y);   
    }

    ////////////////////////////////////////////////
    // gestione POLYGON ////////////////////////////
    ////////////////////////////////////////////////
    elseif(preg_match("/(.*<polygon.*)/",$riga)){
      predef("polygon", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
         $n_translate, $n_rotate, $n_scale,
         $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
         $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val,
         $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate, 
         $scale_livello, $viewbox_livello, $transform_livello, $colori, $font_size[$n_font - 1],
         $perc_x, $perc_y);   
    }

    ////////////////////////////////////////////////
    // gestione POLYLINE ////////////////////////////
    ////////////////////////////////////////////////
    elseif(preg_match("/(.*<polyline.*)/",$riga)){
           predef("polyline", $image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
         $n_translate, $n_rotate, $n_scale,
         $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
         $n_stroke, $n_fill, $fill, $stroke, $stroke_width, $w, $h, $n_wh, $w_val, $h_val,
         $livello, $wh_livello, $translate_livello, $rotate_livello, $rotate, 
         $scale_livello, $viewbox_livello, $transform_livello , $colori, $font_size[$n_font - 1],
         $perc_x, $perc_y);   
    }
    
    ////////////////////////////////////////////////
    ////////////////////////////////////////////////
    ////////////////////////////////////////////////
    // gestione  X, Y, WIDTH e HEIGHT //////////////
    ////////////////////////////////////////////////
    ////////////////////////////////////////////////
    ////////////////////////////////////////////////
    
    // si impostano questi valori nelle varie pile (dimensioni e traslazione)
    //      per gestire questi valori per tutti quegli elementi non presi in
    //      considerazione 

    elseif(($primo_svg == "no") and ($in_text != "1")){
    
     // Imposto X e Y
     if((preg_match("/(.* x=.*)/",$riga)) or (preg_match("/(.* y=.*)/",$riga))){
       if(preg_match("/(.* x=.*)/",$riga)){
         $x = preg_replace("/(.* x=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$x)){
           $x = preg_replace("/(.*)(\".*)/","\$1",$x);
         }
         $x = converti($x, $font_size[$n_font - 1], $perc_x);
       }
       else{
         $x = 0;
       }
       
       if(preg_match("/(.* y=.*)/",$riga)){
         $y = preg_replace("/(.* y=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$y)){
           $y = preg_replace("/(.*)(\".*)/","\$1",$y);
         }
         $y = converti($y, $font_size[$n_font - 1], $perc_y);
         
       }
       else{
         $y = 0;
       }

       // DEBUG
       //echo "x: ".$x."  y: ".$y."<br />";

       $tipo_transform[$n_transform] = "t";
       $translate_x[$n_translate] = $x;
       $translate_y[$n_translate] = $y;

       $translate_livello[$n_translate] = $livello;
       $transform_livello[$n_transform] = $livello;

       $n_transform += 1;
       $n_translate += 1;          
     }
     
     // W e H
     if((preg_match("/(.* width=.*)/",$riga)) or (preg_match("/(.* height=.*)/",$riga))){
       if(preg_match("/(.* width=.*)/",$riga)){
         $w_temp = preg_replace("/(.* width=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$w_temp)){
           $w_temp = preg_replace("/(.*)(\".*)/","\$1",$w_temp);
         }
         $w_temp = converti($w_temp, $font_size[$n_font - 1], $perc_x);

       }
       else{  
         $w_temp = converti("100%", $font_size[$n_font - 1], $perc_x);
       }
       
       if(preg_match("/(.* height=.*)/",$riga)){
         $h_temp = preg_replace("/(.* height=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$h_temp)){
           $h_temp = preg_replace("/(.*)(\".*)/","\$1",$h_temp);
         }
         $h_temp = converti($h_temp, $font_size[$n_font - 1], $perc_y);

       }
       else{ 
         $h_temp = converti("100%", $font_size[$n_font - 1], $perc_y);
       }

       // DEBUG
       //echo "x: ".$x."  y: ".$y."<br />";

       $w_val[$n_wh] = $w_temp;
       $h_val[$n_wh] = $h_temp;

       $wh_livello[$n_wh] = $livello;
    
       $n_wh += 1;
     }
    }
    

    /////// gestione IMEGE ///////////
    if(preg_match("/(.*<image .*)/",$riga)){
        if(preg_match("/(.*href=.*)/",$riga)){
          $href = preg_replace("/(.*href=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$href)){
                 $href = preg_replace("/(.*)(\".*)/","\$1",$href);
          }
          $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
          $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);
        }

        if(substr($href,1,4) != "http"){
          $href = $url_base.$href;  
        }

        $estensione = preg_replace("/(.*)(\.)(.*)/","\$3",$href); 
        
        // NB: x e y sono gestiti separatamente come translate (vale per tutti gli elementi diversi dalle figure
        //     predefinite)
        $x = 0; $y = 0;
        
        // variabile WIDTH        
        if(preg_match("/(.* width=.*)/",$riga)){
            $iw = preg_replace("/(.* width=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$iw)){
                $iw = preg_replace("/(.*)(\".*)/","\$1",$iw);
            }
            $iw = converti($iw, $font_size[$n_font - 1], $perc_x);
        }
        else{
            $iw = $w;
        }
        
        // variabile HEIGHT
        if(preg_match("/(.* height=.*)/",$riga)){
           $ih = preg_replace("/(.* height=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$ih)){
                $ih = preg_replace("/(.*)(\".*)/","\$1",$ih);
           }
           $ih = converti($ih, $font_size[$n_font - 1], $perc_y);
        }
        else{
            $ih = $h;
        }
               
        conversione(&$tr_x, &$tr_y, &$sc_x, &$sc_y, &$vb_x, &$vb_y,
                    $n_transform, $tipo_transform, $translate_x, $translate_y, $translate_livello, 
                    $scale_x, $scale_y, $n_viewbox, $viewbox_x, $viewbox_y,$viewbox_livello, 
                    $w, $h, $n_wh, $wh_livello, $w_val, $h_val);

        $x = ($x * $vb_x) + $tr_x;
        $y = ($y * $vb_y) + $tr_y;
        
        $ih = $ih * $vb_y * $sc_x;
        $iw = $iw * $vb_x * $sc_y;
        
        // in base all'estensione dell'immagine si decide se inserirla oppure no
        //      formati supportati: jpeg, gif e png
        $crea = 1;
        
        if(($estensione == "jpeg") or ($estensione == "jpg")){
              $file_img = @fopen($href, "r");
              if($file_img){ 
                @fclose($file_img);
                $img = imagecreatefromjpeg($href);
              }
              else{
                $crea = 0;
              }
        }
        elseif($estensione == "png"){
              $file_img = @fopen($href, "r");
              if($file_img){ 
                @fclose($file_img);
                $img = imagecreatefrompng($href);
              }
              else{
                $crea = 0;
              }
        }
        elseif($estensione == "gif"){
              $file_img = @fopen($href, "r");
              if($file_img){ 
                @fclose($file_img);
                $img = imagecreatefromgif($href);
              }
              else{
                $crea = 0;
              }
        }
        else{
             $crea = 0;
        }
        
        // la nuova immagine viene copiata nell'immagine che si sta creando    
        if($crea == 1){   
            $img_w = imagesx($img);
            $img_h = imagesy($img);
            imagecopyresized($image,$img,$x,$y,0,0,$iw,$ih,$img_w,$img_h);
            imagedestroy($img);
        }

    } // fine gestione IMAGE


    /////// gestione USE ///////////
    if(preg_match("/(.*<use .*)/",$riga)){

	// serve per segnalare a che livello mi trovavo con use, in modo da
	//  gestire correttamente l'uscita dagli elementi contenuti in defs
	$curr_livello = $livello;

        if(preg_match("/(.*href=.*)/",$riga)){
          $href = preg_replace("/(.*href=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$href)){
                 $href = preg_replace("/(.*)(\".*)/","\$1",$href);
          }

          $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
          $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);
 	  
          $j = 0;
          while ($j < $n_defs){
            $riga_temp = $contenuto_defs[$j];
            if(preg_match("/(.*id=\"$href\".*)/",$riga_temp)){
            
                $nome_elemento = preg_replace("/(.*<)(\w+)(.*)/","\$2",$riga_temp);
                $nome_elemento = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$nome_elemento);
            
                $fine_defs = 0;
                if(preg_match("/(.*\/>.*)/",$riga_temp)){
                    $fine_defs = 2;
                }

		$no = "no";
                // gestione contenuto defs con id = href
                do{
                    gestione_file($image, $riga_temp, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
                                  $n_translate, $n_scale, $n_rotate, $livello, $contenuto_defs, $n_defs,
                                  $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
                                  $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
                                  $stroke_width, $w, $h, $n_wh, $w_val, $h_val, $translate_livello, $rotate_livello, 
                                  $rotate, $scale_livello, $viewbox_livello, $transform_livello, 
                                  $path_comando, $path_valori, $path_n_valori, $path_n_comandi, 
                                  $wh_livello, $colori, $no, $in_text, $testo, $n_testi, 
                                  $x_text, $y_text, $n_font, $font_s, $font_livello, $url_base, $font_f,
                                  $in_textpath, $text_a); 

                   if($fine_defs == 2){ $fine_defs = 1; $j = $n_defs;}
                   elseif((preg_match("/(.*<\/$nome_elemento>.*)/",$riga_temp)) and ($livello == $curr_livello)){
                    $fine_defs = 1; $j = $n_defs;
                   }    
                   else{
                        $j += 1;
                        $riga_temp = $contenuto_defs[$j];
                   }
                } while($fine_defs != 1);    
            }   
            $j += 1;
          }
        }
	
	
    } // fine gestione USE

    

    /////// gestione PATH ///////////
        // Nota: 
        //  - path_comando[i] contiene il nome dell'i-esimo comando
        //  - path_n_comandi contiene il numero di comandi del path
        //  - path_n_valori[i] contiene il numero di valori dell'i-esimo comando
        //  - path_valori[i][j] contiene il j-esimo valore dell'i-esimo comando
        
    if(preg_match("/(.*<path .*)/",$riga)){
        if(preg_match("/(.*d=.*)/",$riga)){
          $d_temp = preg_replace("/(.*d=\")(.*)(\".*)/","\$2",$riga);
             while(preg_match("/(.*\".*)/",$d_temp)){
                    $d_temp = preg_replace("/(.*)(\".*)/","\$1",$d_temp);
             }
             $d_temp = preg_replace("/(.*?)(zm)(.*)/","\$1z m\$3",$d_temp);

        }
        $elemento_path["n_comandi"] = 0;
        $i = 0;
        
        // cerco nell'attributo d tutti i comandi e i rispettivi valori e li inserisco,
        //      opportunamente divisi, in una struttura
        //
        while(preg_match("/([a-zA-Z])/",$d_temp)){
           $elemento_path["comando"][$i] = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$2",$d_temp);
           $elemento_path["comando"][$i] = preg_replace("/\s/","",$elemento_path["comando"][$i]);
           $d_temp = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$3",$d_temp);
           $val_temp = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$1",$d_temp);
           $j = 0;
           while(preg_match("/(\d)/",$val_temp)){
            $elemento_path["valori"][$i][$j] = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$2",
                                               $val_temp);
            $elemento_path["valori"][$i][$j] = preg_replace("/(.*)(\s)(.*)/","\$1\$3",
                                               $elemento_path["valori"][$i][$j]);

            $val_temp  = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$3",$val_temp);
            $j += 1;
           }
           $elemento_path["n_valori"][$i] = $j;
    
           $i += 1;
        }

        $elemento_path["n_comandi"] = $i;

        conversione(&$tr_x, &$tr_y, &$sc_x, &$sc_y, &$vb_x, &$vb_y,
                    $n_transform, $tipo_transform, $translate_x, $translate_y, $translate_livello, 
                    $scale_x, $scale_y,
                    $n_viewbox, $viewbox_x, $viewbox_y,$viewbox_livello, 
                    $w, $h, $n_wh, $wh_livello, $w_val, $h_val);

        // DEBUG
        // echo "x: ".$tr_x."<br />y: ".$tr_y."<br />sc: ".$sc_x,",".$sc_y;
        // echo "<br />vb_x:  ".$vb_x."<br />vb_y: ".$vb_y."<br />--------------<br />";


        // FILL //////////
        if(preg_match("/(.* fill=\"none\".*)/",$riga)){
           $color_fill = "none";
        }
        elseif(preg_match("/(.* fill=\"url.*)/",$riga)){
           $color_fill = "gray";
        }
        else{
            if(preg_match("/(.* fill=.*)/",$riga)){
                $color_fill = preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
                while(preg_match("/(.*\".*)/",$color_fill)){
                    $color_fill = preg_replace("/(.*)(\".*)/","\$1",$color_fill);          
                } 
                $color_fill = preg_replace("/\s/","",$color_fill);
            }
           else{
              // da cercare fill negli elementi precedenti
              $color_fill = $fill[$n_fill - 1];    
           }
        }
        
        // STROKE //////////////////////////
        if(preg_match("/(.* stroke=\"none\".*)/",$riga)){
          $color_stroke = "none";
        }
        elseif(preg_match("/(.* stroke=\"url.*)/",$riga)){
          $color_stroke = "gray";
        }
        else{
            if(preg_match("/(.* stroke=.*)/",$riga)){
                $color_stroke = preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga);
                while(preg_match("/(.*\".*)/",$color_stroke)){
                    $color_stroke = preg_replace("/(.*)(\".*)/","\$1",$color_stroke);            
                }
                $color_stroke = preg_replace("/\s/","",$color_stroke);
            }
            else{
                // da cercare stroke negli elementi precedenti
                $color_stroke = $stroke[$n_stroke - 1];
            }          
        }
        
        // STROKE-WIDTH ///////////////////////
        if(preg_match("/(.* stroke-width=.*)/",$riga)){
           $stroke_w = preg_replace("/(.* stroke-width=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$stroke_w)){
              $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);          
           }
           $stroke_w = preg_replace("/\s/","",$stroke_w);
        }
        else{
           // da cercare stroke negli elementi precedenti
           $stroke_w = $stroke_width["valore"][$stroke_width["n"] - 1];
           $stroke_w = converti($stroke_w, $font_size[$n_font - 1], $perc_x);
        }
        $stroke_w *= $vb_x * $sc_x;


        $elemento_path["fill"] = $color_fill;
        $elemento_path["stroke"] = $color_stroke;
        $elemento_path["stroke_w"] = $stroke_w;

        visualizza_path($image,$elemento_path, $tr_x, $tr_y, $sc_x, $sc_y, $vb_x, $vb_y, $colori);  

    }// fine gestione PATH

    

    /// NB: da fare come ultima operazione alla fine del FOREACH
    /// Decremento il livello dei TAG ///
    /// svuoto dalle pile tutti gli elementi memorizzati nel livello attuale
    
    if((preg_match("/(.*<\/.*)/",$riga)) or(preg_match("/(.*\/>.*)/",$riga))){
        // controllo viewBox
        while(($n_viewbox > 0) and ($viewbox_livello[$n_viewbox - 1] == $livello)){
          $n_viewbox -= 1;
        }
        while(($n_transform > 0) and ($transform_livello[$n_transform - 1] == $livello)){
          $n_transform -= 1;
        }
        while(($n_translate > 0) and ($translate_livello[$n_translate - 1] == $livello)){
          $n_translate -= 1;
        }
        while(($n_rotate > 0) and ($rotate_livello[$n_rotate - 1] == $livello)){
          $n_rotate -= 1;
        }
        while(($n_scale > 0) and ($scale_livello[$n_scale - 1] == $livello)){
          $n_scale -= 1;
        }
        while(($n_stroke > 1) and ($stroke_livello[$n_stroke - 1] == $livello)){
          $n_stroke -= 1;
        }
        while(($stroke_width["n"] > 1) and ($stroke_width["livello"][$stroke_width["n"] - 1] == $livello)){
          $stroke_width["n"] -= 1;
        }
        while(($n_fill > 1) and ($fill_livello[$n_fill - 1] == $livello)){
          $n_fill -= 1;
        }
        while(($n_font > 0) and ($font_livello[$n_font - 1] == $livello)){
          $n_font -= 1;
        }   
        while(($font_f["n"] > 0) and ($font_f[$font_f["n"] - 1]["livello"] == $livello)){
          $font_f["n"] -= 1;
        }
        while(($text_a["n"] > 0) and ($text_a[$text_a["n"] - 1]["livello"] == $livello)){
          $text_a["n"] -= 1;
        }
        while(($n_wh > 0) and ($wh_livello[$n_wh - 1] == $livello)){
          $n_wh -= 1;
        }

        $livello -= 1;
        
    }

  }// fine funzione gestione_file 
            
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////FINE DICHIARAZIONE FUNZIONI ///////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function svgToGif($file)
{

  // risoluzione dello schermo
  $schermo_x = 750;
  $schermo_y = 400;
  
  /* MOD */
  if ($_SERVER['argc'] == 0)
  {
  	//nome del file gif
	//$nome=$_GET["name"];
  
	$nome = $_FILES["file_htm"]["tmp_name"];	
  }  
  else
	$nome = $file;

  /* fine MOD */

  $url_base = "";
  $url_css = "";

  /*
  if(preg_match("/(\/)/",$nome)){
    $url_base = preg_replace("/(.*)(\/)(.*)/","\$1\$2",$nome);
  }
*/
  
  //$file = @fopen($nome, "r");
  if(true){ 
    //@fclose($file);
        $tutte_le_righe = file($nome); 



   ///////////////////////////////////////////////
   // creazione di un file con un tag per riga ///
   ///////////////////////////////////////////////
   $new_svg = fopen('temp.svg',"w");

	$tutto_il_file = NULL;
     
	foreach ($tutte_le_righe as $riga){

      // gestione css esterno
      if(preg_match("/(.*<?xml-stylesheet.* href=.*? type=\"text\/css\".*)/",$riga)){
        $url_css = preg_replace("/(.*<?xml-stylesheet.*?)(href=\")(.*?)(\")(.*)/","\$3",$riga);
        if(preg_match("/(.*\.css.*)/",$url_css)){
            $url_css = preg_replace("/(.*?)(\.css)(.*)/","\$1\$2",$url_css);
        }
        $url_css = preg_replace("/\s/","",$url_css);
      }
       
      $tutto_il_file .= preg_replace("/\n/"," ",$riga);
   }
 

   $tutto_il_file = preg_replace("/</","\n<",$tutto_il_file);
   $tutto_il_file = preg_replace("/\t/"," ",$tutto_il_file);
   $tutto_il_file = preg_replace("/.*<!--.*-->.*/","",$tutto_il_file);  
   $tutto_il_file = preg_replace("/.*<\?.*\?>.*/","",$tutto_il_file);
   $tutto_il_file = preg_replace("/(.*)(transform=\")(.*)/","\$1transform=\" \$3",$tutto_il_file);
   
   // togliamo i namespace!!
   $tutto_il_file = preg_replace("/(.*)(<\S*:)(.*)/","\$1<\$3",$tutto_il_file);
   $tutto_il_file = preg_replace("/(.*)(<\/\S*:)(.*)/","\$1<\/\$3",$tutto_il_file);

   fwrite($new_svg,$tutto_il_file);
   fclose($new_svg);

  
   //////////////////////
   // gestione Entita' //
   //////////////////////

   // cerco e gestisco tutte le entita' e elimino i tag doctype
   $tutte_le_righe = file('temp.svg'); 
   $tutto_il_file = "";

   $in_doctype = 0;
   $n_entita = 0;
   
   foreach ($tutte_le_righe as $riga){

    //////////////////// RICERCA ENTITA /////////////////////////////
    if(preg_match("/(.*<!D.*>.*)/",$riga)){
      $in_doctype = 0;
      $riga = "";
      // doctype e' su una riga sola, quindi non ha entita'
    }
    elseif(preg_match("/(.*<!D.*)/",$riga)){
      $in_doctype = 1;
      $in_entity = 0;
    }

    if($in_doctype == 1){
     $riga_temp = $riga;
     
     if($in_entity == 1){
        if(preg_match("/(.*\".*)/",$riga_temp)){
        $valore = preg_replace("/(.*?)(\")(.*)/","\$1",$riga_temp);
        }
        else{
         $valore = $riga_temp;
        }
        $valore = preg_replace("/'/","\"",$valore); 

        // DEBUG
        //echo "-".$valore."-<br />\n";

        $entita[$n_entita]["valore"] .= $valore;
       
        if(preg_match("/(.*\".*)/",$riga_temp)){
            $in_entity = 0;
            $n_entita += 1;
        }       
     }
     
     if(preg_match("/(.*<!ENTITY.*)/",$riga_temp)){
        $in_entity = 1;
        
        $nome = preg_replace("/(.*<!ENTITY)(.*?)(\".*)/","\$2",$riga_temp);
        $nome = preg_replace("/\s/","",$nome);
       
        if(preg_match("/(.*\".*\".*)/",$riga_temp)){
            $valore = preg_replace("/(.*?)(\")(.*)(\")(.*)/","\$3",$riga_temp);
            $valore = preg_replace("/\s/","",$valore);
        }
        else{
            $valore = preg_replace("/(.*?)(\")(.*)/","\$3",$riga_temp);
        }
        $valore = preg_replace("/'/","\"",$valore);
  
        $entita[$n_entita]["nome"] = $nome;
        $entita[$n_entita]["valore"] = $valore;
     
        if(preg_match("/(.*\".*\">.*)/",$riga_temp)){
            $in_entity = 0;
            $n_entita += 1;
        }
     }

      if(preg_match("/(.*\].*>)/",$riga)){
         $in_doctype = 0;
      }
      
      $riga = "";   
    } // fine in doctype
    
    //////////////////////// SOSTITUZIONE ENTITA //////////////////////////
    else{ 
     
      $n_val = 0;
      while(preg_match("/(.*&.*;.*)/",$riga)){
         $nome_entita = preg_replace("/(.*?)(&)(.*?)(;)(.*)/","\$3",$riga);
         $nome_entita = preg_replace("/\s/","",$nome_entita);


         $k = 0;
         $valore = "";
         while($k < $n_entita){
            if($entita[$k]["nome"] == $nome_entita){
                $valore = $entita[$k]["valore"];
                $k = $n_entita;
            }
            $k += 1;
         }
        
         $valore_da_sostituire[$n_val] = $valore;
         if($valore != ""){
             $stringa = "XX-VAL".$n_val;
             $n_val += 1;
         }

         $riga = preg_replace("/(.*?)(&)($nome_entita)(;)(.*)/","\$1 $stringa \$5",$riga);

         // DEBUG
         //echo $nome_entita."-<br />";

      }
      $k = 0;
      while($k < $n_val){   
        $riga = preg_replace("/(.*?)(XX-VAL$k)(.*)/","\$1 $valore_da_sostituire[$k] \$3",$riga);        
        $k += 1;
      }
      
    }
    $tutto_il_file .=  $riga;
   }

   $new_svg = fopen('temp.svg',"w");
   fwrite($new_svg,$tutto_il_file);
   fclose($new_svg);
   //////////////////////////
   // fine gestione entita //
   //////////////////////////

   

   //////////////////////////
   // gestione CSS esterno //
   //////////////////////////
   
   if($url_css != ""){
   
     if(preg_match("/http/",$url_css)){
         $url_completo = $url_css;
     }
     else{
         $url_completo = $url_base.$url_css;
     }
     $nuovo_style = "<style type=\"text/css\">\n<![CDATA[\n ";
     
     $file_css = file($url_completo); 
    
     foreach ($file_css as $riga_css){
        $contenuto = preg_replace("/\n/"," ",$riga_css);
        $nuovo_style .= $contenuto;
     }

     $nuovo_style .="]]>\n</style>\n";
     
     $tutte_le_righe = file('temp.svg'); 
     $tutto_il_file = $nuovo_style;
    
     foreach ($tutte_le_righe as $riga){
        $tutto_il_file .=  $riga;
     }

     $new_svg = fopen('temp.svg',"w");
     fwrite($new_svg,$tutto_il_file);
     fclose($new_svg);
   }

   ///////////////////////////////
   // fine gestione CSS esterno //
   //////////////////////////////

  
   ////////////////////
   // gestione style //
   ////////////////////
   
   // gestiamo class e id (problemi se style viene definito dopo un elemento che lo richiama)
   $tutte_le_righe = file('temp.svg'); 
   $tutto_il_file = "";

   $n_class = 0;
   $in_style = 0;
   $n_id = 0;
   $n_nome = 0;
   
   foreach ($tutte_le_righe as $riga){
  
    if(preg_match("/(.*<style.* type=\"text\/css\".*)/",$riga)){
      $in_style = 1;
    }
    if($in_style == 1){
      $riga_style = $riga;  
      // gestione class .nome{}
      while(preg_match("/(\.)/",$riga_style)){
        $class_temp = preg_replace("/(.*?)(\.)(.*?)(})(.*)/","\$2\$3\$4",$riga_style);
        $riga_style = preg_replace("/(.*?)(\.)(.*?)(})(.*)/","\$5",$riga_style);
        
        $nome_class[$n_class] = preg_replace("/(.*?)(\.)(.*?)({)(.*)/","\$3",$class_temp);
        $nome_class[$n_class] = preg_replace("/\s/","",$nome_class[$n_class]);

        $valore_temp = preg_replace("/(.*?)({)(.*)(})(.*)/","\$3\$4",$class_temp);
        $valore_temp = preg_replace("/\s/","",$valore_temp);

        // DEBUG
        //echo "NOME:".$nome_class[$n_class]."-- VALORE:".$valore_class[$n_class]."<br />";
        $valore_class[$n_class] = "";
        while(preg_match("/}/",$valore_temp)){
            $new_nome = preg_replace("/(.*?)(:)(.*)/","\$1",$valore_temp);
            $new_nome = preg_replace("/\s/","",$new_nome);

            if(preg_match("/;/",$valore_temp)){
                $new_valore = preg_replace("/(.*?)(:)(.*?)(;)(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
            else{
                $new_valore = preg_replace("/(.*?)(:)(.*?)(})(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
        
            $valore_class[$n_class] .= $new_nome."=\"".$new_valore."\" ";

            if(preg_match("/;/",$valore_temp)){
                $valore_temp = preg_replace("/(.*?)(;)(.*)/","\$3",$valore_temp);
            }
            else{
                $valore_temp = preg_replace("/(.*?)(})(.*)/","\$3",$valore_temp);
            }
        }
        $n_class += 1;  
      }       
      
      $riga_style = $riga;  
      // gestione id #nome {}
      while(preg_match("/(#)/",$riga_style)){
        $id_temp = preg_replace("/(.*?)(#)(.*?)(})(.*)/","\$2\$3\$4",$riga_style);
        $riga_style = preg_replace("/(.*?)(#)(.*?)(})(.*)/","\$5",$riga_style);
        
        $nome_id[$n_id] = preg_replace("/(.*?)(#)(.*?)({)(.*)/","\$3",$id_temp);
        $nome_id[$n_id] = preg_replace("/\s/","",$nome_id[$n_id]);

        $valore_temp = preg_replace("/(.*?)({)(.*)(})(.*)/","\$3\$4",$id_temp);
        $valore_temp = preg_replace("/\s/","",$valore_temp);

        $valore_id[$n_id] = "";
        while(preg_match("/}/",$valore_temp)){
            $new_nome = preg_replace("/(.*?)(:)(.*)/","\$1",$valore_temp);
            $new_nome = preg_replace("/\s/","",$new_nome);

            if(preg_match("/;/",$valore_temp)){
                $new_valore = preg_replace("/(.*?)(:)(.*?)(;)(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
            else{
                $new_valore = preg_replace("/(.*?)(:)(.*?)(})(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
    
            $valore_id[$n_id] .= $new_nome."=\"".$new_valore."\" ";

            if(preg_match("/;/",$valore_temp)){
              $valore_temp = preg_replace("/(.*?)(;)(.*)/","\$3",$valore_temp);
            }
            else{
                $valore_temp = preg_replace("/(.*?)(})(.*)/","\$3",$valore_temp);
            }
        }       
        $n_id += 1;
      }       

      ///////////////////////////// gestione style riferito a nome elemento (gestione elemento style)
      
      $riga_style = $riga;  
      // gestione nome_elemento {}
      while(preg_match("/(\s+\w+\s?{)/",$riga_style)){
        $nome_temp = preg_replace("/(.*?)(\s+)(\w+)(\s?)({)(.*?)(})(.*)/","\$3\$4\$5\$6\$7",$riga_style);
        $riga_style = preg_replace("/(.*?)(\s+)(\w+)(\s?)({)(.*?)(})(.*)/","\$8",$riga_style);
                       
        $nome_nome[$n_nome] = preg_replace("/(.*?)(\w+)(\s?)({)(.*)/","\$2",$nome_temp);
        $nome_nome[$n_nome] = preg_replace("/\s/","",$nome_nome[$n_nome]);

        $valore_temp = preg_replace("/(.*?)({)(.*)(})(.*)/","\$3\$4",$nome_temp);
        $valore_temp = preg_replace("/\s/","",$valore_temp);

        $valore_nome[$n_nome] = "";
                
        while(preg_match("/}/",$valore_temp)){
            $new_nome = preg_replace("/(.*?)(:)(.*)/","\$1",$valore_temp);
            $new_nome = preg_replace("/\s/","",$new_nome);

            if(preg_match("/;/",$valore_temp)){
                $new_valore = preg_replace("/(.*?)(:)(.*?)(;)(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
            else{
                $new_valore = preg_replace("/(.*?)(:)(.*?)(})(.*)/","\$3",$valore_temp);
                $new_valore = preg_replace("/\s/","",$new_valore);
            }
        
            $valore_nome[$n_nome] .= $new_nome."=\"".$new_valore."\" ";
        
            if(preg_match("/;/",$valore_temp)){
                $valore_temp = preg_replace("/(.*?)(;)(.*)/","\$3",$valore_temp);
            }
            else{
                $valore_temp = preg_replace("/(.*?)(})(.*)/","\$3",$valore_temp);
            }
        }        
        $n_nome += 1;
      }       
      
      ///////////////////////////// fine gestione style riferito a nome elemento (gestione elemento style)

    } // fine in style  
    
    if(preg_match("/(.*<\/style.*)/",$riga)){
      $in_style = 0;
    }

    ///////////////////////////////////////////// gestione style riferito a nome_elemento
    $nn = 0;
    while($nn < $n_nome){
       if(preg_match("/(.*<$nome_nome[$nn] .*)/",$riga)){
            $riga = preg_replace("/(.*<$nome_nome[$nn] )(.*)/","\$1 $valore_nome[$nn] \$2",$riga);
       }        
       $nn += 1;    
    }
    ////////////////////////////////////////////// fine gestione style riferito a nome_elemento
    
    if(preg_match("/(.* class=.*)/",$riga)){
       // sostituzione  
       $class = preg_replace("/(.* class=\")(.*)(\".*)/","\$2",$riga);
       while(preg_match("/(.*\".*)/",$class)){
              $class = preg_replace("/(.*)(\".*)/","\$1",$class);
       }
       
       $len = strlen($class);
       $class = substr($class,0,$len - 1);     
       $nomi_class = 0;
       $class_temp = $class;
       $valori_da_sostituire = "";
       
       while(preg_match("/(.*)(\s+)(.*)/",$class_temp)){
           $class_corr = preg_replace("/(.*?)(\s+)(.*)/","\$1",$class_temp);
           $class_temp = preg_replace("/(.*?)(\s+)(.*)/","\$3",$class_temp);
           $j = 0;
           while($j < $n_class){
              if($class_corr == $nome_class[$j]){
                 $valori_da_sostituire .=  $valore_class[$j]." ";
                $nomi_class += 1;
              }
              $j += 1;
          }
      }
      $j = 0;
      while($j < $n_class){
        if($class_temp == $nome_class[$j]){
            $valori_da_sostituire .=  $valore_class[$j]." ";
            $nomi_class += 1;
        }
        $j += 1;
      }
      
      if($nomi_class > 0){
        $riga = preg_replace("/(.*)( class=\"$class\")(.*)/","\$1 $valori_da_sostituire \$3",$riga);
      }

    } // fine if preg_match class

    if(preg_match("/(.* id=.*)/",$riga)){
       // sostituzione  
       $id = preg_replace("/(.* id=\")(.*)(\".*)/","\$2",$riga);
       while(preg_match("/(.*\".*)/",$id)){
              $id = preg_replace("/(.*)(\".*)/","\$1",$id);
       }
       $id = preg_replace("/\s/","",$id);
       
       $j = 0;
       $sostituisci_id = 0;
       while($j < $n_id){
          if($id == $nome_id[$j]){
            $valore_da_sostituire .=  $valore_id[$j]." ";
            $sostituisci_id = 1;
          }
          $j += 1;
       }

       if($sostituisci_id > 0){
        $riga = preg_replace("/(.*)( id=\"$id\" )(.*)/","\$1 \$2 $valore_da_sostituire \$3",$riga);
       }

    } // fine if preg_match id

    $tutto_il_file .=  $riga;
   }

 
   $new_svg = fopen('temp.svg',"w");
   fwrite($new_svg,$tutto_il_file);
   fclose($new_svg);
   /////////////////////////
   // fine gestione style //
   /////////////////////////


   ///////////////////////////////////////
   // gestione attributo style + colori //
   ///////////////////////////////////////
   
   $tutte_le_righe = file('temp.svg'); 
   $tutto_il_file = "";

   $n_colori = 0;

   foreach ($tutte_le_righe as $riga){
    
    if(preg_match("/(.* style=\".*)/",$riga)){
       $contenuto_style = preg_replace("/(.* style=\")(.*)(\".*)/","\$2",$riga);
       while(preg_match("/(.*\".*)/",$contenuto_style)){
          $contenuto_style = preg_replace("/(.*)(\".*)/","\$1",$contenuto_style); 
       } 
       $contenuto_style = preg_replace("/\s/","",$contenuto_style);
    
       $n_attr = 0;
       while(preg_match("/(.*;.*)/",$contenuto_style)){
            $att_temp = preg_replace("/(.*?)(;)(.*)/","\$1",$contenuto_style); 
            $style["nome_att"][$n_attr] = preg_replace("/(.*?)(:)(.*)/","\$1",$att_temp); 
            $style["valore_att"][$n_attr] = preg_replace("/(.*?)(:)(.*)/","\$3",$att_temp); 
            $n_attr += 1;
            $contenuto_style = preg_replace("/(.*?)(;)(.*)/","\$3",$contenuto_style); 
       }
       if(preg_match("/(.*:.*)/",$contenuto_style)){
            $style["nome_att"][$n_attr] = preg_replace("/(.*?)(:)(.*)/","\$1",$contenuto_style); 
            $style["valore_att"][$n_attr] = preg_replace("/(.*?)(:)(.*)/","\$3",$contenuto_style); 
            $n_attr += 1;
       }

       $j = 0;
       $stringa_style = " ";
       while($j < $n_attr){
            $stringa_style .=$style["nome_att"][$j]."=\"".$style["valore_att"][$j]."\" ";
            $j += 1;
       }
       $stringa_style .= " ";
        
       $riga = preg_replace("/(.*)( style=\".*?\")(.*)/","\$1$stringa_style\$3",$riga);
    }     

    /////////////////////////////////////////////////////////////////
    ///////////////// GESTIONE COLORI ///////////////////////////////
    $riga_colori = $riga;
    
    if(preg_match("/(.* fill=.*)/",$riga_colori)){
       $colore = preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga_colori);
       while(preg_match("/(.*\".*)/",$colore)){
            $colore = preg_replace("/(.*)(\".*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);
  
       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori){
           if($valore_colori[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori;      
            }
            $j += 1;
         }
         
         if($new_color == 1){
            $valore_colori[$n_colori] = $colore;
            $n_colori += 1;
         }
       }
    }

    if(preg_match("/(.*stroke=.*)/",$riga_colori)){
       $colore = preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga_colori);
       while(preg_match("/(.*\".*)/",$colore)){
            $colore = preg_replace("/(.*)(\".*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);

       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori){
             if($valore_colori[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori;      
             }
             $j += 1;
         }
         if($new_color == 1){
            $valore_colori[$n_colori] = $colore;
            $n_colori += 1;
         }
      }          
    }
    ///////////////// FINE GESTIONE COLORI ////////////////////////////
    //////////////////////////////////////////////////////////////////
      
    $tutto_il_file .=  $riga;
   }

   $new_svg = fopen('temp.svg',"w");
   fwrite($new_svg,$tutto_il_file);
   fclose($new_svg);
   ////////////////////////////////////////////
   // fine gestione attributo style + colori //
   ///////////////////////////////////////////

    
   ///////////////////////////////////////////
   // adesso gestiamo il nuovo file creato ///
   ///////////////////////////////////////////
   
   $tutte_le_righe = file('temp.svg'); 

   // usati per segnalare se ho incontrato il primo elemento svg,
   //   serve per impostare le dimensioni dell'immagine
   $svg_start = 1;
   $primo_svg = "no"; 
   
   // azzero le pile ed il livello degli elementi
   $n_viewbox = 0;
   $n_transform = 0;
   $n_translate = 0;
   $n_rotate = 0;
   $n_scale = 0;
   $livello = 0;

   // imposto le pile per fill e stroke   
   $n_fill = 1;
   $n_stroke = 1;
   $stroke_width["n"] = 1;
   
   $fill[0] = "black";
   $stroke[0] = "none";
   $fill_livello[0] = "-1";
   $stroke_width["livello"][0] = "-1";
   $stroke_width["valore"][0]  = "1";
   $stroke_livello[0] = "-1";
   
   // variabili per la gestione dei commenti e di defs
   $comment = 0;
   $desc = 0;
   $in_defs = 0;
   $n_defs = 0; 
   
   $in_text = 0;
   //// Gestione Testi //
   // $n_testi: contiene il numero di testi (+1)
   // $testo[$j]["xxxx"]:   - $j: indice del testo 
   //               - xxxx: - "tipo": tipo del testo (text, tspan, tref, textPath)
   //                   - "valore": valore della parte di testo
   //                   - "x": valore di x presente nel tag (se non c'e' vale "none"). Solo 
   //                           per elementi diversi da text
   //                   - "y": stessa cosa
   //                   - "font": valore del font (se non e' presente vale "none", per gli 
   //                           elementi diversi da text. Comunque verra' impostato 
   //                           col valore del font da usare
   //                   - "colore": colore del testo (come per font...)
   //                   - "inc-x": incremento di x (solo per elementi diversi da text), se non
   //                           e' presente viene impostato a 0
   //                   - "inc-y": stessa cosa
   //                   - "x-val": valore effettivo delle x della parte di testo da visualizzare
   //                           viene calcolato sommando le lunghezze delle porzioni di
   //                           testo precedente e eventuali aggiustamenti
   //                   - "y-val": stessa cosa
   //
   
   $testo[0]["valore"] ="";
   $n_testi = 0;
   
   $n_wh = 0;

   $n_font = 1;
   $font_size[0] = 12;
   $font_livello[0] = -1;
   
   $font_f["n"] = 1;
   $font_f[0]["valore"]  = "Arial"; // valore di default ??
   $font_f[0]["livello"] = 0;

   $text_a["n"] = 1;
   $text_a[0]["valore"]  = "left";
   $text_a[0]["livello"] = 0;

   $in_textpath = "false";
  
  
  /////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////
  /////////////////// GESTIONE DEL DOCUMENTO ////////////////////////////
  /////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////
   foreach ($tutte_le_righe as $riga){
   
     if(preg_match("/(.*<!--.*)/",$riga)){
      $comment = 1;
     }
     if(preg_match("/(.*<desc.*)/",$riga)){
      $desc = 1;
     }
     if(preg_match("/(.*<desc.*\/>)/",$riga)){
      $desc = 0;
     }
     if(preg_match("/(.*<defs.*)/",$riga)){
      $in_defs = 1;
     }
     if(preg_match("/(.*<defs.*\/>.*)/",$riga)){
      $in_defs = 0;
     }

     // gestione degli elementi   
     if(($comment == 0) and ($in_defs == 0) and ($desc == 0)){
     
     
        //////////////////////////////
        //////////////////////////////
        // qui imposto w e h /////////
        /////////////////////////////
        // sono nel tag svg, il primo
	     if (($svg_start == 1) and (preg_match("/(.*<svg.*)/",$riga)))
	     {
            $svg_start = 0;
            $primo_svg = "si";

            $font = 12;
            if(preg_match("/(.* font-size=.*)/",$riga)){
              $font = preg_replace("/(.* font_size=\")(.*)(\".*)/","\$2",$riga);
              while(preg_match("/(.*\".*)/",$font)){
                  $font = preg_replace("/(.*)(\".*)/","\$1",$font);
              }
              $font = preg_replace("/\s/","",$font);
            }

            // imposto width e height
            if(preg_match("/(.* width.*)/",$riga)){
               $w = preg_replace("/(.* width=\")(.*)(\".*)/","\$2",$riga);
               while(preg_match("/(.*\".*)/",$w)){
                     $w = preg_replace("/(.*)(\".*)/","\$1",$w);
               }
               // w necessita di una conversione in pixel!!
               $w = preg_replace("/\s/","",$w);
               $w = converti($w,$font,$schermo_x); 
            }
            else{
                $w = $schermo_x;
            }

            if(preg_match("/(.* height.*)/",$riga)){
                $h = preg_replace("/(.* height=\")(.*)(\".*)/","\$2",$riga);
                while(preg_match("/(.*\".*)/",$h)){
                  $h = preg_replace("/(.*)(\".*)/","\$1",$h);
                }
                // h necessita di una conversione in pixel!!
                $h = preg_replace("/\s/","",$h);
                $h = converti($h,$font,$schermo_y);
            }
            else{
                $h = $schermo_y;  
            }

            $ov = "none";
            if(preg_match("/(.* overflow=.*)/",$riga)){
                $ov = preg_replace("/(.* overflow=\")(.*)(\".*)/","\$2",$riga);
                while(preg_match("/(.*\".*)/",$ov)){
                  $ov = preg_replace("/(.*)(\".*)/","\$1",$ov);
                }
                $ov = preg_replace("/\s/","",$ov);
            }

            //////////////////////////////////
            // creazione dell'immagine GIF ///
            /////////////////////////////////

            if($ov == "visible"){
                $image = imagecreate($schermo_x + 200,$schermo_y + 200);
            }
            else{
                $image = imagecreate(ceil($w) + 1, ceil($h) + 1); 
                // +1, se non c'e' non vengono visualizzati i bordi delle 
                // immagini grandi quanto la figura
            }

            // alloco tutti i colori che mi serviranno
            $colori =  carica_colori($image, $n_colori, $valore_colori);

            imagefill($image,0,0,$colori["white"]);
            // fine creazione immagine //
     
        } // fine if, primo svg
    
    
        gestione_file($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $n_transform, 
                      $n_translate, $n_scale, $n_rotate, $livello, $contenuto_defs, $n_defs,
                      $translate_x, $translate_y, $scale_x, $scale_y, $tipo_transform, 
                      $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello, 
                      $stroke_width, $w, $h, $n_wh, $w_val, $h_val, $translate_livello, 
                      $rotate_livello, $rotate, $scale_livello, $viewbox_livello, $transform_livello, 
                      $wh_livello, $colori, $primo_svg, $in_text, $testo, $n_testi,$path_comando, 
                      $path_valori, $path_n_valori, $path_n_comandi, $x_text, $y_text, $n_font, 
                      $font_size, $font_livello, $url_base, $font_f, $in_textpath, $text_a);

        if($primo_svg == "si"){ $primo_svg = "no"; }
     } // fine if comment == 0 and in_defs == 0 
     elseif(($comment == 0) and ($in_defs == 1)){
        $contenuto_defs[$n_defs] = $riga;
        $n_defs += 1;}
     if(preg_match("/(.*-->.*)/",$riga)){
	     $comment = 0;}
     if(preg_match("/(.*<\/desc.*)/",$riga)){
        $desc = 0;}
     if(preg_match("/(.*<\/defs>.*)/",$riga)){
        $in_defs = 0;}} // fine FOREACH piu' esterno
   
   
   // Per l'eventuale creazioni di un file gif
   //$image_gif = imagegif($image,'prova.gif');


   ////////////////////////////////////////////
   // VISUALIZZAZIONE IMMAGINE ////////////////
   ////////////////////////////////////////////
   
   if ($_SERVER['argc'] == 0) 
   {
   	@header('Content-type: image/gif'); 
   	imagegif($image); 
   	imagedestroy($image); 
   } 
   else
	return $image;
  }
  else{
	  echo "<html><head><title>Sorry</title></head><body>Spiacenti,";
	  echo "file ".$nome." non trovato</body></html>";
  }
}


if ($_SERVER['argc'] == 0) 
	svgToGif(NULL);

?>  
