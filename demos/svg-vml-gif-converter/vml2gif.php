<?php

set_time_limit (600);
error_reporting(E_ALL ^ E_NOTICE);

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
    // carica tutti i colori_vml che sono utilizzati nel documento (quelli nella forma rgb(x,x,x) e #c1c2c3,
    //      memorizzati nell'array valore_colori_vml.
    //      Inoltre carica i principali colori_vml espressi mediante il nome.
    //      Tutti i colori_vml allocati sono memorizzati nell'array colori_vml da usare per riferirsi ad un
    //          particolare colore.
    //
    function carica_colori_vml(&$image, $n_colori_vml, $valore_colori_vml){
    
      // imposto i colori_vml definiti tramite nome (non sono definiti tutti!)
      $colori_vml["black"]  =  imagecolorallocate($image, 0, 0, 0);
      $colori_vml["aqua"]   =  imagecolorallocate($image, 0, 255, 255);
      $colori_vml["blue"]   =  imagecolorallocate($image, 0,0,255);
      $colori_vml["brown"]  =  imagecolorallocate($image, 165,42,42);
      $colori_vml["gray"]   =  imagecolorallocate($image, 128,128,128);
      $colori_vml["green"]  =  imagecolorallocate($image, 0,128,0);
      $colori_vml["grey"]   =  imagecolorallocate($image, 128,128,128);
      $colori_vml["lime"]   =  imagecolorallocate($image, 0,255,0);
      $colori_vml["maroon"] =  imagecolorallocate($image, 128,0,0);
      $colori_vml["navy"]   =  imagecolorallocate($image, 0,0,128);
      $colori_vml["orange"] =  imagecolorallocate($image, 255,165,0);
      $colori_vml["pink"]   =  imagecolorallocate($image, 255,192,203);
      $colori_vml["purple"] =  imagecolorallocate($image, 128,0,128);
      $colori_vml["red"]    =  imagecolorallocate($image, 255,0,0);
      $colori_vml["silver"] =  imagecolorallocate($image, 192,192,192);
      $colori_vml["yellow"] =  imagecolorallocate($image, 255,255,0);
      $colori_vml["white"]  =  imagecolorallocate($image, 255, 255, 255);

      // imposto tutti i colori_vml utilizzati nel documenti nella 
      //    forma rgb(x,x,x) e #c1c2c3
      $j = 0;
      while($j < $n_colori_vml){
        $colore = $valore_colori_vml[$j];
        if(preg_match("/(.*#.*)/",$colore)){
         $val1 = preg_replace("/(#)(..)(.*)/","\$2",$colore);
         $val2 = preg_replace("/(#..)(..)(.*)/","\$2",$colore);      
         $val3 = preg_replace("/(#....)(..)(.*)/","\$2",$colore);
   
         $val1 = hexdec($val1);
         $val2 = hexdec($val2);
         $val3 = hexdec($val3);
              
         $colori_vml[$colore]  =  imagecolorallocate($image, $val1, $val2, $val3);
        
        }
        elseif(preg_match("/(.*rgb.*)/",$colore)){

         $val1 = preg_replace("/(rgb\()(.*?)(,.*)/","\$2",$colore);
         $val2 = preg_replace("/(rgb\(.*,)(.*)(,.*)/","\$2",$colore);        
         $val3 = preg_replace("/(rgb\(.*,.*,)(.*)(\))/","\$2",$colore);
     
         $colori_vml[$colore]  =  imagecolorallocate($image, $val1, $val2, $val3);
        }
    
        $j += 1;    
      }

      return $colori_vml;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CONVERTI:
    //      converte il valore passato in input (dotato eventualmente di 
    //       unita' di misura) nel corrispondente valore assoluto, cioe'
    //       in pixel (o user unit). 
    //      A questa funzione vengono passati due ulteriori parametri per
    //       calcolare il valore espresso in percentuale o nei casi in cui si
    //       riferisca alla dimensione del font.
    //     NB: da segnalare che VML utilizza due tipi di conversione per i valori, appropriatamente
    //          gestiti mediante l'utilizzo del livello di annidamento (i valori del primo
    //          elemento e quelli dei bordi e dei font, hanno una conversione diversa rispetto
    //          agli altri valori).
    //

    function converti_vml($valore,$font,$perc,$livello){    
       if(preg_match("/(.*cm.*)/",$valore)){
          $valore = preg_replace("/(.*)(cm.*)/","\$1",$valore);
          if($livello == 1){
            $valore = $valore * 35.43307;
          }
          else{
            $valore = $valore * 885;
          }
       }  
       elseif(preg_match("/(.*in.*)/",$valore)){
          $valore = preg_replace("/(.*)(in.*)/","\$1",$valore);
          if($livello == 1){
            $valore = $valore * 96;
          }
          else{
            $valore = $valore * 2300;
          }
       }
       elseif(preg_match("/(.*px.*)/",$valore)){
            $valore = preg_replace("/(.*)(px.*)/","\$1",$valore);
       }
       elseif(preg_match("/(.*pt.*)/",$valore)){
          $valore = preg_replace("/(.*)(pt.*)/","\$1",$valore);
          if($livello == 1){
            $valore = $valore * 1.25;
          }
          else{
            $valore = $valore * 32;
          }
       }
       elseif(preg_match("/(.*mm.*)/",$valore)){
          $valore = preg_replace("/(.*)(mm.*)/","\$1",$valore);
          if($livello == 1){
            $valore = $valore * 3.543307;
          }
          else{
            $valore = $valore * 88.5;
          }
       }
       elseif(preg_match("/(.*pc.*)/",$valore)){
          $valore = preg_replace("/(.*)(pc.*)/","\$1",$valore);
          if($livello == 1){
            $valore = $valore * 15;
          }
          else{
            $valore = $valore * 385;
          }
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
          altri casi (non gestiti):
             * ex
       */   

        return ($valore);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CONVERSIONE:
    // imposta gli attributi di conversione (transalte x e y, viewbox x e y)
    // In base ai valori degli elementi precedenti, valori di dimensionamento (viewbox, width e 
    //      height) e di posizionamento (translate x e y),
    //      calcola i dimensionamenti e gli spostamenti da applicare all'elemento corrente.
    // NB: in VML non sono presenti attributi di trasformazione, le traslazioni si riferiscono agli
    //      eventuali valori di left e top incontrati oppure ai valori di coordorigin
    // NB: in VML non esiste neanche l'attributo viewbox, ma si utilizza coordsize, quindi,
    //      tutte le variabile che si riferiscono a viewbox, in realtà si riferiscono a coordsize      

    // imposta gli attributi di conversione (transalte x e y, viewbox x e y)
    function conversione_vml(&$tr_x, &$tr_y, &$vb_x, &$vb_y, $n_translate, $translate_x, $translate_y, 
                         $n_viewbox, $viewbox_x, $viewbox_y, $w_val, $h_val, $translate_livello, 
                         $viewbox_livello){

      ////////////////////////
      // gestione translate //
      ////////////////////////
      $i = 0;
  
      $tr_x = 0; $tr_y = 0;

    /* DEBUG
     echo $viewbox_x[0]." ".$viewbox_x[1]." ".$viewbox_x[2]."<br />";
     echo $w_val[0]." ".$w_val[1]." ".$w_val[2]."<br />";
     echo $viewbox_livello[0]." ".$viewbox_livello[1]." ".$viewbox_livello[2]."<br />";
    */
      

      while($i < $n_translate){
        // DEBUG
        //echo $translate_x[$i]["valore"]." (".$translate_livello[$i].") -- ";
      
        $tr_x_temp = $translate_x[$i];
        $tr_y_temp = $translate_y[$i];

        $j = 0;

        // DEBUG
        //echo $tr_x_temp." (".$translate_livello[$i].")<br />";

        while(($j < $n_viewbox) and ($viewbox_livello[$j] < $translate_livello[$i])){
            $tr_x_temp *= ($w_val[$j] / $viewbox_x[$j]);
            $tr_y_temp *= ($h_val[$j] / $viewbox_y[$j]);
            $j += 1;
        }           
    
        $tr_x = $tr_x + $tr_x_temp;
        $tr_y = $tr_y + $tr_y_temp;

        $i += 1;
      }
      
      // DEBUG
      //echo "<br />TR_X:  ".$tr_x."<br />";

      ////////////////////////
      // gestione viewbox ////
      ////////////////////////
      $vb = $n_viewbox;
      $vb_x = 1; 
      $vb_y = 1;      
    
      while($vb > 0){
        // in teoria vb e wh vanno di pari passo
        
        // DEBUG    
        //echo "____ ".$w_val[$vb - 1]." -- ".$viewbox_x[$vb - 1]."<br />";
    
          $vb_x *= ($w_val[$vb - 1] / $viewbox_x[$vb - 1]);
          $vb_y *= ($h_val[$vb - 1] / $viewbox_y[$vb - 1]);
          $vb -= 1;
      }
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
    function gestione_angoli_vml($x1,$x2,$y1,$y2,&$seno,&$coseno){
        $mod = 0;
        $lato1 = ($x2 - $x1);
        $lato2 = ($y2 - $y1);
        if((($lato1 < 0) and ($lato2 < 0)) or (($lato1 > 0) and ($lato2 > 0))){
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
    function calcola_inversione_vml($new_x1,$new_x2,$new_y1,$new_y2,
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
    function bezier3_vml($p1,$p2,$p3,$mu){
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
    function cubicbezier_vml($p0,$p1,$p2,$p3,$mu){

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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funzione predef

   // PREDEF:
    //  gestisce le figure predefinite (rect,roundrect, oval, line, curve, arc, polyline, image)
    //    le figure vengono gestite una per volta, in base all'ordine di definizio nell'albero
    //      del documento, i valori realtivi alle loro proprieta' vengono mantenuti in una struttura,
    //      chiamata predef. 
    //    In questa funzione si impostano i valori di questa struttura, cioe' i vari attributi di
    //      posizionamento e dimensionamento e le caratteristiche di fill e stroke
    //
    function predef_vml(&$image, &$riga, &$n_viewbox, &$viewbox_x, &$viewbox_y, 
                    &$n_translate, &$livello, &$translate_x, &$translate_y,
                    &$n_stroke, &$n_fill, &$fill, &$stroke, &$fill_livello, &$stroke_livello,
                    &$w, &$h, &$n_wh, &$w_val, &$h_val, &$translate_livello,
                    &$viewbox_livello, &$wh_livello, $colori_vml, 
                    &$font_temp, &$predef, &$on_predef, &$fill_on, &$stroke_on, &$fill_color, 
                    &$stroke_colori_vml, $perc_x, $perc_y,$livello){

      if(preg_match("/(.* style=.*)/",$riga)){     
         $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*[\"|'].*)/",$style)){
             $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
          }
          $style = " ".$style;

          // gestione WIDTH                 
          $w_temp = 0;
          if(preg_match("/(.*[ |;]width\s?:.*)/",$style)){
             $w_temp = preg_replace("/(.*[ |;]width\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$w_temp)){
                 while(preg_match("/(.*;.*)/",$w_temp)){
                       $w_temp = preg_replace("/(.*?)(;.*)/","\$1",$w_temp);
                  }
             }
         
             $w_temp = preg_replace("/\s/","",$w_temp);
             $w_temp = converti_vml($w_temp,$font_temp,$perc_x,$livello);
          }  

          // gestione HEIGHT
          $h_temp = 0;
          if(preg_match("/(.*[ |;]height\s?:.*)/",$style)){
             $h_temp = preg_replace("/(.*[ |;]height\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$h_temp)){
                 while(preg_match("/(.*;.*)/",$h_temp)){
                       $h_temp = preg_replace("/(.*)(;.*)/","\$1",$h_temp);
                  }
             }
             $h_temp = preg_replace("/\s/","",$h_temp);
             $h_temp = converti_vml($h_temp,$font_tempi,$perc_y, $livello); 
             
          }

          // gestione X (LEFT)
          $x_temp = 0;
          if(preg_match("/(.*[ |;]left\s?:.*)/",$style)){
             $x_temp = preg_replace("/(.*[ |;]left\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$x_temp)){
               while(preg_match("/(.*;.*)/",$x_temp)){
                       $x_temp = preg_replace("/(.*)(;.*)/","\$1",$x_temp);
               }
             }
             $x_temp = preg_replace("/\s/","",$x_temp);
             $x_temp = converti_vml($x_temp,$font_temp,$perc_x, $livello); 
          }  

          // gestione Y (TOP)
          $y_temp = 0;
          if(preg_match("/(.*[ |;]top\s?:.*)/",$style)){
             $y_temp = preg_replace("/(.*[ |;]top\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$y_temp)){
                while(preg_match("/(.*;.*)/",$y_temp)){
                    $y_temp = preg_replace("/(.*)(;.*)/","\$1",$y_temp);
                }
             }
                                 
             $y_temp = preg_replace("/\s/","",$y_temp);
             $y_temp = converti_vml($y_temp,$font_temp,$perc_y, $livello); 
          }        
      }
      // non c'e' style
      else{
         $w_temp = 0;
         $h_temp = 0;
         $x_temp = 0;
         $y_temp = 0;
      }


      // gestione LINE e CURVE
      if(($predef["tipo"] == "line") or  ($predef["tipo"] == "curve")){
         // variabili X1, Y1
         if(preg_match("/(.* from=.*)/",$riga)){           
           $from_temp = preg_replace("/(.* from=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$from_temp)){
             $from_temp = preg_replace("/(.*)(\".*)/","\$1",$from_temp);
           }
           
           if(preg_match("/(.*,.*)/",$from_temp)){     
            $x1 = preg_replace("/(.*)(,)(.*)/","\$1",$from_temp);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(.*)(,)(.*)/","\$3",$from_temp);
            $y1 = preg_replace("/\s/","",$y1);
           }
           else{
            $x1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$from_temp);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$from_temp);
            $y1 = preg_replace("/\s/","",$y1);
           }
    
           $x1 = converti_vml($x1, $valore_font,$perc_x, $livello);
           $y1 = converti_vml($y1, $valore_font,$perc_y, $livello);
         
           $predef["x1"] = $x1;
           $predef["y1"] = $y1;
           
         }
         else{
            $predef["x1"] = 0;
            $predef["y1"] = 0;
         }
    
         // variabili X2, Y2
         if(preg_match("/(.* to=.*)/",$riga)){         
           $to_temp = preg_replace("/(.* to=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$to_temp)){
             $to_temp = preg_replace("/(.*)(\".*)/","\$1",$to_temp);
           }
           
           if(preg_match("/(.*,.*)/",$to_temp)){       
            $x2 = preg_replace("/(.*)(,)(.*)/","\$1",$to_temp);
            $x2 = preg_replace("/\s/","",$x2);

            $y2 = preg_replace("/(.*)(,)(.*)/","\$3",$to_temp);
            $y2 = preg_replace("/\s/","",$y2);
           }
           else{
            $x2 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$to_temp);
            $x2 = preg_replace("/\s/","",$x2);

            $y2 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$to_temp);
            $y2 = preg_replace("/\s/","",$y2);
           }
    
           $x2 = converti_vml($x2, $valore_font,$perc_x, $livello);
           $y2 = converti_vml($y2, $valore_font,$perc_y, $livello);

           $predef["x2"] = $x2;
           $predef["y2"] = $y2;
           
         }
         else{
            if($predef["tipo"] == "line"){
                $predef["x2"] = 10;
                $predef["y2"] = 10; 
            }
            else{
                $predef["x2"] = 30;
                $predef["y2"] = 20; 
            }
        }
      } // fine gestione line, curve


      // gestione CURVE, control point
      if($predef["tipo"] == "curve"){
         // variabili control_x_1, control_y_1, control_x_2, control_y_2
         // CONTROL1
         if(preg_match("/(.* control1=.*)/",$riga)){           
           $c1 = preg_replace("/(.* control1=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$c1)){
             $c1 = preg_replace("/(.*)(\".*)/","\$1",$c1);
           }
           
           if(preg_match("/(.*,.*)/",$c1)){    
            $x1 = preg_replace("/(.*)(,)(.*)/","\$1",$c1);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(.*)(,)(.*)/","\$3",$c1);
            $y1 = preg_replace("/\s/","",$y1);
           }
           else{
            $x1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$c1);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$c1);
            $y1 = preg_replace("/\s/","",$y1);
           }
    
           $x1 = converti_vml($x1, $valore_font,$perc_x, $livello);
           $y1 = converti_vml($y1, $valore_font,$perc_y, $livello);
         
           $predef["control_x_1"] = $x1;
           $predef["control_y_1"] = $y1;
           
         }
         else{
            $predef["control_x_1"] = 10;
            $predef["control_y_1"] = 10;
         }
        
         // CONTROL2 
         if(preg_match("/(.* control2=.*)/",$riga)){           
           $c2 = preg_replace("/(.* control2=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$c2)){
             $c2 = preg_replace("/(.*)(\".*)/","\$1",$c2);
           }
           
           if(preg_match("/(.*,.*)/",$c2)){    
            $x1 = preg_replace("/(.*)(,)(.*)/","\$1",$c2);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(.*)(,)(.*)/","\$3",$c2);
            $y1 = preg_replace("/\s/","",$y1);
           }
           else{
            $x1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$c2);
            $x1 = preg_replace("/\s/","",$x1);

            $y1 = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$c2);
            $y1 = preg_replace("/\s/","",$y1);
           }
    
           $x1 = converti_vml($x1, $valore_font,$perc_x, $livello);
           $y1 = converti_vml($y1, $valore_font,$perc_y, $livello);
         
           $predef["control_x_2"] = $x1;
           $predef["control_y_2"] = $y1;
           
         }
         else{
            $predef["control_x_2"] = 20;
            $predef["control_y_2"] = 0;
         }
      } // fine gestione curve, control point


      // gestione POLYLINE, POLYGON
      if(($predef["tipo"] == "polyline") or ($predef["tipo"] == "polygon")){
        
         // variabile points
         if(preg_match("/(.* points=.*)/",$riga)){
           $pp = preg_replace("/(.* points=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$pp)){
             $pp = preg_replace("/(.*)(\".*)/","\$1",$pp);        
           }
               
           $n_punti = 0;
           while(preg_match("/(.*\d.*)/",$pp)){
             $predef["punti"][$n_punti] = preg_replace("/(-?\d+\.?\d*)(\D*)(.*)/","\$1",$pp);
             $pp = preg_replace("/(-?\d+\.?\d*)(\D*)(.*)/","\$3",$pp);
             $n_punti += 1;
           }
           $predef["n_punti"] = $n_punti;           
         }
         else{
            $predef["n_punti"] = 0;
         }

      } // fine gestione polyline, polygon


      // gestione ARC
      if($predef["tipo"] == "arc"){
         // variabili start-angle e end-angle
         // START-angle
         if(preg_match("/(.* startangle=.*)/",$riga)){         
           $start = preg_replace("/(.* startangle=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$start)){
             $start = preg_replace("/(.*)(\".*)/","\$1",$start);
           }
           $start = preg_replace("/\s/","",$start);
                    
           $predef["start-angle"] = $start;        
         }
         else{
            $predef["start-angle"] = 0;
         }
         // END-angle
         if(preg_match("/(.* endangle=.*)/",$riga)){           
           $end = preg_replace("/(.* endangle=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$end)){
             $end = preg_replace("/(.*)(\".*)/","\$1",$end);
           }
           $end = preg_replace("/\s/","",$end);
      
           $predef["end-angle"] = $end;  
         }
         else{
            $predef["end-angle"] = 90;
         }    
      } // fine gestione arc

      // gestione ROUNDRECT
      if($predef["tipo"] == "roundrect"){
         // variabile arcsize
         if(preg_match("/(.* arcsize=.*)/",$riga)){        
           $arcs = preg_replace("/(.* arcsize=\")(.*)(\".*)/","\$2",$riga);
           while(preg_match("/(.*\".*)/",$arcs)){
             $arcs = preg_replace("/(.*)(\".*)/","\$1",$arcs);
           }
           $arcs = preg_replace("/\s/","",$arcs);
   
           if($arcs > 1){ $arcs = 1; }       
              $predef["arc-size"] = $arcs;
         }
         else{
            $predef["arc-size"] = 0.2;
         }    
      } // fine gestione roundrect


      // conversione (translate + viewbox);
      conversione_vml(&$tr_x, &$tr_y, &$vb_x, &$vb_y, $n_translate, $translate_x, $translate_y, 
                  $n_viewbox, $viewbox_x, $viewbox_y, $w_val, $h_val, $translate_livello, $viewbox_livello);

      $predef["x"] = ($x_temp * $vb_x) + $tr_x ;
      $predef["y"] = ($y_temp * $vb_y) + $tr_y;
      $predef["w"] = $w_temp * $vb_x;
      $predef["h"] = $h_temp * $vb_y;
      
      // DEBUG
      //echo $vb_x." tr: ".$tr_x."<br />";
      //echo $vb_y." tr: ".$tr_y."<br />";

      if($predef["tipo"] == "curve"){
        $predef["w"] = $vb_x; 
        $predef["h"] = $vb_y;       
      }

      
      if($predef["tipo"] == "line"){
        $predef["x1"] = ($x1 * $vb_x) + $tr_x; 
        $predef["y1"] = ($y1 * $vb_y) + $tr_y;
        $predef["x2"] = ($x2 * $vb_x) + $tr_x;
        $predef["y2"] = ($y2 * $vb_y) + $tr_y;
      }
  
      if(($predef["tipo"] == "polyline") or ($predef["tipo"] == "polygon")){        
           $j_punti = 0;
           $pari = 0;
           while($j_punti < $n_punti){
              if ($pari == 0){
                $predef["punti"][$j_punti]  = ($predef["punti"][$j_punti] * $vb_x) + $tr_x;
                $pari = 1;
              }
              else{
                $predef["punti"][$j_punti]  = ($predef["punti"][$j_punti] * $vb_y) + $tr_y;
                $pari = 0;
              }
              $j_punti += 1;
           }
      }

      // imposto fill, stroke e stroke_w:
      // FILL
      $fill_true = 1;
      $predef["fill"] = "white"; 
      if(preg_match("/(.* fill=.*)/",$riga)){      
        $fill_temp= preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$fill_temp)){
           $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
        }     
        $fill_temp = preg_replace("/\s/","",$fill_temp);
        if(($fill_temp == "f") or ($fill_temp == "F") or ($fill_temp == "false") or ($fill_temp == "FALSE")){
            $fill_true = 0;
            $predef["fill"] = "none";
        }
      }
      if((preg_match("/(.* fillcolor=.*)/",$riga))){ //and  ($fill_true == 1)){    
        $fill_temp= preg_replace("/(.* fillcolor=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$fill_temp)){
          $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
        }     
        $fill_temp = preg_replace("/\s/","",$fill_temp);
        $predef["fill"] = $fill_temp;     
      }
      
      // STROKE
      $stroke_true = 1;
      $predef["stroke"] = "black"; 
      if(preg_match("/(.* stroke=.*)/",$riga)){    
        $stroke_temp= preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_temp)){
           $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
        }     
        $stroke_temp = preg_replace("/\s/","",$stroke_temp);
        if(($stroke_temp == "f") or ($stroke_temp == "F") or ($stroke_temp == "false") or ($stroke_temp == "FALSE")){
          $stroke_true = 0;
            $predef["stroke"] = "none";
        }
      }
      if((preg_match("/(.* strokecolor=.*)/",$riga))){ //and  ($stroke_true == 1)){    
        $stroke_temp= preg_replace("/(.* strokecolor=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_temp)){
          $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
        }     
        $stroke_temp = preg_replace("/\s/","",$stroke_temp);
        $predef["stroke"] = $stroke_temp;     
      }
      
      // STROKE-WEIGHT
      // nb: se non c'e' vale quello di default (0.75)
      if((preg_match("/(.* strokeweight=.*)/",$riga))){
        $stroke_w= preg_replace("/(.* strokeweight=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_w)){
          $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);
        }     
        $stroke_w = preg_replace("/\s/","",$stroke_w);
        $stroke_w = converti_vml($stroke_w, $valore_font,100, 1);

        $predef["stroke_w"] = $stroke_w;      
      }

  }// fine funzione predef

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funzione shape

    // SHAPE:
    //  gestisce l'elemento shape, imposta tutti i valori degli attributi di shape (dimensioni, colori_vml, ecc.)
    //      all'interno della struttura shape. Non procede alla visualizzazione, in quanto alcune caratteristiche
    //      potrebbero essere influenza da eventuali riferimenti ad elementi shapetype
    //
    function shape(&$image, &$riga, &$n_viewbox, &$viewbox_x, &$viewbox_y, $viewbox_co_x, $viewbox_co_y,
                   &$n_translate, &$livello, &$translate_x, &$translate_y,
                   &$n_stroke, &$n_fill, &$fill, &$stroke, &$fill_livello, &$stroke_livello,
                   &$w, &$h, &$n_wh, &$w_val, &$h_val, &$translate_livello,
                   &$viewbox_livello, &$wh_livello, $colori_vml, &$font_temp, &$shape, 
                   &$on_shape, &$fill_on, &$stroke_on, &$fill_color, &$stroke_color, $perc_x, $perc_y, $livello){

      // gestione delle proprieta' contenute in style
      if(preg_match("/(.* style=.*)/",$riga)){     
         $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*[\"|'].*)/",$style)){
             $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
          }
          $style = " ".$style;
         
          $w_temp = 0;
          if(preg_match("/(.*[ |;]width\s?:.*)/",$style)){
            $w_temp = preg_replace("/(.*[ |;]width\s?:)(.*?)/","\$2",$style);
            if(preg_match("/(.*;.*)/",$w_temp)){
                 while(preg_match("/(.*;.*)/",$w_temp)){
                       $w_temp = preg_replace("/(.*?)(;.*)/","\$1",$w_temp);
                  }
             }
         
             $w_temp = preg_replace("/\s/","",$w_temp);
             $w_temp = converti_vml($w_temp,$font_temp,$perc_x, $livello);
          }  

          $h_temp = 0;
          if(preg_match("/(.*[ |;]height\s?:.*)/",$style)){
             $h_temp = preg_replace("/(.*[ |;]height\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$h_temp)){
                 while(preg_match("/(.*;.*)/",$h_temp)){
                       $h_temp = preg_replace("/(.*)(;.*)/","\$1",$h_temp);
                  }
             }
             $h_temp = preg_replace("/\s/","",$h_temp);
             $h_temp = converti_vml($h_temp,$font_temp,$perc_y, $livello); 
          }

          $x_temp = 0;
          if(preg_match("/(.*[ |;]left\s?:.*)/",$style)){
             $x_temp = preg_replace("/(.*[ |;]left\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$x_temp)){
               while(preg_match("/(.*;.*)/",$x_temp)){
                       $x_temp = preg_replace("/(.*)(;.*)/","\$1",$x_temp);
               }
             }
             $x_temp = preg_replace("/\s/","",$x_temp);
             $x_temp = converti_vml($x_temp,$font_temp,$perc_x, $livello); 
          }  

          $y_temp = 0;
          if(preg_match("/(.*[ |;]top\s?:.*)/",$style)){
             $y_temp = preg_replace("/(.*[ |;]top\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$y_temp)){
                while(preg_match("/(.*;.*)/",$y_temp)){
                    $y_temp = preg_replace("/(.*)(;.*)/","\$1",$y_temp);
                }
             }
                                 
             $y_temp = preg_replace("/\s/","",$y_temp);
             $y_temp = converti_vml($y_temp,$font_temp,$perc_y, $livello); 
          }

          if(preg_match("/(.*[ |;]font-size\s?:.*)/",$style)){
                 $fs = preg_replace("/(.*[ |;]font-size\s?:)(.*?)/","\$2",$style);
                 if(preg_match("/(.*;.*)/",$fs)){
                     while(preg_match("/(.*;.*)/",$fs)){
                       $fs = preg_replace("/(.*)(;.*)/","\$1",$fs);
                      }
                 }
                $fs = preg_replace("/\s/","",$fs);
                $fs = converti_vml($fs,$font_temp,$perc_x, 1); 
                
                $shape["text"]["font_s"] = "default_".$fs;
          }

          if(preg_match("/(.*[ |;]font-family\s?:.*)/",$style)){
             $ff = preg_replace("/(.*[ |;]font-family\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$ff)){
                 while(preg_match("/(.*;.*)/",$ff)){
                       $ff = preg_replace("/(.*)(;.*)/","\$1",$ff);
                  }
             }
             $ff = preg_replace("/\s/","",$ff);
             
             $shape["text"]["font_f"] = "default_".$ff;
          }          
      }
      // non c'e' style
      else{
         $w_temp = 0;
         $h_temp = 0;
         $x_temp = 0;
         $y_temp = 0;
      }

      // gestione coordorigin e coordsize
      
      // valori di default di viewbox (coordsize)
      if($n_viewbox > 0){
        $vb_x = $viewbox_x[$n_viewbox - 1];
        $vb_y = $viewbox_y[$n_viewbox - 1];
      }
      else{
        $vb_x = 1000; 
        $vb_y = 1000;
      }
      if(preg_match("/(.* coordsize=.*)/",$riga)){     
         $shape["impostato_cs"] = "T";
         $vb = preg_replace("/(.* coordsize=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$vb)){
               $vb = preg_replace("/(.*)(\".*)/","\$1",$vb);
         }
         if(preg_match("/(.*,.*)/",$vb)){      
            $vb_x = preg_replace("/(.*)(,)(.*)/","\$1",$vb);
            $vb_x = preg_replace("/\s/","",$vb_x);

            $vb_y = preg_replace("/(.*)(,)(.*)/","\$3",$vb);
            $vb_y = preg_replace("/\s/","",$vb_y);
         }
         else{
            $vb_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$vb);
            $vb_x = preg_replace("/\s/","",$vb_x);

            $vb_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$vb);
            $vb_y = preg_replace("/\s/","",$vb_y);
         }
      }
        
      $shape["vb_x"] = $vb_x;
      $shape["vb_y"] = $vb_y;   

      // imposto coordorigin: la tratto come una traslazione
      $co_x = 0; $co_y = 0;
      if($n_viewbox > 0){
        $co_x = (-1) * $viewbox_co_x[$n_viewbox - 1];
        $co_y = (-1) * $viewbox_co_y[$n_viewbox - 1];
        $shape["co_x_default"] = $co_x;
        $shape["co_y_default"] = $co_y;
      }
      else{
        $co_x = 0; 
        $co_y = 0;
      }
      if(preg_match("/(.* coordorigin=.*)/",$riga)){       
        $shape["impostato_co"] = "T";

        $co = preg_replace("/(.* coordorigin=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$co)){
                $co = preg_replace("/(.*)(\".*)/","\$1",$co);
        }
        if(preg_match("/(.*,.*)/",$co)){       
            $co_x = preg_replace("/(.*)(,)(.*)/","\$1",$co);
            $co_x = preg_replace("/\s/","",$co_x);

            $co_y = preg_replace("/(.*)(,)(.*)/","\$3",$co);
            $co_y = preg_replace("/\s/","",$co_y);
        }
        else{
            $co_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$co);
            $co_x = preg_replace("/\s/","",$co_x);

            $co_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$co);
            $co_y = preg_replace("/\s/","",$co_y);
        }
    
        $co_x = -1 * $co_x;
        $co_y = -1 * $co_y;       
      }         

      // conversione (translate + viewbox);
      conversione_vml(&$tr_x, &$tr_y, &$vb_x, &$vb_y, $n_translate, $translate_x, $translate_y, 
                  $n_viewbox, $viewbox_x, $viewbox_y, $w_val, $h_val, $translate_livello, $viewbox_livello);
    
      // DEBUG
      //echo "co_x: ".$co_x." co_y ".$co_y."<br />";
      
      // valori da moltiplicare per coordorigin (x e y). Serve anche in shapetype
      $shape["co_x_conv"] = 1; 
      $shape["co_y_conv"] = 1;
      
      $k = 0;
      while($k < $n_viewbox){
        $shape["co_x_conv"] *= ($w_val[$k] / $viewbox_x[$k]);
        $shape["co_y_conv"] *= ($h_val[$k] / $viewbox_y[$k]);
        $k += 1;
      }
      
      $shape["co_x_conv"] *= ($w_temp / $shape["vb_x"]);
      $shape["co_y_conv"] *= ($h_temp / $shape["vb_y"]);


      $co_x *= $shape["co_x_conv"];
      $co_y *= $shape["co_y_conv"];

      // NB: questi valori contengono lo shift dovuto al  coordorigin precedente (e ereditato), nel caso che questo shape si riferisca
      //     ad un shapetype questo shift va annullato in quanto coordorigin non viene ereditato
      $shape["co_x_default"] *= $shape["co_x_conv"];
      $shape["co_y_default"] *= $shape["co_y_conv"];

      $shape["co_x"] = $co_x;
      $shape["co_y"] = $co_y;

      $shape["x"] = ($x_temp * $vb_x) + $tr_x + $co_x;       
      $shape["y"] = ($y_temp * $vb_y) + $tr_y + $co_y; 
      
      // DEBUG
      //echo "X: ".$shape["x"]." Y: ".$shape["y"]."<br />";
      
      $shape["w"] = $w_temp * $vb_x;
      $shape["w-eff"] = $w_temp;
      $shape["h-eff"] = $h_temp;
      $shape["h"] = $h_temp * $vb_y;

      // imposto path
      if(preg_match("/(.* path=.*)/",$riga)){
         $path_temp = preg_replace("/(.* path=\")(.*)(\".*)/","\$2",$riga);
         while(preg_match("/(.*\".*)/",$path_temp)){
               $path_temp = preg_replace("/(.*)(\".*)/","\$1",$path_temp);
         }
         $path_temp = preg_replace("/(.*)(,,)(.*)/","\$1,0,\$3",$path_temp);
         
         $shape["path"]["tipo"] = "p";
         
         path($shape, $path_temp);
      }

      
      // imposto fill e stroke:
      // FILL
      $fill_true = 1;
      $shape["fill"] = "default_white"; 
      if(preg_match("/(.* fill=.*)/",$riga)){      
        $fill_temp= preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$fill_temp)){
           $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
        }     
        $fill_temp = preg_replace("/\s/","",$fill_temp);
        if(($fill_temp == "f") or ($fill_temp == "F") or ($fill_temp == "false") or ($fill_temp == "FALSE")){
            $fill_true = 0;
            $shape["fill"] = "cerca_shapetype";
        }
      }
      if((preg_match("/(.* fillcolor=.*)/",$riga))){ //and  ($fill_true == 1)){    
        $fill_temp= preg_replace("/(.* fillcolor=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$fill_temp)){
          $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
        }     
        $fill_temp = preg_replace("/\s/","",$fill_temp);
        $shape["fill"] = $fill_temp;      
      }
      
      // STROKE
      $stroke_true = 1;
      $shape["stroke"] = "default_black"; 
      if(preg_match("/(.* stroke=.*)/",$riga)){    
        $stroke_temp= preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_temp)){
           $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
        }     
        $stroke_temp = preg_replace("/\s/","",$stroke_temp);
        if(($stroke_temp == "f") or ($stroke_temp == "F") or ($stroke_temp == "false") or ($stroke_temp == "FALSE")){
              $stroke_true = 0;
              $shape["stroke"] = "cerca_shapetype";
        }
      }
      if((preg_match("/(.* strokecolor=.*)/",$riga))){ //and  ($stroke_true == 1)){    
        $stroke_temp= preg_replace("/(.* strokecolor=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_temp)){
          $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
        }     
        $stroke_temp = preg_replace("/\s/","",$stroke_temp);
        $shape["stroke"] = $stroke_temp;      
      }
      if((preg_match("/(.* strokeweight=.*)/",$riga))){ 
        $stroke_w= preg_replace("/(.* strokeweight=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$stroke_w)){
          $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);
        }     
        $stroke_w = preg_replace("/\s/","",$stroke_w);
        $stroke_w = converti_vml($stroke_w,$font_temp,100, 1); 

        $shape["stroke_w"] = $stroke_w;   
      }


    }// fine funzione shape

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funzione shapetype

    // SHAPETYPE:
    //  gestisce l'elemento shapetype, viene richiamata da un elemento shape con l'attributo type impostato.
    //      tutti gli elementi shapetype vengono inseriti nell'array contenuto_shapetype, a questo punto,
    //      in base al valori di type dell'elemento shape corrente, si cerca l'opportuno elemento shapetype,
    //      se viene trovato, si analizzano tutte le caratteristiche, impostandole nella struttura per la 
    //      gestione di shape, che si chiama shape, solo quelle proprietà non ancora impostate
    //
    function shapetype(&$image, $colori_vml, &$shape, $contenuto_shapetype, $n_st, $perc_x, $perc_y, $font_temp){
        $on_shapetype = 0;
        $in_textbox = 0;
        $cs_in_shapetype = "F";

        // scorro tutto l'array contenuto_shapetype, alla ricerca dell'elemento shapetype riferito
        $j = 0;
        while ($j < $n_st){
            $riga = $contenuto_shapetype[$j];
            $nome_type = $shape["type"];
            if((preg_match("/(.*id=\"$nome_type\".*)/",$riga)) and 
               (preg_match("/(.*<v:shapetype .*)/",$riga))){
               
                $on_shapetype = 1;
            
                // cerco coordsize 
                if((preg_match("/(.* coordsize=.*)/",$riga)) and ($shape["impostato_cs"] == "F")){     
                    $shape["impostato_cs"] = "T";
                    $cs_in_shapetype = "T";
                    $vb = preg_replace("/(.* coordsize=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$vb)){
                       $vb = preg_replace("/(.*)(\".*)/","\$1",$vb);
                    }
                    if(preg_match("/(.*,.*)/",$vb)){      
                        $vb_x = preg_replace("/(.*)(,)(.*)/","\$1",$vb);
                        $vb_x = preg_replace("/\s/","",$vb_x);

                        $vb_y = preg_replace("/(.*)(,)(.*)/","\$3",$vb);
                        $vb_y = preg_replace("/\s/","",$vb_y);
                    }
                    else{
                        $vb_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$vb);
                        $vb_x = preg_replace("/\s/","",$vb_x);

                        $vb_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$vb);
                        $vb_y = preg_replace("/\s/","",$vb_y);
                    }
                
                    $vb_x_old = $shape["vb_x"];
                    $vb_y_old = $shape["vb_y"];
                    $shape["vb_x"] = $vb_x;
                    $shape["vb_y"] = $vb_y;    
                }
                elseif($shape["impostato_cs"] == "F"){
                    // se shape si riferisce ad un shapetype non eredita coordsize 
                    //      dagli elementi precedenti 
                    $shape["vb_x"] = 1000;
                    $shape["vb_y"] = 1000;
                }
            
                // imposto coordorigin: la tratto come una traslazione
                if((preg_match("/(.* coordorigin=.*)/",$riga)) and ($shape["impostato_co"] == "F")){       
                    $co_x = 0; $co_y = 0;
                    $shape["impostato_co"] = "T";   
                    $co = preg_replace("/(.* coordorigin=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$co)){
                       $co = preg_replace("/(.*)(\".*)/","\$1",$co);
                    }
                    if(preg_match("/(.*,.*)/",$co)){       
                        $co_x = preg_replace("/(.*)(,)(.*)/","\$1",$co);
                        $co_x = preg_replace("/\s/","",$co_x);

                        $co_y = preg_replace("/(.*)(,)(.*)/","\$3",$co);
                        $co_y = preg_replace("/\s/","",$co_y);
                    }
                    else{
                        $co_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$co);
                        $co_x = preg_replace("/\s/","",$co_x);

                        $co_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$co);
                        $co_y = preg_replace("/\s/","",$co_y);
                    }
                    $co_x = -1 * $co_x;
                    $co_y = -1 * $co_y;
           
                    // imposto correttamento lo shift
                    $co_x *= $shape["co_x_conv"];
                    $co_y *= $shape["co_y_conv"];

                    if($cs_in_shapetype == "T"){
                  
                        $co_x =  $co_x / ($shape["w-eff"] / $vb_x_old);
                        $co_y =  $co_y / ($shape["h-eff"] / $vb_y_old);   
                                
                        $co_x *= $shape["w-eff"] / $vb_x;
                        $co_y *= $shape["h-eff"] / $vb_y;
                    }
                
                    $shape["x"] += $co_x;
                    $shape["y"] += $co_y;
                }    
                elseif($shape["impostato_co"] == "F"){
                    // se shape si riferisce ad un shapetype non eredita 
                    //      coordorigin dagli elementi precedenti  
                    $shape["x"] -=  $shape["co_x_default"];
                    $shape["y"] -=  $shape["co_y_default"];
                }

                // imposto path
                if((preg_match("/(.* path=.*)/",$riga)) and ($shape["path"]["impostato"] == "F")){
                    $path_temp = preg_replace("/(.* path=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$path_temp)){
                       $path_temp = preg_replace("/(.*)(\".*)/","\$1",$path_temp);
                    }
                    $path_temp = preg_replace("/(.*)(,,)(.*)/","\$1,0,\$3",$path_temp);
                    $path_temp = preg_replace("/(.*)(xe)(.*)/","\$1x e\$3",$path_temp);

                    if($shape["path"]["tipo"] == "none"){
                         $shape["path"]["tipo"] = "p";
                    }
         
                    path($shape, $path_temp);
                    
                    if($shape["path"]["impostato"] == "T"){
                        $shape["path"]["impostato"] = "S";
                    }
                }

                // imposto fill e stroke:
                // FILL 
                $fill_true = 1;
                $fill_st = "white"; 
                if(preg_match("/(.* fill=.*)/",$riga)){      
                    $fill_temp= preg_replace("/(.* fill=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$fill_temp)){
                        $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
                    }     
                    $fill_temp = preg_replace("/\s/","",$fill_temp);
                    if(($fill_temp == "f") or ($fill_temp == "F") or 
                       ($fill_temp == "false") or ($fill_temp == "FALSE")){
                        $fill_true = 0;
                        $fill_st = "none";
                    }
                }
                if((preg_match("/(.* fillcolor=.*)/",$riga))){ //and  ($fill_true == 1)){    
                    $fill_temp= preg_replace("/(.* fillcolor=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$fill_temp)){
                        $fill_temp = preg_replace("/(.*)(\".*)/","\$1",$fill_temp);
                    }     
                    $fill_temp = preg_replace("/\s/","",$fill_temp);
                    $fill_st = $fill_temp;    
                }
                // impostiamo fill
                if($shape["fill"] == "cerca_shapetype"){ $shape["fill"] = $fill_st; }
                elseif((preg_match("/(default_)(.*)/",$shape["fill"])) and ($fill_st != "none")){
                    $shape["fill"] = $fill_st;
                }
 
                // STROKE
                $stroke_true = 1;
                $stroke_st = "black"; 
                if(preg_match("/(.* stroke=.*)/",$riga)){    
                    $stroke_temp= preg_replace("/(.* stroke=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$stroke_temp)){
                        $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
                    }     
                    $stroke_temp = preg_replace("/\s/","",$stroke_temp);
                    if(($stroke_temp == "f") or ($stroke_temp == "F") or 
                       ($stroke_temp == "false") or ($stroke_temp == "FALSE")){
                        $stroke_true = 0;
                        $stroke_st = "none";
                    }
                }
                if((preg_match("/(.* strokecolor=.*)/",$riga))){ //and  ($stroke_true == 1)){    
                    $stroke_temp= preg_replace("/(.* strokecolor=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$stroke_temp)){
                        $stroke_temp = preg_replace("/(.*)(\".*)/","\$1",$stroke_temp);
                    }     
                    $stroke_temp = preg_replace("/\s/","",$stroke_temp);
                    $stroke_st = $stroke_temp;    
                }
                if((preg_match("/(.* strokeweight=.*)/",$riga))){
                    $stroke_w= preg_replace("/(.* strokeweight=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$stroke_w)){
                        $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);
                    }     
                    $stroke_w = preg_replace("/\s/","",$stroke_w);
                    $stroke_w = converti_vml($stroke_w, $font_temp, 100,1); 
                    if(preg_match("/(.*default_.*)/",$shape["stroke_w"])){
                        $shape["stroke_w"] = "st_".$stroke_w;     
                    }
                }

                // impostiamo stroke
                if($shape["stroke"] == "cerca_shapetype"){ $shape["stroke"] = $stroke_st; }
                elseif((preg_match("/(default_)(.*)/",$shape["stroke"])) and ($stroke_st != "none")){
                    $shape["stroke"] = $stroke_st;
                }
              
                // controlliamo se shapetype non contiene altri elementi
                if(preg_match("/\/>/",$riga)){
                    $on_shapetype = 0; 
                    $j = $n_st;
                }           
            } // fine shapetype senza elementi
            
            // gestione path, fill e stroke

            /////////////////////////////////////////////////
            // gestione path ////////////////////////////////
            /////////////////////////////////////////////////
            if(preg_match("/(.*<v:path.*)/",$riga)){
                // path puo' comparire solo dentro shape o shapetype.   
                if(($on_shapetype == 1) and ($shape["path"]["impostato"] != "T")){
                        
                    if($shape["path"]["tipo"] == "none"){
                        $shape["path"]["tipo"] = "p";
                    }
            
                    if(preg_match("/(.* v=.*)/",$riga)){       
                        $path_temp= preg_replace("/(.* v=\")(.*)(\".*)/","\$2",$riga);
                        while(preg_match("/(.*\".*)/",$path_temp)){
                            $path_temp = preg_replace("/(.*)(\".*)/","\$1",$path_temp);
                        }
                        $path_temp = preg_replace("/(.*)(,,)(.*)/","\$1,0,\$3",$path_temp);
                        $path_temp = preg_replace("/(.*)(xe)(.*)/","\$1x e\$3",$path_temp);

                        if($path_temp != ""){
                            path($shape, $path_temp);
                            if($shape["path"]["impostato"] == "T"){
                                $shape["path"]["impostato"] = "S";
                            } 
                        }    
                    }   
                } // fine on_shapetype = 1 and impostatop path != T
                
                if($on_shapetype == 1){
                    if(preg_match("/(.* textpathok=.*)/",$riga)){      
                        $tp= preg_replace("/(.* textpathok=\")(.*)(\".*)/","\$2",$riga);
                        while(preg_match("/(.*\".*)/",$tp)){
                            $tp = preg_replace("/(.*)(\".*)/","\$1",$tp);
                        }                  
                        $tp = preg_replace("/\s/","",$tp);       

                        if(($tp == "t") or ($tp == "T") or ($tp == "true") or ($tp == "TRUE")){
                            $shape["path"]["tipo"] = "t";
                        }
                    }
                    if((preg_match("/(.* textboxrect=.*)/",$riga)) and 
                       ($shape["textbox"]["impostato_box"] == "F")){
                  
                        $tb= preg_replace("/(.* textboxrect=\")(.*)(\".*)/","\$2",$riga);
                        while(preg_match("/(.*\".*)/",$tb)){
                            $tb = preg_replace("/(.*)(\".*)/","\$1",$tb);
                        }
                        $tb = preg_replace("/\s/","",$tb);       
                        $shape["textbox"]["impostato_box"] = "T";
             
                        $j = 0;
                        while((preg_match("/(\d)/",$tb)) and ($j < 2)){
                            $valore[$j] = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$2",$tb);           
                            $tb  = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$3",$tb);
                            $j += 1;
                        }
                        $valore[0] = preg_replace("/\s/","",$valore[0]);         
                        $valore[1] = preg_replace("/\s/","",$valore[1]);         
        
                        $shape["textbox"]["x"] = $valore[0];
                        $shape["textbox"]["y"] = $valore[1];
                    }   
 
                } // fine on_shape = 1
            } // fine gestione path


            /////////////////////////////////////////////////
            // gestione textpath ////////////////////////////
            /////////////////////////////////////////////////
            if(preg_match("/(.*<v:textpath.*)/",$riga)){
                // path puo' comparire solo dentro shape o shapetype. 
                if(($on_shapetype == 1) and ($shape["text"]["impostato"] == "F")){
                    // da impostare font-family, font-size
                    if(preg_match("/(.* string=.*)/",$riga)){    
                        $string_temp= preg_replace("/(.* string=\")(.*)(\".*)/","\$2",$riga);
                        while(preg_match("/(.*\".*)/",$string_temp)){
                            $string_temp = preg_replace("/(.*)(\".*)/","\$1",$string_temp);
                        }
                        $str_len = strlen($string_temp);
                        $string_temp = substr($string_temp,0,$str_len - 1);
                        $shape["text"]["testo"] = $string_temp;    
                        $shape["text"]["impostato"] = "T";
                    }                     
                } // fine on_shape = 1

                if(preg_match("/(.* style=.*)/",$riga)){     
                    $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*[\"|'].*)/",$style)){
                        $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
                    }
                    $style = " ".$style;

                    if(preg_match("/(.*[ |;]font-size\s?:.*)/",$style)){
                        $fs = preg_replace("/(.*[ |;]font-size\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$fs)){
                            while(preg_match("/(.*;.*)/",$fs)){
                                $fs = preg_replace("/(.*)(;.*)/","\$1",$fs);
                            }
                        }
                        $fs = preg_replace("/\s/","",$fs);
                        $fs = converti_vml($fs,$font_temp,$perc_x, 1);
                        if(preg_match("/(.*default_.*)/",$shape["text"]["font_s"])){
                            $shape["text"]["font_s"] = $fs;
                        }               
                    }

                    if(preg_match("/(.*[ |;]font-family\s?:.*)/",$style)){
                        $ff = preg_replace("/(.*[ |;]font-family\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$ff)){
                            while(preg_match("/(.*;.*)/",$ff)){
                                $ff = preg_replace("/(.*)(;.*)/","\$1",$ff);
                            }
                        }
                        $ff = preg_replace("/\s/","",$ff);
                        if(preg_match("/(.*default_.*)/",$shape["text"]["font_f"])){
                            $shape["text"]["font_f"] = $ff;
                        }
                    }
                }               
            } // fine gestione textpath

            /////////////////////////////////////////////////
            // gestione imagedata ////////////////////////////////
            /////////////////////////////////////////////////
            if(preg_match("/(.*<v:imagedata.*)/",$riga)){
                // path puo' comparire solo dentro shape o shapetype. 
                if($on_shapetype == 1){
                    $shape["fill"] = "none";
         
                    if(preg_match("/(.*src=.*)/",$riga)){
                        $href = preg_replace("/(.*src=\")(.*)(\".*)/","\$2",$riga);
                        while(preg_match("/(.*\".*)/",$href)){
                            $href = preg_replace("/(.*)(\".*)/","\$1",$href);
                        }
                        $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
                        $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);
                    }
                    $estensione = preg_replace("/(.*)(\.)(.*)/","\$3",$href); 
                
                    $shape["image_st"][$shape["n_image_st"]]["estensione"] = $estensione;
                    $shape["image_st"][$shape["n_image_st"]]["nome_file"]  = $href;
                    $shape["n_image_st"] += 1;
                          
                } // fine on_shape = 1
            } // fine gestione imagedata

            /////////////////////////////////////////////////
            // gestione texbox //////////////////////////////
            /////////////////////////////////////////////////
            if(preg_match("/(.*<v:textbox.*)/",$riga)){
                // path puo' comparire solo dentro shape o shapetype. 
                if(($on_shapetype == 1) and ($shape["textbox"]["impostato_testo"] == "F") ){
                    $in_textbox = 1;

                    $shape["textbox"]["impostato_testo"] = "T";
                    $shape["textbox"]["valore"] =  preg_replace("/(<.*>)(.*)/","\$2",$riga);
        
                    $str_len = strlen($shape["textbox"]["valore"]);
                    $shape["textbox"]["valore"] = substr($shape["textbox"]["valore"],0,$str_len - 1);
                } // fine on_shapetype = 1
            } // fine gestione tag iniziale textpath
    
            if(preg_match("/(.*<\/v:textbox.*)/",$riga)){
                $in_textbox = 0;
            } // fine textbox
    
            if($in_textbox == 1){
                // aggiungo testo 
                $shape["textbox"]["valore"] .=  preg_replace("/(<.*>)(.*)/","\$2",$riga);
                $str_len = strlen($shape["textbox"]["valore"]);
                $shape["textbox"]["valore"] = substr($shape["textbox"]["valore"],0,$str_len - 1);
            }

            ///////////////////////////////////////////////////
            // gestione stoke - fill (elementi)  //////////////
            //////////////////////////////////////////////////
            if(preg_match("/(.*<v:stroke.*)/",$riga)){
            
                // on
                $on_temp = 1;
                if(preg_match("/(.* on=.*)/",$riga_temp)){
                    $on_temp= preg_replace("/(.* on=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$on_temp)){
                        $on_temp = preg_replace("/(.*)(\".*)/","\$1",$on_temp);
                    }
                    $on_temp = preg_replace("/\s/","",$on_temp);
          
                    if(($on_temp == "f") or ($on_temp == "F") or 
                       ($on_temp == "false") or ($on_temp == "FALSE")){
                        $on_temp = 0;
                    }
                    else{
                        $on_temp = 1;
                    }
                }
                
                // color
                $color_temp = "black";
                if(preg_match("/(.* color=.*)/",$riga)){
                    $color_temp= preg_replace("/(.* color=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$color_temp)){
                        $color_temp = preg_replace("/(.*)(\".*)/","\$1",$color_temp);
                    }
                    $color_temp = preg_replace("/\s/","",$color_temp);          
                }
                $stroke_w = "none";
                if(preg_match("/(.* weight=.*)/",$riga)){
                    $stroke_w= preg_replace("/(.* weight=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$stroke_w)){
                        $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);
                    }
                    $stroke_w = preg_replace("/\s/","",$stroke_w);          
                    $stroke_w = converti_vml($stroke_w,$font_temp,100,1);
                }  
            
                // Mettiamo i valori trovati nei rispettivi elementi:
                if($on_shapetype == 1){
                    // impostiamo stroke
                    if($shape["stroke"] == "cerca_shapetype"){ 
                        if($on_temp == 0){
                            $shape["stroke"] = "none";
                        }
                        else{
                            // if color_temp != none ?? 
                            $shape["stroke"] = $color_temp;
                        
                            if($stroke_w != "none"){
                                if($preg_match("/(.*default_.*)/",$shape["stroke_w"]) or 
                                    $preg_match("/(.*st_.*)/",$shape["stroke_w"])){
                                    $shape["stroke_w"] = "st_".$stroke_w;
                                }
                            }                       
                        }
                    }
                    elseif((preg_match("/(default_)(.*)/",$shape["stroke"])) and ($on_temp != 0)){
                        $shape["stroke"] = $color_temp;
                    }
                }
            } // fine stroke
    
            // FILL
            if(preg_match("/(.*<v:fill.*)/",$riga)){
            
                // on
                $on_temp = 1;
                if(preg_match("/(.* on=.*)/",$riga)){
                    $on_temp= preg_replace("/(.* on=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$on_temp)){
                        $on_temp = preg_replace("/(.*)(\".*)/","\$1",$on_temp);
                    }
                    $on_temp = preg_replace("/\s/","",$on_temp);
          
                    if(($on_temp == "f") or ($on_temp == "F") or 
                       ($on_temp == "false") or ($on_temp == "FALSE")){
                        $on_temp = 0;
                    }
                    else{
                        $on_temp = 1;
                    }
                }
                
                // color
                $color_temp = "white";
                if(preg_match("/(.* color=.*)/",$riga)){
                    $color_temp = preg_replace("/(.* color=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$color_temp)){
                        $color_temp = preg_replace("/(.*)(\".*)/","\$1",$color_temp);
                    }
                    $color_temp = preg_replace("/\s/","",$color_temp);          
                }
              
                if(preg_match("/(.* type=.*)/",$riga)){
                    $type = preg_replace("/(.* type=\")(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*\".*)/",$type)){
                        $type = preg_replace("/(.*)(\".*)/","\$1",$type);
                    }
                    $type = preg_replace("/\s/","",$type);
                 
                    if($type != "solid"){
                        $color_temp = "gray";
                    }
                }

                // Mettiamo i valori trovati nei rispettivi elementi:
                if($on_shapetype == 1){
                    // impostiamo fill
                    if($shape["fill"] == "cerca_shapetype"){ 
                        if($on_temp == 0){
                            $shape["fill"] = "none";
                        }
                        else{
                            $shape["fill"] = $color_temp;
                        }                   
                    }
                    elseif((preg_match("/(default_)(.*)/",$shape["fill"])) and ($on_temp != 0)){
                        $shape["fill"] = $color_temp;
                    }
                }
            } // fine fill

            
            if(preg_match("/(.*<\/v:shapetype\".*)/",$riga)){
                $on_shapetype = 0;
                $j = $n_st;
            } // fine shapetype con elementi
            
            
            $j += 1;
        }         
    }// fine funzione shapetype


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funzione path

    // PATH:
    //  seleziona e suddivide tutti i comandi e i rispettivi valori della stringa che rappresenta il path,
    //      inserendoli in un'opprotuna struttura.
    //
    function path(&$shape, &$path){
        
        $path_n_comandi = 0;
        $i = 0;
        while(preg_match("/([a-zA-Z])/",$path)){
           $shape["path"]["comando"][$i] = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$2",$path);
           $shape["path"]["comando"][$i] = preg_replace("/\s/","",$shape["path"]["comando"][$i]);
           $path = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$3",$path);
           $val_temp = preg_replace("/(.*?)([a-zA-Z]+)(.*)/","\$1",$path);
           $j = 0;
           while(preg_match("/(\d)/",$val_temp)){
                $shape["path"]["valori"][$i][$j] = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$2",$val_temp);            
                $val_temp  = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$3",$val_temp);
                $j += 1;
           }
           $shape["path"]["n_valori"][$i] = $j;
           $i += 1;
        }

        $shape["path"]["n_comandi"] = $i;
        if ($i > 0){ $shape["path"]["impostato"] = "T"; }
        
        // NB: i comandi non sono translati ne scaleti, bisogna farlo al momento della gestione. 
        //  Dipende dal tipo di comandi
    
    } // fine funzione path

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funzione visualizza_path

    // VISUALIZZA_PATH:
    //  funzione per la gestione dei path: gli viene passata una struttura (shape) che contiene tutti i
    //      comandi e i rispettivi valori del path, questi vengono convertiti e tradotti in un insieme
    //      di punti, inseriti nella struttura path, che saranno rappresentati mediante 
    //      la funzione per la visualizzazione di un path (visualizza_segmento_path).
    //
    function visualizza_path_vml(&$image, $shape, $colori_vml){ 
    
        $tr_x = $shape["x"]; $tr_y = $shape["y"];
        $vb_x = $shape["w"] / $shape["vb_x"]; $vb_y = $shape["h"] / $shape["vb_y"];
    
        $j = 0;
        $last_point_x = $tr_x * $vb_x;
        $last_point_y = $tr_y * $vb_y;
        $first_point_x = $last_point_x;
        $first_point_y = $last_point_y;

        $start_x = 0;
        $start_y = 0;

        $path["n_punti"] = 2;
        $path["punti"][0] = $last_point_x;
        $path["punti"][1] = $last_point_y;

        $fill_path = 1;
        $stroke_path = 1;

        // analizziamo tutti i path
        while($j < $shape["path"]["n_comandi"]){

            // andiamo per casi
            
            ////////////// Comando M ///////////////
            if (($shape["path"]["comando"][$j] == "m") or ($shape["path"]["comando"][$j] == "M")){
                if($path["n_punti"] > 2){
                    visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
                
                    $path["n_punti"] = 2;
                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;
                }

                if($shape["path"]["n_valori"][$j] >= 2){
                    $start_x = $shape["path"]["valori"][$j][0];
                    $start_y = $shape["path"]["valori"][$j][1];
                    
                    $first_point_x = ($shape["path"]["valori"][$j][0] * $vb_x) + $tr_x;
                    $first_point_y = ($shape["path"]["valori"][$j][1] * $vb_y) + $tr_y;
                    $last_point_x = $first_point_x;
                    $last_point_y = $first_point_y;
                            
                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;
                }

                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";            
            } // fine M
          
            ////////////// Comando T ///////////////          
            if (($shape["path"]["comando"][$j] == "t") or ($shape["path"]["comando"][$j] == "T")){
          
                if($path["n_punti"] > 2){
                    visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
                
                    $path["n_punti"] = 2;
                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;
                }

                if($shape["path"]["n_valori"][$j] >= 2){
                
                    $x_agg = 0; $y_agg = 0;
                    if($j > 1){
                        if($vb_x > 0){ $x_agg = ($last_point_x - $tr_x) / $vb_x; }
                        if($vb_y > 0){ $y_agg = ($last_point_y - $tr_y) / $vb_y; }
                    }        
                
                    $start_x = $shape["path"]["valori"][$j][0];
                    $start_y = $shape["path"]["valori"][$j][1];             
                
                    $first_point_x = (($shape["path"]["valori"][$j][0] + $x_agg) * $vb_x) + $tr_x;
                    $first_point_y = (($shape["path"]["valori"][$j][1] + $y_agg) * $vb_y) + $tr_y;

                    $last_point_x = $first_point_x;
                    $last_point_y = $first_point_y;

                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;
                }

                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";            
            } // fine T
          
            ////////////// Comando L ///////////////          
            if (($shape["path"]["comando"][$j] == "l") or ($shape["path"]["comando"][$j] == "L")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
               
                    $np = $path["n_punti"];
                    $path["punti"][$np]     = $x_val;
                    $path["punti"][$np + 1] = $y_val;
                    $path["n_punti"] += 2;                      
               
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 2; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine L
      
            ////////////// Comando R ///////////////      
            if (($shape["path"]["comando"][$j] == "r") or ($shape["path"]["comando"][$j] == "R")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){
                    $x_agg = 0; $y_agg = 0;
                    if($vb_x > 0){ $x_agg = ($last_point_x - $tr_x) / $vb_x; }
                    if($vb_y > 0){ $y_agg = ($last_point_y - $tr_y) / $vb_y; }
    
                    $x_val = (($shape["path"]["valori"][$j][$k    ] + $x_agg) * $vb_x) + $tr_x;
                    $y_val = (($shape["path"]["valori"][$j][$k + 1] + $y_agg) * $vb_y) + $tr_y;
              
                    $np = $path["n_punti"];
                    $path["punti"][$np]     = $x_val;
                    $path["punti"][$np + 1] = $y_val;
                    $path["n_punti"] += 2;
                              
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 2; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine R

            ////////////// Comando C ///////////////
            if (($shape["path"]["comando"][$j] == "c") or ($shape["path"]["comando"][$j] == "C")){
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
                if($shape["path"]["n_valori"][$j] >= 6){
                    $k = 0;
                    while($k < $shape["path"]["n_valori"][$j]){

                        $old_last_x = $last_point_x;
                        $old_last_y = $last_point_y;
                                   
                        $x_control_1 = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                        $y_control_1 = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
                        $x_control_2 = ($shape["path"]["valori"][$j][$k + 2] * $vb_x) + $tr_x;
                        $y_control_2 = ($shape["path"]["valori"][$j][$k + 3] * $vb_y) + $tr_y;
               
                        $last_point_x = ($shape["path"]["valori"][$j][$k + 4] * $vb_x) + $tr_x;
                        $last_point_y = ($shape["path"]["valori"][$j][$k + 5] * $vb_y) + $tr_y;

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
                    
                            $punto = cubicbezier_vml($p1, $p2, $p3, $p4,$cb);
                    
                            $np = $path["n_punti"];
                            $path["punti"][$np]     = $punto["x"];
                            $path["punti"][$np + 1] = $punto["y"];
                                $path["n_punti"] += 2;
 
                            $cb += 0.1;
                        }

                        $k += 6; 
                    }
                }
            } // fine C

            ////////////// Comando V ///////////////
            if (($shape["path"]["comando"][$j] == "v") or ($shape["path"]["comando"][$j] == "V")){
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
                if($shape["path"]["n_valori"][$j] >= 6){
                    $k = 0;
                    while($k < $shape["path"]["n_valori"][$j]){
                
                        $x_agg = 0; $y_agg = 0;
                        if($vb_x > 0){ $x_agg = ($last_point_x - $tr_x) / $vb_x; }
                        if($vb_y > 0){ $y_agg = ($last_point_y - $tr_y) / $vb_y; }
                   
                        $old_last_x = $last_point_x;
                        $old_last_y = $last_point_y;
                                   
                        $x_control_1 = (($shape["path"]["valori"][$j][$k    ] + $x_agg) * $vb_x) + $tr_x;
                        $y_control_1 = (($shape["path"]["valori"][$j][$k + 1] + $y_agg) * $vb_y) + $tr_y;
                        $x_control_2 = (($shape["path"]["valori"][$j][$k + 2] + $x_agg) * $vb_x) + $tr_x;
                        $y_control_2 = (($shape["path"]["valori"][$j][$k + 3] + $y_agg) * $vb_y) + $tr_y;
               
                        $last_point_x = (($shape["path"]["valori"][$j][$k + 4] + $x_agg) * $vb_x) + $tr_x;
                        $last_point_y = (($shape["path"]["valori"][$j][$k + 5] + $y_agg) * $vb_y) + $tr_y;
 
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
                    
                            $punto = cubicbezier_vml($p1, $p2, $p3, $p4,$cb);
                    
                            $np = $path["n_punti"];
                            $path["punti"][$np]     = $punto["x"];
                            $path["punti"][$np + 1] = $punto["y"];
                            $path["n_punti"] += 2;
 
                            $cb += 0.1;
                        }
                        $k += 6; 
                    }
                }
            } // fine V
          
            ////////////// Comando QX ///////////////          
            if (($shape["path"]["comando"][$j] == "qx") or ($shape["path"]["comando"][$j] == "QX")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
               
                    $p1["x"] = $last_point_x;
                    $p1["y"] = $last_point_y;
                    
                    $p2["x"] = $x_val;
                    $p2["y"] = $last_point_y;
                                   
                    $p3["x"] = $x_val;
                    $p3["y"] = $y_val;
                 
                    $cb = 0;
                    while($cb < 1){
                        $punto = bezier3_vml($p1, $p2, $p3, $cb);
                    
                        $np = $path["n_punti"];
                        $path["punti"][$np]     = $punto["x"];
                        $path["punti"][$np + 1] = $punto["y"];
                        $path["n_punti"] += 2;
 
                        $cb += 0.1;
                    }
               
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                   $k += 2; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine QX

            ////////////// Comando QY ///////////////
            if (($shape["path"]["comando"][$j] == "qy") or ($shape["path"]["comando"][$j] == "QY")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                   $x_val = ($shape["path"]["valori"][$j][$k] * $vb_x) + $tr_x;
                   $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;

                   $p1["x"] = $last_point_x;
                   $p1["y"] = $last_point_y;
                    
                   $p2["x"] = $last_point_x;
                   $p2["y"] = $y_val;
                                   
                   $p3["x"] = $x_val;
                   $p3["y"] = $y_val;
                 
                   $cb = 0;
                   while($cb < 1){
                        $punto = bezier3_vml($p1, $p2, $p3, $cb);
                    
                        $np = $path["n_punti"];
                        $path["punti"][$np]     = $punto["x"];
                        $path["punti"][$np + 1] = $punto["y"];
                        $path["n_punti"] += 2;
 
                        $cb += 0.1;
                   }
               
                   $last_point_x = $x_val;
                   $last_point_y = $y_val;
           
                   $k += 2; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine QY

            ////////////// Comando QB ///////////////
            if (($shape["path"]["comando"][$j] == "qb") or ($shape["path"]["comando"][$j] == "QB")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    //$x_control_1 = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $last_point_x;
                    //$y_control_1 = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $last_point_y;

                    $x_control_1 = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_control_1 = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;

                    $x_val = ($shape["path"]["valori"][$j][$k + 2] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 3] * $vb_y) + $tr_y;
               
                    $p1["x"] = $last_point_x;
                    $p1["y"] = $last_point_y;
                    
                    $p2["x"] = $x_control_1;
                    $p2["y"] = $y_control_1;
                                   
                    $p3["x"] = $x_val;
                    $p3["y"] = $y_val;
                 
                    $cb = 0;
                    while($cb < 1){                    
                        $punto = bezier3_vml($p1, $p2, $p3, $cb);
                    
                        $np = $path["n_punti"];
                        $path["punti"][$np]     = $punto["x"];
                        $path["punti"][$np + 1] = $punto["y"];
                        $path["n_punti"] += 2;
 
                        $cb += 0.1;
                    }
               
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 4; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine QX

            ////////////// Comando AE ///////////////
            if (($shape["path"]["comando"][$j] == "ae") or ($shape["path"]["comando"][$j] == "AE")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;

                    $x_val += ($shape["path"]["valori"][$j][$k + 2] * $vb_x);
                    //$y_val += ($shape["path"]["valori"][$j][$k + 3] * $vb_y);
           
                    $np = $path["n_punti"];
                    $path["punti"][$np]     = $x_val;
                    $path["punti"][$np + 1] = $y_val;
                    $path["n_punti"] += 2;           
               
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 6; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine AE

            ////////////// Comando AL ///////////////
            if (($shape["path"]["comando"][$j] == "al") or ($shape["path"]["comando"][$j] == "AL")){
                if($path["n_punti"] > 2){
                    visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
                
                    $path["n_punti"] = 2;
                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;    
                }
                
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){
                
                    $last_point_x  = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $last_point_y  = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
                    $last_point_x += ($shape["path"]["valori"][$j][$k + 2] * $vb_x);

                    $path["n_punti"] = 2;
                    $path["punti"][0] = $last_point_x;
                    $path["punti"][1] = $last_point_y;

                    $first_point_x = $last_point_x;
                    $first_point_y = $last_point_y;
    
                    $k += 6; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine AL

            ////////////// Comando AT ///////////////
            if (($shape["path"]["comando"][$j] == "at") or ($shape["path"]["comando"][$j] == "AT")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    $x_val = ($shape["path"]["valori"][$j][$k]     * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
                
                    $first_line_x = (($shape["path"]["valori"][$j][$k + 2] - 
                    $shape["path"]["valori"][$j][$k    ]) * $vb_x) + $x_val;
                    $first_line_y = $y_val;

                    $second_line_x = $first_line_x;
                    $second_line_y = (($shape["path"]["valori"][$j][$k + 3] - 
                    $shape["path"]["valori"][$j][$k + 1]) * $vb_y) + $y_val;
               
                    $third_line_x = $x_val;
                    $third_line_y = $second_line_y;

                    $fourth_line_x = $x_val;
                    $fourth_line_y = $y_val;
                       
                    $np = $path["n_punti"];
                    $path["punti"][$np    ] = $x_val;
                    $path["punti"][$np + 1] = $y_val;
               
                    $path["punti"][$np + 2] = $first_line_x;
                    $path["punti"][$np + 3] = $first_line_y;
                    $path["punti"][$np + 4] = $second_line_x;
                    $path["punti"][$np + 5] = $second_line_y;
                    $path["punti"][$np + 6] = $third_line_x;
                    $path["punti"][$np + 7] = $third_line_y;
                    $path["punti"][$np + 8] = $fourth_line_x;
                    $path["punti"][$np + 9] = $fourth_line_y;
                    $path["punti"][$np + 10] = $first_line_x;
                    $path["punti"][$np + 11] = $first_line_y;
               
                    $path["n_punti"] += 12;
                                 
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 8; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine AT

            ////////////// Comando AR ///////////////
            if (($shape["path"]["comando"][$j] == "ar") or ($shape["path"]["comando"][$j] == "AR")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){
                    visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
                             
                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;

                    $path["n_punti"] = 2;
                    $path["punti"][0] = $x_val;
                    $path["punti"][1] = $y_val;
               
                    $first_line_x = (($shape["path"]["valori"][$j][$k + 2] - 
                    $shape["path"]["valori"][$j][$k    ]) * $vb_x) + $x_val;
                    $first_line_y = $y_val;

                    $second_line_x = $first_line_x;
                    $second_line_y = (($shape["path"]["valori"][$j][$k + 3] - 
                    $shape["path"]["valori"][$j][$k + 1]) * $vb_y) + $y_val;
               
                    $third_line_x = $x_val;
                    $third_line_y = $second_line_y;

                    $fourth_line_x = $x_val;
                    $fourth_line_y = $y_val;
                       
                    $np = $path["n_punti"];
               
                    $path["punti"][$np    ] = $first_line_x;
                    $path["punti"][$np + 1] = $first_line_y;
                    $path["punti"][$np + 2] = $second_line_x;
                    $path["punti"][$np + 3] = $second_line_y;
                    $path["punti"][$np + 4] = $third_line_x;
                    $path["punti"][$np + 5] = $third_line_y;
                    $path["punti"][$np + 6] = $fourth_line_x;
                    $path["punti"][$np + 7] = $fourth_line_y;
                    $path["punti"][$np + 8] = $first_line_x;
                    $path["punti"][$np + 9] = $first_line_y;

                    $path["n_punti"] += 10;           
               
                    $last_point_x = $first_point_x;
                    $last_point_y = $first_point_y;

                    //$first_point_x = $x_val;
                    //$first_point_y = $y_val;

                    $k += 8; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine AR

            ////////////// Comando WA ///////////////
            if (($shape["path"]["comando"][$j] == "wa") or ($shape["path"]["comando"][$j] == "WA")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){

                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;
               
                    $first_line_x = (($shape["path"]["valori"][$j][$k + 2] - 
                    $shape["path"]["valori"][$j][$k]) * $vb_x) + $x_val;
                    $first_line_y = $y_val;

                    $second_line_x = $first_line_x;
                    $second_line_y = (($shape["path"]["valori"][$j][$k + 3] - 
                    $shape["path"]["valori"][$j][$k + 1]) * $vb_y) + $y_val;
               
                    $third_line_x = $x_val;
                    $third_line_y = $second_line_y;

                    $fourth_line_x = $x_val;
                    $fourth_line_y = $y_val;
                       
                    $np = $path["n_punti"];
                    $path["punti"][$np]     = $x_val;
                    $path["punti"][$np + 1] = $y_val;
               
                    $path["punti"][$np + 2] = $first_line_x;
                    $path["punti"][$np + 3] = $first_line_y;
                    $path["punti"][$np + 4] = $second_line_x;
                    $path["punti"][$np + 5] = $second_line_y;
                    $path["punti"][$np + 6] = $third_line_x;
                    $path["punti"][$np + 7] = $third_line_y;
                    $path["punti"][$np + 8] = $fourth_line_x;
                    $path["punti"][$np + 9] = $fourth_line_y;
                    $path["punti"][$np + 10] = $first_line_x;
                    $path["punti"][$np + 11] = $first_line_y;
               
                    $path["n_punti"] += 12;
               
                    $last_point_x = $x_val;
                    $last_point_y = $y_val;
           
                    $k += 8; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine WA

            ////////////// Comando WR ///////////////
            if (($shape["path"]["comando"][$j] == "wr") or ($shape["path"]["comando"][$j] == "WR")){
                $k = 0;
                while($k < $shape["path"]["n_valori"][$j]){
                    visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
                             
                    $x_val = ($shape["path"]["valori"][$j][$k    ] * $vb_x) + $tr_x;
                    $y_val = ($shape["path"]["valori"][$j][$k + 1] * $vb_y) + $tr_y;

                    $path["n_punti"] = 2;
                    $path["punti"][0] = $x_val;
                    $path["punti"][1] = $y_val;
               
                    $first_line_x = (($shape["path"]["valori"][$j][$k + 2] - 
                    $shape["path"]["valori"][$j][$k]) * $vb_x) + $x_val;
                    $first_line_y = $y_val;

                    $second_line_x = $first_line_x;
                    $second_line_y = (($shape["path"]["valori"][$j][$k + 3] - 
                    $shape["path"]["valori"][$j][$k + 1]) * $vb_y) + $y_val;
               
                    $third_line_x = $x_val;
                    $third_line_y = $second_line_y;

                    $fourth_line_x = $x_val;
                    $fourth_line_y = $y_val;
                       
                    $np = $path["n_punti"];
               
                    $path["punti"][$np    ] = $first_line_x;
                    $path["punti"][$np + 1] = $first_line_y;
                    $path["punti"][$np + 2] = $second_line_x;
                    $path["punti"][$np + 3] = $second_line_y;
                    $path["punti"][$np + 4] = $third_line_x;
                    $path["punti"][$np + 5] = $third_line_y;
                    $path["punti"][$np + 6] = $fourth_line_x;
                    $path["punti"][$np + 7] = $fourth_line_y;
                    $path["punti"][$np + 8] = $first_line_x;
                    $path["punti"][$np + 9] = $first_line_y;

                    $path["n_punti"] += 10;
                           
                    $last_point_x = $first_point_x;
                    $last_point_y = $first_point_y;

                    //$first_point_x = $x_val;
                    //$first_point_y = $y_val;

                    $k += 8; 
                }
                // ??
                $x_control_1 = ""; $x_control_2 = ""; $y_control_1 = ""; $y_control_2 = "";
            } //fine WR

            ////////////// Comando X ///////////////
            if (($shape["path"]["comando"][$j] == "x") or ($shape["path"]["comando"][$j] == "X")){            
            
                if(($last_point_x != $first_point_x) or ($last_point_y != $first_point_y)){
                    //$last_point_x = $first_point_x;
                    //$last_point_y = $first_point_y;
                
                    $np = $path["n_punti"];
                    $path["punti"][$np    ] = $first_point_x;
                    $path["punti"][$np + 1] = $first_point_y;
                    $path["n_punti"] += 2;
                }
            } //fine X

            ////////////// Comando NF ///////////////
            if (($shape["path"]["comando"][$j] == "nf") or ($shape["path"]["comando"][$j] == "NF")){
                // visualizza path
                visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);

                $path["n_punti"] = 2;
                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;

                $fill_path = 0; 
            } //fine NF

            ////////////// Comando NS ///////////////
            if (($shape["path"]["comando"][$j] == "ns") or ($shape["path"]["comando"][$j] == "NS")){
                // visualizza path
                visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);

                $path["n_punti"] = 2;
                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;

                $stroke_path = 0; 
            } //fine NS

            ////////////// Comando E ///////////////
            if (($shape["path"]["comando"][$j] == "e") or ($shape["path"]["comando"][$j] == "E")){
                // visualizza path
                visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);

                $fill_path = 1;
                $stroke_path = 1;           

                $path["n_punti"] = 2;
                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;

            } //fine E

            ////////////// Comando XE ///////////////
            if (($shape["path"]["comando"][$j] == "xe") or ($shape["path"]["comando"][$j] == "XE")){

                //$last_point_x = $first_point_x;
                //$last_point_y = $first_point_y;
                
                $np = $path["n_punti"];
                $path["punti"][$np    ] = $first_point_x;
                $path["punti"][$np + 1] = $first_point_y;
                $path["n_punti"] += 2;

                visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);

                $fill_path = 1;
                $stroke_path = 1;

                $path["n_punti"] = 2;
                $path["punti"][0] = $last_point_x;
                $path["punti"][1] = $last_point_y;         
            } //fine XE

            $j += 1;
        } // fine while

        if($path["n_punti"] > 2){
            visualizza_segmento_path_vml($image, $path, $shape, $colori_vml, $fill_path, $stroke_path);
        }
    } // fine funzione visualizza_path

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // VISUALIZZA_SEGMENTO_PATH:
    //    visualizza una porzione del path, trattandolo come fosse un poligono
    //
    function visualizza_segmento_path_vml(&$image, &$path, &$shape, $colori_vml, $fill_path, $stroke_path){
    
        if(($shape["fill"] != "none") and ($fill_path == 1)){
            if($path["n_punti"] > 2){
               $np = $path["n_punti"];
               $path["punti"][$np] = $path["punti"][0];
               $path["punti"][$np + 1] = $path["punti"][1];
               $path["n_punti"] += 2;

               imagefilledpolygon($image,$path["punti"],$path["n_punti"] / 2, $colori_vml[$shape["fill"]]);

               $path["n_punti"] -= 2;
            }        
        }
        
        if(($shape["stroke"] != "none") and ($stroke_path == 1)){

            $k = - ($shape["stroke_w"] / 2);
            while($k < ($shape["stroke_w"] / 2)){
                $j = 0;
                while ($j < ($path["n_punti"] - 2)){
                
                    // GESTIONE ANGOLI //
                    gestione_angoli_vml($path["punti"][$j    ],$path["punti"][$j + 2],
                                    $path["punti"][$j + 1],$path["punti"][$j + 3],
                                    $seno,$coseno);

                    calcola_inversione_vml($path["punti"][$j    ],$path["punti"][$j + 2],
                                       $path["punti"][$j + 1],$path["punti"][$j + 3],
                                       $x1,$x2,$y1,$y2,$inv);

                    $y_val = $k * $inv * $seno;
                    $x_val = $k * $inv * $coseno;

                    if($j > 0){
                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori_vml[$shape["stroke"]]);    
                    }
                    else{
                        $punto_iniziale_x = $x1 + $x_val;
                        $punto_iniziale_y = $y1 + $y_val;
                    }

                    imageline($image,$x1 + $x_val, $y1 + $y_val,
                              $x2 + $x_val, $y2 + $y_val,
                              $colori_vml[$shape["stroke"]]);         

                    $j += 2;

                    $last_point_x = $x2 + $x_val;
                    $last_point_y = $y2 + $y_val;
                
                    if($j >= $path["n_punti"] - 2 ){
                        if(($path["punti"][0] == $path["punti"][$j    ]) and 
                           ($path["punti"][1] == $path["punti"][$j + 1])){  
                     
                            imageline($image,$last_point_x, $last_point_y,
                                      $punto_iniziale_x, $punto_iniziale_y,
                                      $colori_vml[$shape["stroke"]]);
                    
                        }
                    }                               
                }
                $k += 0.01;
            }
        }
    } // fine funzione visualizza_segmento_path
   
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      // GESTIONE_FILE:
      //    funzione principale che gestisce ogni riga del documento
      //
      function gestione_file_vml(&$image, &$riga, &$n_viewbox, &$viewbox_x, &$viewbox_y, &$viewbox_co_x, &$viewbox_co_y,
                            &$n_translate, &$livello, &$contenuto_shapetype, &$n_st, &$translate_x, &$translate_y,
                            &$n_stroke, &$n_fill, &$fill, &$stroke, &$fill_livello, &$stroke_livello, &$stroke_width,
                            &$w, &$h, &$n_wh, &$w_val, &$h_val, &$translate_livello, &$viewbox_livello, &$wh_livello, 
                            $colori_vml, $primo_group, &$in_text, &$testo, &$n_testi, &$path_comando, &$path_valori, 
                            &$path_n_valori, &$path_n_comandi, &$x_text, &$y_text, &$n_font, &$font_s, &$font_livello, 
                            &$predef, &$on_predef, &$shape, &$on_shape, &$fill_on, &$stroke_on, &$fill_color, &$stroke_color, 
                            &$in_textbox, $url_base){


    // impostazione di alcune variabili globali, tra cui la dimensione corrente del font
    $font_temp = $font_s[$n_font];
    $fine_shape = 0;
    $visualizza_predef = "no";
    
    ////////////////////////////////////////////////////////////////////    
    // imposto due variabili che contengono i valori a cui si 
    //  riferiscono i valori espressi in % 
    //
    if($n_viewbox == 0){
      $perc_x = $primo_group["w"];
    }
    elseif($n_viewbox == 1){
      if($primo_group["is_coordsize"] == "no"){ $perc_x = $primo_group["w"]; }
      else{ $perc_x = $viewbox_x[$n_viewbox - 1]; }
    }
    else{ $perc_x = $viewbox_x[$n_viewbox - 1]; }
    
    if($n_viewbox == 0){
      $perc_y = $primo_group["h"];
    }
    elseif($n_viewbox == 1){
      if($primo_group["is_coordsize"] == "no"){ $perc_y = $primo_group["h"]; }
      else{ $perc_y = $viewbox_y[$n_viewbox - 1]; }
    }
    else{ $perc_y = $viewbox_y[$n_viewbox - 1]; }
    ///////////////////////////////////////////////////////////////////
    
        
    ///////////////////////////////////
    ///  Aumento il livello dei TAG ///
    //////////////////////////////////
    if(preg_match("/(.*<\/.*)/",$riga)){
    }
    elseif(preg_match("/(.*<v:.*)/",$riga)){
        $livello += 1;
    }
        
    ///////////////////////////////////////////////////
    // gestione group ////////////////////////////////
    //////////////////////////////////////////////////
    if(preg_match("/(.*<v:group.*)/",$riga)){
      // da fare due cose: 1. gestione x,y,
      //                   2. coordsize: se non c'e' si imposta a 1000, 1000
         
      if(preg_match("/(.* style=.*)/",$riga)){     
          $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*[\"|'].*)/",$style)){
             $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
          }
          $style = " ".$style;
          
          // gestione WIDTH       
          $w_temp = 0;
          if($primo_group["in"] == "si"){ $w_temp = 750; }
          if(preg_match("/(.*[ |;]width\s?:.*)/",$style)){
             $w_temp = preg_replace("/(.*[ |;]width\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$w_temp)){
                 while(preg_match("/(.*;.*)/",$w_temp)){
                       $w_temp = preg_replace("/(.*?)(;.*)/","\$1",$w_temp);
                  }
             }        
             $w_temp = preg_replace("/\s/","",$w_temp);
             $w_temp = converti_vml($w_temp,$font_temp,$perc_x, $livello); 
          }  

          // gestione HEIGHT
          $h_temp = 0;
          if($primo_group["in"] == "si"){ $h_temp = 400; }
          if(preg_match("/(.*[ |;]height\s?:.*)/",$style)){
             $h_temp = preg_replace("/(.*[ |;]height\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$h_temp)){
                 while(preg_match("/(.*;.*)/",$h_temp)){
                       $h_temp = preg_replace("/(.*)(;.*)/","\$1",$h_temp);
                  }
             }
             $h_temp = preg_replace("/\s/","",$h_temp);
             $h_temp = converti_vml($h_temp,$font_temp,$perc_y, $livello);              
          }

          // se c'e' top o left (o entrambi) impostiamo la translazione
          if((preg_match("/(.*[ |;]left\s?:.*)/",$style)) or (preg_match("/(.*[ |;]top\s?:.*)/",$style))){
          
            // gestione LEFT
            $x_temp = 0;
            if($livello == 1){
                $x_temp = 10;
            }
                
            if(preg_match("/(.*[ |;]left\s?:.*)/",$style)){
                $x_temp = preg_replace("/(.*[ |;]left\s?:)(.*?)/","\$2",$style);
                if(preg_match("/(.*;.*)/",$x_temp)){
                    while(preg_match("/(.*;.*)/",$x_temp)){
                            $x_temp = preg_replace("/(.*)(;.*)/","\$1",$x_temp);
                    }
                }
                $x_temp = preg_replace("/\s/","",$x_temp);
                $x_temp = converti_vml($x_temp,$font_temp,$perc_x, $livello); 
            }  
            
            // gestione TOP
            $y_temp = 0;
            if($livello == 1){
              $y_temp = 15;
            }

            if(preg_match("/(.*[ |;]top\s?:.*)/",$style)){
                $y_temp = preg_replace("/(.*[ |;]top\s?:)(.*?)/","\$2",$style);
                if(preg_match("/(.*;.*)/",$y_temp)){
                    while(preg_match("/(.*;.*)/",$y_temp)){
                            $y_temp = preg_replace("/(.*)(;.*)/","\$1",$y_temp);
                    }
                }                                 
                $y_temp = preg_replace("/\s/","",$y_temp);
                $y_temp = converti_vml($y_temp,$font_temp,$perc_y, $livello);              
            }                 
        
            $translate_x[$n_translate] = $x_temp;
            $translate_y[$n_translate] = $y_temp;
               
            $translate_livello[$n_translate] = $livello;
            $n_translate += 1;              
          }
          
          elseif($livello == 1){
            // left e top per il primo elemento hanno il valore di default 
            //      diverso da 0.
            $x_temp = 10; 
            $y_temp = 15;   
            
            $translate_x[$n_translate] = $x_temp;
            $translate_y[$n_translate] = $y_temp;
               
            $translate_livello[$n_translate] = $livello;
            $n_translate += 1;
          }
      }
      // non c'e' style
      else{
        // non imposto translate
        // imposto w,h: dovrebbero essere uguali a 0
      
        $w_temp = 0;
        if($primo_group["in"] == "si"){ $w_temp = 750; }
        
        $h_temp = 0;
        if($primo_group["in"] == "si"){ $h_temp = 400; }

        // x e y non vengono impostati
      }

      // GESITIONE coordorigin e coordsize (viewbox)
      //
      // COORDSIZE:
      // valori di default di viewbox (coordsize)
      if($n_viewbox > 0){
        $vb_x = $viewbox_x[$n_viewbox - 1];
        $vb_y = $viewbox_y[$n_viewbox - 1];
      }
      else{
        $vb_x = 1000; 
        $vb_y = 1000;
      }
       
      if(preg_match("/(.* coordsize=.*)/",$riga)){    
       if($primo_group["in"] == "si"){ $primo_group["is_coordsize"] = "si"; }
            $vb = preg_replace("/(.* coordsize=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$vb)){
              $vb = preg_replace("/(.*)(\".*)/","\$1",$vb);
            }
            
            if(preg_match("/(.*,.*)/",$vb)){    
                $vb_x = preg_replace("/(.*)(,)(.*)/","\$1",$vb);
                $vb_x = preg_replace("/\s/","",$vb_x);

                $vb_y = preg_replace("/(.*)(,)(.*)/","\$3",$vb);
                $vb_y = preg_replace("/\s/","",$vb_y);
            }
            else{
                $vb_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$vb);
                $vb_x = preg_replace("/\s/","",$vb_x);
                
                $vb_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$vb);
                $vb_y = preg_replace("/\s/","",$vb_y);
            }
      }
    
      // COORDORIGIN:
      // imposto coordorigin: la tratto come una traslazione
      $co_x = 0; $co_y = 0;
      if($n_viewbox > 0){
        $co_x = (-1) * $viewbox_co_x[$n_viewbox - 1];
        $co_y = (-1) * $viewbox_co_y[$n_viewbox - 1];
      }
      else{
        $co_x = 0; 
        $co_y = 0;
      }
      if(preg_match("/(.* coordorigin=.*)/",$riga)){     
        $co = preg_replace("/(.* coordorigin=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$co)){
              $co = preg_replace("/(.*)(\".*)/","\$1",$co);
        }
        if(preg_match("/(.*,.*)/",$co)){    
            $co_x = preg_replace("/(.*)(,)(.*)/","\$1",$co);
            $co_x = preg_replace("/\s/","",$co_x);

            $co_y = preg_replace("/(.*)(,)(.*)/","\$3",$co);
            $co_y = preg_replace("/\s/","",$co_y);
        }
        else{
            $co_x = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$1",$co);
            $co_x = preg_replace("/\s/","",$co_x);

            $co_y = preg_replace("/(-?\d+\.?\d*)(\s+)(-?\d+\.?\d*)/","\$3",$co);
            $co_y = preg_replace("/\s/","",$co_y);
        }

        $co_x = -1 * $co_x;
        $co_y = -1 * $co_y;       
      }
    
      // imposto le pile che mantengono le traslazioni e i dimensionamenti (definiti 
      //      da coordsize, width e height)
    
      // imposto la traslazione
      $translate_x[$n_translate] = $co_x * ($w_temp / $vb_x);
      $translate_y[$n_translate] = $co_y * ($h_temp / $vb_y);

      $translate_livello[$n_translate] = $livello;
      $n_translate += 1;

      // DEBUG
      //echo $vb_x." -- ".$vb_y."<br />";

      $viewbox_x[$n_viewbox] = $vb_x;
      $viewbox_y[$n_viewbox] = $vb_y;
      $viewbox_co_x[$n_viewbox] = $co_x * (-1);
      $viewbox_co_y[$n_viewbox] = $co_y * (-1);

      // DEBUG
      //echo "vb: w: ".$viewbox_x[$n_viewbox]." h: ".$viewbox_y[$n_viewbox]." x: ".$viewbox_co_x[$n_viewbox];
      //echo " y: ".$viewbox_co_y[$n_viewbox]."<br />";

      $viewbox_livello[$n_viewbox] = $livello;
      $n_viewbox += 1;
    
      $w_val[$n_wh] = $w_temp;
      $h_val[$n_wh] = $h_temp;
      $wh_livello[$n_wh] = $livello;
      $n_wh += 1;
    
      // DEBUG
      // echo "<br />_________".$w_temp." (".$viewbox_livello[$n_viewbox - 1].")____________<br />";
      // echo $w_temp." ".$h_temp."<br />";
        
    } // fine gestione group


    ////////////////////////////////////////////////
    // gestione RECT ///////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:rect.*)/",$riga)){
       $on_predef = 1;
       
       $predef["tipo"] = "rect";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";

       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);     
    
       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "rect";               
       }
    }// fine v:rect

    if(preg_match("/(.*<\/v:rect.*)/",$riga)){
        $visualizza_predef = "rect";
    } // fine rect (con elementi)
    
    if($visualizza_predef == "rect"){
       $visualizza_predef = "no";   
       // visualizzo il rettangolo
       if($predef["fill"] != "none"){
            imagefilledrectangle($image,($predef["x"]),($predef["y"]),($predef["x"] + $predef["w"]),
                                 ($predef["y"] + $predef["h"]),$colori_vml[$predef["fill"]]);
       }
       if($predef["stroke"] != "none"){
              // nb: stroke_w non e' scalato!   
              $j = - ($predef["stroke_w"] / 2);
              while($j < ($predef["stroke_w"] / 2)){          
                 imagerectangle($image,$predef["x"] + $j, $predef["y"] + $j,
                                $predef["x"] + $predef["w"] - $j,
                                $predef["y"] + $predef["h"] - $j,
                                $colori_vml[$predef["stroke"]]);
                 $j += 0.01;
              }
       }

       $fill_on = 1; $stroke_on = 1;
       $fill_color = "white"; $stroke_color = "black";
       $on_predef = 0;        
    } // fine visualizzazione rect


    ////////////////////////////////////////////////
    // gestione ROUNDRECT //////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:roundrect.*)/",$riga)){
       // approssimato: non vengono visualizzati gli angoli smussati!
       $on_predef = 1;
       
       $predef["tipo"] = "roundrect";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";

       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "roundrect";                      
       }
    }// fine v:roundrect

    if(preg_match("/(.*<\/v:roundrect.*)/",$riga)){
         $visualizza_predef = "roundrect";              
    } // fine roundrect (con elementi)
    
    if($visualizza_predef == "roundrect"){
        $visualizza_predef = "no";             

        // roundrect e' trattato come un path!
        // visualizzo il rettangolo (approssimato)  
        $shape["x"] = 0; $shape["y"] = 0;
        $shape["w"] = 1; $shape["h"] = 1; 
        $shape["vb_x"] = 1; $shape["vb_y"] = 1;
        
        $shape["fill"]     = $predef["fill"];
        $shape["stroke"]   = $predef["stroke"];
        $shape["stroke_w"] = $predef["stroke_w"];
       
        // imposto R
        if($predef["w"] < $predef["h"]){
            $r = ($predef["w"] / 2) * $predef["arc-size"];
        }
        else{
            $r = ($predef["h"] / 2) * $predef["arc-size"];
        }
        
        $shape["path"]["n_comandi"] = 9;
        
        // 1. MOVE TO (in altro a sx)   
        $shape["path"]["comando"][0]   = "M";
        $shape["path"]["n_valori"][0]  = 2;
        $shape["path"]["valori"][0][0] = $predef["x"] + $r;
        $shape["path"]["valori"][0][1] = $predef["y"];

        // 2. PRIMA LINEA (orizzontale alta) 
        $shape["path"]["comando"][1]   = "L";
        $shape["path"]["n_valori"][1]  = 2;
        $shape["path"]["valori"][1][0] = $predef["x"] + $predef["w"] - $r;
        $shape["path"]["valori"][1][1] = $predef["y"];

        // 3. PRIMO ARC (angolo alto dx)
        $shape["path"]["comando"][2]   = "QX";
        $shape["path"]["n_valori"][2]  = 2;
        $shape["path"]["valori"][2][0] = $predef["x"] + $predef["w"];
        $shape["path"]["valori"][2][1] = $predef["y"] + $r;

        // 4. SECONDA LINEA (verticale dx) 
        $shape["path"]["comando"][3]   = "L";
        $shape["path"]["n_valori"][3]  = 2;
        $shape["path"]["valori"][3][0] = $predef["x"] + $predef["w"];
        $shape["path"]["valori"][3][1] = $predef["y"] + $predef["h"] - $r;

        // 5. SECONDO ARC (angolo basso dx)
        $shape["path"]["comando"][4]   = "QY";
        $shape["path"]["n_valori"][4]  = 2;
        $shape["path"]["valori"][4][0] = $predef["x"] + $predef["w"] - $r;
        $shape["path"]["valori"][4][1] = $predef["y"] + $predef["h"];
     
        // 6. TERZA LINEA (orizzontale bassa)
        $shape["path"]["comando"][5]   = "L";
        $shape["path"]["n_valori"][5]  = 2;
        $shape["path"]["valori"][5][0] = $predef["x"] + $r;
        $shape["path"]["valori"][5][1] = $predef["y"] + $predef["h"];

        // 7. TERZO ARC (angolo basso sx)
        $shape["path"]["comando"][6]   = "QX";
        $shape["path"]["n_valori"][6]  = 2;
        $shape["path"]["valori"][6][0] = $predef["x"];
        $shape["path"]["valori"][6][1] = $predef["y"] + $predef["h"] - $r;
 
        // 8. QUARTA LINEA (verticale sx) 
        $shape["path"]["comando"][7]   = "L";
        $shape["path"]["n_valori"][7]  = 2;
        $shape["path"]["valori"][7][0] = $predef["x"];
        $shape["path"]["valori"][7][1] = $predef["y"] + $r;

        // 9. QUARTO ARC (angolo alto sx)
        $shape["path"]["comando"][8]   = "QY";
        $shape["path"]["n_valori"][8]  = 2;
        $shape["path"]["valori"][8][0] = $predef["x"] + $r;
        $shape["path"]["valori"][8][1] = $predef["y"];   

        visualizza_path_vml($image, $shape, $colori_vml);     

        $fill_on = 1; $stroke_on = 1;
        $fill_color = "white"; $stroke_color = "black";
        $on_predef = 0; 
    } // fine  visualizzazione roundrect


    ////////////////////////////////////////////////
    // gestione OVAL ///////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:oval.*)/",$riga)){
       $on_predef = 1;
    
       $predef["tipo"] = "oval";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";
       
       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);
    
       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "oval";                       
       }
    }// fine oval

    if(preg_match("/(.*<\/v:oval.*)/",$riga)){
        $visualizza_predef = "oval";                
    }  // fine oval (con elementi)

    if($visualizza_predef == "oval"){               
         $visualizza_predef = "no";             
         
         // visualizzo l'ellisse
         if($predef["fill"] != "none"){
            imagefilledellipse($image,($predef["x"] + ($predef["w"] / 2)),
                               ($predef["y"] + ($predef["h"] / 2)), ($predef["w"]),
                               ($predef["h"]),$colori_vml[$predef["fill"]]);

         }
         if($predef["stroke"] != "none"){
            // DEBUG
            //echo $shape["tipo"]." -- ".$shape["x"]." -- ".$shape["y"];
            
            $j = - ($predef["stroke_w"] / 2);
            while($j < ($predef["stroke_w"] / 2)){          
                 imageellipse($image,$predef["x"] + ($predef["w"] / 2), 
                              $predef["y"] + ($predef["h"] / 2),
                              $predef["w"] + ($j * 2),
                              $predef["h"] + ($j * 2),
                              $colori_vml[$predef["stroke"]]);
                 $j += 0.01;
            }
         }

         $fill_on = 1; $stroke_on = 1;
         $fill_color = "white"; $stroke_color = "black";
         $on_predef = 0;        
    } // fine visualizzazione oval (con elementi)


    ////////////////////////////////////////////////
    // gestione LINE ///////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:line.*)/",$riga)){
       $on_predef = 1;
    
       $predef["tipo"] = "line";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";
       
       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

      // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
      if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "line";                       
      }
    }// fine line

    if(preg_match("/(.*<\/v:line.*)/",$riga)){
         $visualizza_predef = "line";               
    }  // fine line (con elementi)

    if($visualizza_predef == "line"){
        $visualizza_predef = "no";
        // NB: line non supporta fill (ovvio).
        // visualizzo la linea
        
        if($predef["stroke"] != "none"){
        
            // DEBUG
            //echo $shape["tipo"]." -- ".$shape["x"]." -- ".$shape["y"];        
            //echo "x1: ".ceil($predef["x1"])." x2: ".ceil($predef["x2"])."<br />";
            //echo "y1: ".ceil($predef["y1"])." y2: ".ceil($predef["y2"])."<br />";

            gestione_angoli_vml($predef["x1"],$predef["x2"],$predef["y1"],$predef["y2"],$seno,$coseno);               
            
            $j = - ($predef["stroke_w"] / 2);
            while($j < ($predef["stroke_w"] / 2)){    

                calcola_inversione_vml($predef["x1"],$predef["x2"],
                                   $predef["y1"],$predef["y2"],
                                   $x1,$x2,$y1,$y2,$inv);
                                   
                $y_val =  $j * $inv * $seno;
                $x_val =  $j * $inv * $coseno;

                
                imageline($image,$x1 + $x_val, $y1 + $y_val,
                          $x2 + $x_val, $y2 + $y_val,
                          $colori_vml[$predef["stroke"]]);
                          
                $j += 0.01;
            }            
        }

        $fill_on = 1; $stroke_on = 1;
        $fill_color = "white"; $stroke_color = "black";
        $on_predef = 0;        
    } // fine visualizzazione line

    ////////////////////////////////////////////////
    // gestione POLYLINE ///////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:polyline.*)/",$riga)){
       $on_predef = 1;
    
       $predef["tipo"] = "polyline";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";
       
       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
          // non ci sono fill e stroke (elementi)
          $visualizza_predef = "polyline";                   
       }
    }// fine polyline

    if(preg_match("/(.*<\/v:polyline.*)/",$riga)){
        $visualizza_predef = "polyline";
    } // fine polyline (con elementi)

    if($visualizza_predef == "polyline"){       
        $visualizza_predef = "no";
        
        // visualizzo le linee
        if($predef["fill"] != "none"){
            imagefilledpolygon($image,$predef["punti"],$predef["n_punti"] / 2,$colori_vml[$predef["fill"]]);
        } 
        if($predef["stroke"] != "none"){
        
            $k = - ($predef["stroke_w"] / 2);
            while($k < ($predef["stroke_w"] / 2)){
            
                $j = 0;
                while ($j < ($predef["n_punti"] - 2)){

                    // GESTIONE ANGOLI //
                    gestione_angoli_vml($predef["punti"][$j    ],$predef["punti"][$j + 2],
                                    $predef["punti"][$j + 1],$predef["punti"][$j + 3],
                                    $seno,$coseno);

                    calcola_inversione_vml($predef["punti"][$j    ],$predef["punti"][$j + 2],
                                       $predef["punti"][$j + 1],$predef["punti"][$j + 3],
                                       $x1,$x2,$y1,$y2,$inv);

                    $y_val = $k * $inv * $seno;
                    $x_val = $k * $inv * $coseno;

                    if($j > 0){
                        imageline($image,$last_point_x, $last_point_y,
                                  $x1 + $x_val, $y1 + $y_val,
                                  $colori_vml[$predef["stroke"]]);        
                    }
                    else{
                        $punto_iniziale_x = $x1 + $x_val;
                        $punto_iniziale_y = $y1 + $y_val;
                    }

                    imageline($image,$x1 + $x_val, $y1 + $y_val,
                              $x2 + $x_val, $y2 + $y_val,
                              $colori_vml[$predef["stroke"]]);        

                    $j += 2;

                    $last_point_x = $x2 + $x_val;
                    $last_point_y = $y2 + $y_val;
                
                    if($j > $predef["n_punti"] - 2 ){
                        if(($punti[0] == $punti[$j]) and ($punti[1] == $punti[$j + 1])){                                     
                         imageline($image,$last_point_x, $last_point_y,
                                   $punto_iniziale_x, $punto_iniziale_y,
                                   $colori_vml[$predef["stroke"]]);                
                        }
                    }
                }
                $k += 0.01;
            }        
        }

        $fill_on = 1; $stroke_on = 1;
        $fill_color = "white"; $stroke_color = "black";
        $on_predef = 0;        
    } // fine visualizzazione polyline


    ////////////////////////////////////////////////
    // gestione ARC ////////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:arc.*)/",$riga)){
       $on_predef = 1;
       
       $predef["tipo"] = "arc";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";

       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "arc";                
       }
    }// fine v:arc

    if(preg_match("/(.*<\/v:arc.*)/",$riga)){
        $visualizza_predef = "arc";
    } // fine arc (con elementi)
    
    if($visualizza_predef == "arc"){
        $visualizza_predef = "no";
       
        if($predef["start-angle"] < $predef["end-angle"]){
            $start =  270  + $predef["start-angle"];
            $end   =  270  + $predef["end-angle"];
        }
        else{
            $end    = 270 + $predef["start-angle"];
            $start  = 270 + $predef["end-angle"];
        }
       
        // visualizzo l'arco
        if($predef["fill"] != "none"){
             imagefilledarc($image, $predef["x"] + ($predef["w"] / 2), 
                            $predef["y"] + ($predef["h"] / 2),
                            $predef["w"], $predef["h"],
                            $start, $end,
                            $colori_vml[$predef["fill"]],IMG_ARC_PIE); // CHORD, NOFILL, EDGED
        }

        if($predef["stroke"] != "none"){
              // nb: stroke_w non e' scalato!   
              $j = - ($predef["stroke_w"] / 2);
              while($j < ($predef["stroke_w"] / 2)){     
                 imagearc($image,$predef["x"] + ($predef["w"] / 2), 
                          $predef["y"] + ($predef["h"] / 2),
                          $predef["w"] + $j,
                          $predef["h"] + $j,
                          $start, $end,
                          $colori_vml[$predef["stroke"]]);
                          
                 $j += 0.01;
              }
        }

        $fill_on = 1; $stroke_on = 1;
        $fill_color = "white"; $stroke_color = "black";
        $on_predef = 0;        
    } // fine visualizzazione arc


    ////////////////////////////////////////////////
    // gestione CURVE //////////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:curve.*)/",$riga)){
       $on_predef = 1;
    
       $predef["tipo"] = "curve";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";
       $predef["stroke_w"] = "1";
       
       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
              $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
              $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
              $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

       // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
       if(preg_match("/(.*\/>.*)/",$riga)){
         // non ci sono fill e stroke (elementi)
         $visualizza_predef = "curve";                      
       }
    }// fine curve

    if(preg_match("/(.*<\/v:curve.*)/",$riga)){
        $visualizza_predef = "curve";               
    }  // fine curve (con elementi)

    if($visualizza_predef == "curve"){  
         $visualizza_predef = "no";          
    
        // curve gestita come path!     
        // visualizzo la curva (approssimata)       
        $shape["x"]    = $predef["x"];
        $shape["y"]    = $predef["y"];
        $shape["w"]    = $predef["w"];
        $shape["h"]    = $predef["h"];
        $shape["vb_x"] = 1;
        $shape["vb_y"] = 1;
        
        $shape["fill"]     = $predef["fill"];
        $shape["stroke"]   = $predef["stroke"];
        $shape["stroke_w"] = $predef["stroke_w"];
        
        $shape["path"]["n_comandi"] = 2;
        
        $shape["path"]["comando"][0] = "M";
        $shape["path"]["n_valori"][0] = 2;
        $shape["path"]["valori"][0][0] = $predef["x1"];
        $shape["path"]["valori"][0][1] = $predef["y1"];

        
        $shape["path"]["comando"][1] = "c";
        $shape["path"]["n_valori"][1] = 6;
        $shape["path"]["valori"][1][0] = $predef["control_x_1"];
        $shape["path"]["valori"][1][1] = $predef["control_y_1"];
        $shape["path"]["valori"][1][2] = $predef["control_x_2"];
        $shape["path"]["valori"][1][3] = $predef["control_y_2"];
        $shape["path"]["valori"][1][4] = $predef["x2"];
        $shape["path"]["valori"][1][5] = $predef["y2"];
        
        visualizza_path_vml($image, $shape, $colori_vml);
            
        $fill_on = 1; $stroke_on = 1;
        $fill_color = "white"; $stroke_color = "black";
        $on_predef = 0;
        
    } // fine visualizzazione curve (con elementi)


    ////////////////////////////////////////////////
    // gestione POLYGON ///////////////////////////         POLYGON NON ESISTE
    ////////////////////////////////////////////////


    ///////////////////////////////////////////////////
    // gestione stoke - fill (elementi)  //////////////
    //////////////////////////////////////////////////
    
    // STROKE
    if(preg_match("/(.*<v:stroke.*)/",$riga)){
    
      // on
      $on_temp = 1;
      if(preg_match("/(.* on=.*)/",$riga)){
          $on_temp= preg_replace("/(.* on=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$on_temp)){
             $on_temp = preg_replace("/(.*)(\".*)/","\$1",$on_temp);
          }
          $on_temp = preg_replace("/\s/","",$on_temp);
          
          if(($on_temp == "f") or ($on_temp == "F") or 
             ($on_temp == "false") or ($on_temp == "FALSE")){
                $on_temp = 0;
          }
          else{
                $on_temp = 1;
          }
      }
      
      // color
      $color_temp = "none";
      if(preg_match("/(.* color=.*)/",$riga)){
            $color_temp= preg_replace("/(.* color=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$color_temp)){
                $color_temp = preg_replace("/(.*)(\".*)/","\$1",$color_temp);
            }
            $color_temp = preg_replace("/\s/","",$color_temp);          
      }
      
      // stroke_weight
      $stroke_w = "none";
      if(preg_match("/(.* weight=.*)/",$riga)){
          $stroke_w= preg_replace("/(.* weight=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$stroke_w)){
             $stroke_w = preg_replace("/(.*)(\".*)/","\$1",$stroke_w);
          }
          $stroke_w = preg_replace("/\s/","",$stroke_w);
          $stroke_w = converti_vml($stroke_w, $font_temp,100, 1);
      }


      // Mettiamo i valori trovati nei rispettivi elementi:
      //    L'elemento stroke puo' trovarsi esclusivamente in una figura predefinita
      //        oppure all'interno di shape (o shapetype)
      
      // stroke in una figura PREDEFINITA
      if($on_predef == 1){
        if($on_temp == 0){
            $predef["stroke"] = "none";
        }
        else{
            //if($color_temp = "default_black"){ $color_temp = "black"; }
            if($color_temp != "none"){
                $predef["stroke"] = $color_temp;
            }
            if($stroke_w != "none"){
                $predef["stroke_w"] = $stroke_w;
            }           
        }
      }
      
      // stroke in SHAPE
      elseif($on_shape == 1){
        if($on_temp == 0){
            $shape["stroke"] = "none";
        }
        else{
            if($color_temp != "none"){
                $shape["stroke"] = $color_temp;
            }
            if($stroke_w != "none"){
                $shape["stroke_w"] = $stroke_w;
            }
        }
      }
      
      // da gestire altri casi ??
      else{ }
    
    } // fine stroke
    
    // FILL
    if(preg_match("/(.*<v:fill.*)/",$riga)){
      // on
      $on_temp = 1;
      if(preg_match("/(.* on=.*)/",$riga)){
          $on_temp= preg_replace("/(.* on=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$on_temp)){
             $on_temp = preg_replace("/(.*)(\".*)/","\$1",$on_temp);
          }
          $on_temp = preg_replace("/\s/","",$on_temp);
          
          if(($on_temp == "f") or ($on_temp == "F") or 
             ($on_temp == "false") or ($on_temp == "FALSE")){
                $on_temp = 0;
          }
          else{
                $on_temp = 1;
          }
      }
      
      // color
      $color_temp = "default_white";
      if(preg_match("/(.* color=.*)/",$riga)){
          $color_temp = preg_replace("/(.* color=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$color_temp)){
             $color_temp = preg_replace("/(.*)(\".*)/","\$1",$color_temp);
          }
          $color_temp = preg_replace("/\s/","",$color_temp);          
      }

      // type (si riferisce ad un gradiente): gestito tramite il riempimento grigio 
      if(preg_match("/(.* type=.*)/",$riga)){
        $type = preg_replace("/(.* type=\")(.*)(\".*)/","\$2",$riga);
        while(preg_match("/(.*\".*)/",$type)){
            $type = preg_replace("/(.*)(\".*)/","\$1",$type);
        }
        $type = preg_replace("/\s/","",$type);
                 
        if($type != "solid"){
            $color_temp = "gray";
        }         
      }

      // Mettiamo i valori trovati nei rispettivi elementi:
      //    L'elemento fill puo' trovarsi esclusivamente in una figura predefinita
      //        oppure all'interno di shape (o shapetype)
      
      // fill in una figura PREDEFINITA
      if($on_predef == 1){
        if($on_temp == 0){
            $predef["fill"] = "none";
        }
        else{
            $predef["fill"] = $color_temp;
        }
      }
      
      // fill in SHAPE
      elseif($on_shape == 1){
        if($on_temp == 0){
            $shape["fill"] = "none";
        }
        else{
            $shape["fill"] = $color_temp;
        }
      }
      // da gestire altri casi ??
      else{ }

    } // fine fill



    ///////////////////////////////////////////////////
    // gestione shape ////////////////////////////////
    //////////////////////////////////////////////////
    if(preg_match("/(.*<v:shape.*)/",$riga)){
      $on_shape = 1;
      $shape["fill"] = "none";
      $shape["stroke"] = "none";
      $shape["stroke_w"] = "default_1";
      $shape["type"] = "";
      $shape["path"]["tipo"] = "none";
      $shape["path"]["impostato"] = "F";
      $shape["text"]["impostato"] = "F";
      $shape["text"]["font_s"] = "default_16";
      $shape["text"]["font_f"] = "default_Arial";
      
      $shape["n_image"] = 0;
      $shape["url_base"] = $url_base;
      $shape["textbox"]["impostato_testo"] = "F";
      $shape["textbox"]["impostato_box"] = "F";
      $shape["textbox"]["x"] = 0;
      $shape["textbox"]["y"] = 0;
      $shape["n_image_st"] = 0;
      $shape["impostato_cs"] = "F";
      $shape["impostato_co"] = "F";

      // gestione eventuale riferimento a shapetype (gestito prima della fase di
      //        visualizzazione, prima si impostano gli attributi definiti in shape, poi
      //        si cercano quelli mancanti in shapetype)
      if(preg_match("/(.* type=.*)/",$riga)){      
          $nome_st= preg_replace("/(.* type=\")(.*)(\".*)/","\$2",$riga);
          while(preg_match("/(.*\".*)/",$nome_st)){
             $nome_st = preg_replace("/(.*)(\".*)/","\$1",$nome_st);
          }
          $shape["type"] = preg_replace("/(.*)(#)(.*)/","\$3",$nome_st);
          $shape["type"] = preg_replace("/\s/","",$shape["type"]);  
      } 

      shape($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $viewbox_co_x, $viewbox_co_y,
            $n_translate, $livello,$translate_x, $translate_y,
            $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
            $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
            $wh_livello, $colori_vml, $font_temp, $shape, 
            $on_shape, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);

      // DEBUG
      //echo "X: ".$shape["x"]." Y: ".$shape["y"]." w: ".$shape["w"]." h: ".$shape["h"]." vb_x: ".$shape["vb_x"];
      //echo " vb_y: ".$shape["vb_y"]."<br />";
        
      // se l'elemento non ha altri elementi (fill e stroke) lo visualizzo subito)
      if(preg_match("/(.*\/>.*)/",$riga)){
         $fine_shape = 1;   
      }
    } // fine gestione shape
    
    if(preg_match("/(.*<\/v:shape.*)/",$riga)){ 
         $fine_shape = 1;   
    } // fine gestione shape (con elementi) 
    
    ///////////////////////////////////////////
    // visualizzazione shape
    ////////////////////////////////////////
    if($fine_shape == 1){

         $fine_shape = 0;

         if($shape["type"] != ""){
            // gestione shapetype   
            shapetype($image, $colori_vml, $shape, $contenuto_shapetype, $n_st, $perc_x, $perc_y, $font_temp);
         }

         // sistemo i colori_vml !!
         if($shape["fill"] == "cerca_shapetype"){
                $shape["fill"] = "none";
         }
         elseif(preg_match("/(default_)(.*)/",$shape["fill"])){
                $shape["fill"] = preg_replace("/(default_)(.*)/","\$2",$shape["fill"]);
         }
        
         if($shape["stroke"] == "cerca_shapetype"){
            $shape["stroke"] = "none";
         }
         elseif(preg_match("/(default_)(.*)/",$shape["stroke"])){
            $shape["stroke"] = preg_replace("/(default_)(.*)/","\$2",$shape["stroke"]);
         }
    
         if($shape["stroke_w"] == "cerca_shapetype"){
            $shape["stroke_w"] = "1";
         }
         elseif(preg_match("/(default_)(.*)/",$shape["stroke_w"])){
            $shape["stroke_w"] = preg_replace("/(default_)(.*)/","\$2",$shape["stroke_w"]);
         }
         elseif(preg_match("/(st_)(.*)/",$shape["stroke_w"])){
            $shape["stroke_w"] = preg_replace("/(st_)(.*)/","\$2",$shape["stroke_w"]);
         }

         if(preg_match("/(default_)(.*)/",$shape["text"]["font_s"])){
            $shape["text"]["font_s"] = preg_replace("/(default_)(.*)/","\$2",$shape["text"]["font_s"]);
         }

         // visualizzo eventuali immagini
         // prima visualizzo quelle definite in shapetype, poi le altre
         $k = 0;
         while($k < $shape["n_image_st"]){
               if(substr($shape["image_st"][$k]["nome_file"],1,4) != "http"){
                  $shape["image_st"][$k]["nome_file"] = $url_base.$shape["image_st"][$k]["nome_file"];  
               }

               $crea = 1;
               if(($shape["image_st"][$k]["estensione"] == "jpeg") or 
                  ($shape["image_st"][$k]["estensione"] == "jpg")){
                    $file_img = @fopen($shape["image_st"][$k]["nome_file"], "r");
                    if($file_img){ 
                        @fclose($file_img);
                        $img = imagecreatefromjpeg($shape["image_st"][$k]["nome_file"]);
                    }
                    else{
                        $crea = 0;
                    }
               }
               elseif($shape["image"][$k]["estensione"] == "png"){
                  $file_img = @fopen($shape["image_st"][$k]["nome_file"], "r");
                  if($file_img){ 
                    @fclose($file_img);
                    $img = imagecreatefrompng($shape["image_st"][$k]["nome_file"]);
                  }
                  else{
                    $crea = 0;
                  }
               }
               elseif($shape["image"][$k]["estensione"] == "gif"){
                  $file_img = @fopen($shape["image_st"][$k]["nome_file"], "r");
                  if($file_img){ 
                    @fclose($file_img);
                    $img = imagecreatefromgif($shape["image_st"][$k]["nome_file"]);
                  }
                  else{
                    $crea = 0;
                  }
               }
               else{             
                  $crea = 0;         
               }
               
               // fase di visualizzazione
               if($crea == 1){   
                  $img_w = imagesx($img);
                  $img_h = imagesy($img);
                  imagecopyresized($image,$img,$shape["x"],$shape["y"],0,0,
                                   $shape["w"],$shape["h"],$img_w,$img_h);  
                  imagedestroy($img);
              }
              
              $k += 1;
         }
         
         
         // visualizzo le immagine definite in shape
         $k = 0;
         while($k < $shape["n_image"]){
            if(substr($shape["image"][$k]["nome_file"],1,4) != "http"){
                  $shape["image"][$k]["nome_file"] = $url_base.$shape["image"][$k]["nome_file"];    
            }               

            $crea = 1;
            if(($shape["image"][$k]["estensione"] == "jpeg") or ($shape["image"][$k]["estensione"] == "jpg")){
                  $file_img = @fopen($shape["image"][$k]["nome_file"], "r");
                  if($file_img){ 
                    @fclose($file_img);
                    $img = imagecreatefromjpeg($shape["image"][$k]["nome_file"]);
                  }
                  else{
                    $crea = 0;
                  }
            }
            elseif($shape["image"][$k]["estensione"] == "png"){
                  $file_img = @fopen($shape["image"][$k]["nome_file"], "r");
                  if($file_img){ 
                    @fclose($file_img);
                    $img = imagecreatefrompng($shape["image"][$k]["nome_file"]);
                  }
                  else{
                    $crea = 0;
                  }
            }
            elseif($shape["image"][$k]["estensione"] == "gif"){
                  $file_img = @fopen($shape["image"][$k]["nome_file"], "r");
                  if($file_img){ 
                    @fclose($file_img);
                    $img = imagecreatefromgif($shape["image_st"][$k]["nome_file"]);
                  }
                  else{
                    $crea = 0;
                  }
            }
            else{             
                  $crea = 0;         
            }
            
            // fase di visualizzazione
            if($crea == 1){   
                  $img_w = imagesx($img);
                  $img_h = imagesy($img);
                  imagecopyresized($image,$img,$shape["x"],$shape["y"],0,0,
                                   $shape["w"],$shape["h"],$img_w,$img_h);  
                  imagedestroy($img);
            }
            $k += 1;
         }
     
         // visualizzo la figura                 
         if(($shape["path"]["tipo"] == "p") or (($shape["path"]["tipo"] == "t") and ($shape["text"]["impostato"] == "F"))){
                visualizza_path_vml($image, $shape, $colori_vml);
         }
         elseif($shape["path"]["tipo"] == "t"){
            // da visualizzare il testo (tipo = t)
        
            $tr_x = $shape["x"]; $tr_y = $shape["y"];
            $vb_x = $shape["w"] / $shape["vb_x"]; $vb_y = $shape["h"] / $shape["vb_y"];
    
            $font_name_temp = $shape["text"]["font_f"];

            if((preg_match("/(Verdana)(.*)/",$font_name_temp)) or (preg_match("/(verdana)(.*)/",$font_name_temp))  ){
                $font_name = 'FONT/verdana.ttf';
            }
            elseif((preg_match("/(Arial)(.*)/",$font_name_temp)) or (preg_match("/(arial)(.*)/",$font_name_temp))  ){
                $font_name = 'FONT/arial.ttf';
            }
            else{
                $font_name = 'FONT/times.ttf';
            }

            $dim = $shape["text"]["font_s"]; //*  $vb_x * $sc_x;

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
            //echo $shape["text"]["testo"]." ".$font_name." ".$dim."<br />";

            if($shape["stroke"] != "none"){
                imagettftext($image,$dim ,0, ($shape["path"]["valori"][0][0] * $vb_x) + $tr_x,
                            ($shape["path"]["valori"][0][1] * $vb_y) + $tr_y,
                             $colori_vml[$shape["stroke"]], $font_name, $shape["text"]["testo"]);
            }
            if($shape["fill"] != "none"){               
                imagettftext($image,$dim ,0, ($shape["path"]["valori"][0][0] * $vb_x) + $tr_x,
                            ($shape["path"]["valori"][0][1] * $vb_y) + $tr_y,
                             $colori_vml[$shape["fill"]], $font_name, $shape["text"]["testo"]);
            }
         }

         // visualizzo l'eventuale testo definito in textbox
         if($shape["textbox"]["impostato_testo"] == "T"){
            // conversione (translate + viewbox);
            conversione_vml(&$tr_x, &$tr_y, &$vb_x, &$vb_y, $n_translate, $translate_x, $translate_y, 
                        $n_viewbox, $viewbox_x, $viewbox_y, $w_val, $h_val, $translate_livello, $viewbox_livello);        

            // textbox ha questi valori di default:
            $font_name_tb = 'FONT/times.ttf';
            $dim_tb = 12;
            
            $tr_x = $shape["x"]; $tr_y = $shape["y"];
            $vb_x = $shape["w"] / $shape["vb_x"]; $vb_y = $shape["h"] / $shape["vb_y"];
            
            $x_tb = $shape["textbox"]["x"] * $vb_x + $tr_x;
            $y_tb = $shape["textbox"]["y"] * $vb_y + $tr_y + 16;        

            /*      
            $x_tb = $shape["textbox"]["x"] * $vb_x + $shape["x"] + $tr_x;
            $y_tb = $shape["textbox"]["y"] * $vb_y + $shape["y"] + $tr_y;
            */
        
            //$x_tb = ($shape["textbox"]["x"] / $shape["vb_x"]) * $shape["w"];
            //$y_tb = ($shape["textbox"]["y"] / $shape["vb_y"]) * $shape["h"];

            // gestisco ogni porzione di textbox (ogni porzione e' racchiusa in 
            //      un tag HTML)
            $tb = 0;
            while($tb < $shape["textbox"]["n_testi"]){
                $dim = $shape["textbox"]["testo"][$tb]["font_s"];
                $font_name = $shape["textbox"]["testo"][$tb]["font_f"];
                $colore = $shape["textbox"]["testo"][$tb]["colore"];
                $valore = $shape["textbox"]["testo"][$tb]["valore"];
        
                if($font_name == "FONT/times.ttf"){
                    $dim /= 1.3;
                }
                elseif($font_name == "FONT/verdana.ttf"){
                    $dim /= 1.5;
                }
                elseif($font_name == "FONT/arial.ttf"){
                    $dim /= 1.4;
                }

                // mi sposto in base alla dimensione di tutti i testi precedenti
                $tb_prec = 0;
                $shift_x = 0;
                while($tb_prec < $tb){
                    $len_prec    = strlen($shape["textbox"]["testo"][$tb_prec]["valore"]);
                    $font_prec   = $shape["textbox"]["testo"][$tb_prec]["font_s"];
                    //$font_f_prec = $shape["textbox"]["testo"][$tb_prec]["font_f"];

                    $shift_corr = $len_prec * $font_prec / 2;
                    $shift_x += $shift_corr;
    
                    $tb_prec += 1;   
                }
   
                imagettftext($image,$dim, 0, $x_tb + $shift_x, $y_tb,$colori_vml[$colore], $font_name, $valore);
            
                $tb += 1;
            }    
         }
         
         $fill_on = 1; $stroke_on = 1;
         $fill_color = "white"; $stroke_color = "black";
         $on_shape = 0;
    } // fine visualizzazione shape


/////////////////////// COMMENTI FINO A QUI /////////////////////////////
/////////////////////// COMMENTI FINO A QUI /////////////////////////////
/////////////////////// COMMENTI FINO A QUI /////////////////////////////



    /////////////////////////////////////////////////
    // gestione path ////////////////////////////////
    /////////////////////////////////////////////////
    if(preg_match("/(.*<v:path.*)/",$riga)){
      // path puo' comparire solo dentro shape o shapetype. 
      if($on_shape == 1){
          // da gestire: textboxrect
          if($shape["path"]["tipo"] == "none"){
              $shape["path"]["tipo"] = "p";
          }
              if(preg_match("/(.* v=.*)/",$riga)){     
             $path_temp= preg_replace("/(.* v=\")(.*)(\".*)/","\$2",$riga);
             while(preg_match("/(.*\".*)/",$path_temp)){
                $path_temp = preg_replace("/(.*)(\".*)/","\$1",$path_temp);
             }
             $path_temp = preg_replace("/(.*)(,,)(.*)/","\$1,0,\$3",$path_temp);
             $path_temp = preg_replace("/(.*)(xe)(.*)/","\$1x e\$3",$path_temp);


             if($path_temp != ""){
                 path($shape, $path_temp);
             }    
          } 
          if(preg_match("/(.* textpathok=.*)/",$riga)){    
             $tp= preg_replace("/(.* textpathok=\")(.*)(\".*)/","\$2",$riga);
             while(preg_match("/(.*\".*)/",$tp)){
                $tp = preg_replace("/(.*)(\".*)/","\$1",$tp);
             }
             $tp = preg_replace("/\s/","",$tp);       
             if(($tp == "t") or ($tp == "T") or ($tp == "true") or ($tp == "TRUE")){
                $shape["path"]["tipo"] = "t";
             }
          } 
          
          if(preg_match("/(.* textboxrect=.*)/",$riga)){       
             $tb= preg_replace("/(.* textboxrect=\")(.*)(\".*)/","\$2",$riga);
             while(preg_match("/(.*\".*)/",$tb)){
                $tb = preg_replace("/(.*)(\".*)/","\$1",$tb);
             }
             //$tb = preg_replace("/\s/","",$tb);         
             $shape["textbox"]["impostato_box"] = "T";
             
             $j = 0;
             while((preg_match("/(\d)/",$tb)) and ($j < 2)){
            $valore[$j] = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$2",$tb);           
            $tb  = preg_replace("/(.*?)(-?\d+\.?\d*)(.*)/","\$3",$tb);
            $j += 1;
             }
             $valore[0] = preg_replace("/\s/","",$valore[0]);         
             $valore[1] = preg_replace("/\s/","",$valore[1]);
                    
             $shape["textbox"]["x"] = $valore[0];
                 $shape["textbox"]["y"] = $valore[1];
             
          } 

       } // fine on_shape = 1
    } // fine gestione path


    /////////////////////////////////////////////////
    // gestione textpath ////////////////////////////
    /////////////////////////////////////////////////
    if(preg_match("/(.*<v:textpath .*)/",$riga)){
      // path puo' comparire solo dentro shape o shapetype. 
      if($on_shape == 1){
          if(preg_match("/(.* style=.*)/",$riga)){     
            $style= preg_replace("/(.* style=\")(.*)(\".*)/","\$2",$riga);
                while(preg_match("/(.*\".*)/",$style)){
                   $style = preg_replace("/(.*)(\".*)/","\$1",$style);
            }
            $style = " ".$style;            
        
            if(preg_match("/(.*[ |;]font-size\s?:.*)/",$style)){

                        $fs = preg_replace("/(.*[ |;]font-size\s?:)(.*?)/","\$2",$style);
                    if(preg_match("/(.*;.*)/",$fs)){
                     while(preg_match("/(.*;.*)/",$fs)){
                            $fs = preg_replace("/(.*)(;.*)/","\$1",$fs);
                             }
                        }
                
                        $fs = preg_replace("/\s/","",$fs);
                            $fs = converti_vml($fs,$font_temp,$perc_x, 1);
                if(preg_match("/(.*default_.*)/",$shape["text"]["font_s"])){
                    $shape["text"]["font_s"] = $fs;
                }               
                }
            elseif(preg_match("/(default_)(.*)/",$shape["text"]["font_s"])){
                $shape["text"]["font_s"] = preg_replace("/(default_)(.*)/","\$2",$shape["text"]["font_s"]);
            }
                    

                if(preg_match("/(.*[ |;]font-family\s?:.*)/",$style)){
                        $ff = preg_replace("/(.*[ |;]font-family\s?:)(.*?)/","\$2",$style);
                    if(preg_match("/(.*;.*)/",$ff)){
                       while(preg_match("/(.*;.*)/",$ff)){
                            $ff = preg_replace("/(.*)(;.*)/","\$1",$ff);
                       }
                    }
                    $ff = preg_replace("/\s/","",$ff);
                    if(preg_match("/(.*default_.*)/",$shape["text"]["font_f"])){
                    $shape["text"]["font_f"] = $ff;
                }
            }               
            elseif(preg_match("/(default_)(.*)/",$shape["text"]["font_f"])){
                $shape["text"]["font_f"] = preg_replace("/(default_)(.*)/","\$2",$shape["text"]["font_f"]);
                }
    
            
          }
          else{
            if(preg_match("/(default_)(.*)/",$shape["text"]["font_s"])){
                $shape["text"]["font_s"] = preg_replace("/(default_)(.*)/","\$2",$shape["text"]["font_s"]);
            }
            if(preg_match("/(default_)(.*)/",$shape["text"]["font_f"])){
                $shape["text"]["font_f"] = preg_replace("/(default_)(.*)/","\$2",$shape["text"]["font_f"]);
                }

          }
          
          
              if(preg_match("/(.* string=.*)/",$riga)){    
             $string_temp= preg_replace("/(.* string=\")(.*)(\".*)/","\$2",$riga);
             while(preg_match("/(.*\".*)/",$string_temp)){
                $string_temp =   preg_replace("/(.*)(\".*)/","\$1",$string_temp);
                 }
             $str_len = strlen($string_temp);
             $string_temp = substr($string_temp,0,$str_len - 1);

             $shape["text"]["testo"] = $string_temp;    
             $shape["text"]["impostato"] = "T";
          } 
          
       } // fine on_shape = 1
    } // fine gestione textpath

    /////////////////////////////////////////////////
    // gestione texbox //////////////////////////////
    /////////////////////////////////////////////////

        if(preg_match("/(.*<\/v:textbox.*)/",$riga)){
        $in_textbox = 0;
    } // fine textbox

    if($in_textbox == 1){

        if(preg_match("/(.*<xxx:.*)/",$riga)){

            $testo_temp =  preg_replace("/(<.*>)(.*)/","\$2",$riga);
            $str_len = strlen($testo_temp);
            $testo_temp = substr($testo_temp,0,$str_len - 1);

       
            $t_corr = $shape["textbox"]["n_testi"];
        $livello_prec = $shape["textbox"]["testo"][$t_corr - 1]["livello"];


        $color_temp  = $shape["textbox"]["testo"][$t_corr - 1]["colore"];
        $font_f_temp = $shape["textbox"]["testo"][$t_corr - 1]["font_f"];
        $font_s_temp = $shape["textbox"]["testo"][$t_corr - 1]["font_s"];


        if(preg_match("/(.* style=.*)/",$riga)){       
                $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*[\"|'].*)/",$style)){
                   $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
            }
            $style = " ".$style;
         
          if(preg_match("/(.*[ |;]color\s?:.*)/",$style)){
                 $color_temp = preg_replace("/(.*[ |;]color\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$color_temp)){
                 while(preg_match("/(.*;.*)/",$color_temp)){
                       $color_temp = preg_replace("/(.*?)(;.*)/","\$1",$color_temp);
                  }
             }              
             $color_temp = preg_replace("/\s/","",$color_temp);          
          }  

          
        
          if(preg_match("/(.*[ |;]font-size\s?:.*)/",$style)){
                 $fs = preg_replace("/(.*[ |;]font-size\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$fs)){
                 while(preg_match("/(.*;.*)/",$fs)){
                       $fs = preg_replace("/(.*)(;.*)/","\$1",$fs);
                  }
             }
             $fs = preg_replace("/\s/","",$fs);
                     $fs = converti_vml($fs,$font_temp,100, 1); 
             
             $font_s_temp = $fs;
          }

          if(preg_match("/(.*[ |;]font-family\s?:.*)/",$style)){
                 $ff = preg_replace("/(.*[ |;]font-family\s?:)(.*?)/","\$2",$style);
             if(preg_match("/(.*;.*)/",$ff)){
                 while(preg_match("/(.*;.*)/",$ff)){
                       $ff = preg_replace("/(.*)(;.*)/","\$1",$ff);
                  }
             }
             $ff = preg_replace("/\s/","",$ff);
             $font_f_temp = $ff;
  
          } 
            }
            // non c'e' style
            else{
               // non fa niente
            }

        // sistemo i font
            $font_name_temp = $font_f_temp;
    
            if((preg_match("/(Verdana)(.*)/",$font_name_temp)) or (preg_match("/(verdana)(.*)/",$font_name_temp))  ){
            $font_name = 'FONT/verdana.ttf';
        }
        elseif((preg_match("/(Arial)(.*)/",$font_name_temp)) or (preg_match("/(arial)(.*)/",$font_name_temp))  ){
            $font_name = 'FONT/arial.ttf';
        }
        else{
            $font_name = 'FONT/times.ttf';
        }
        $font_f_temp = $font_name;

        $shape["textbox"]["testo"][$t_corr]["valore"]  = $testo_temp;
        $shape["textbox"]["testo"][$t_corr]["colore"]  = $color_temp;
        $shape["textbox"]["testo"][$t_corr]["font_f"]  = $font_f_temp;
        $shape["textbox"]["testo"][$t_corr]["font_s"]  = $font_s_temp; 
        $shape["textbox"]["testo"][$t_corr]["livello"] = $livello_prec + 1;

        $shape["textbox"]["n_testi"] += 1;

        }
        elseif(preg_match("/(.*<\/xxx:.*)/",$riga)){
        
            $testo_temp =  preg_replace("/(<.*>)(.*)/","\$2",$riga);
            $str_len = strlen($testo_temp);
            $testo_temp = substr($testo_temp,0,$str_len - 1);

       
            $t_corr = $shape["textbox"]["n_testi"];
        $livello_prec = $shape["textbox"]["testo"][$t_corr - 1]["livello"];


        $color_temp  = $shape["textbox"]["testo"][$t_corr - 2]["colore"];
        $font_f_temp = $shape["textbox"]["testo"][$t_corr - 2]["font_f"];
        $font_s_temp = $shape["textbox"]["testo"][$t_corr - 2]["font_s"];


        $shape["textbox"]["testo"][$t_corr]["valore"]  = $testo_temp;
        $shape["textbox"]["testo"][$t_corr]["colore"]  = $color_temp;
        $shape["textbox"]["testo"][$t_corr]["font_f"]  = $font_f_temp;
        $shape["textbox"]["testo"][$t_corr]["font_s"]  = $font_s_temp; 
        $shape["textbox"]["testo"][$t_corr]["livello"] = $livello_prec - 1;

        $shape["textbox"]["n_testi"] += 1;

        
        }
        else{
            // qua non dovrebbe mai andarci
        //echo "errore"; 
        }

    }
    
    if(preg_match("/(.*<v:textbox.*)/",$riga)){
      // path puo' comparire solo dentro shape o shapetype. 
      if($on_shape == 1){
        $in_textbox = 1;

        $shape["textbox"]["impostato_testo"] = "T";

        $testo_temp =  preg_replace("/(<.*>)(.*)/","\$2",$riga);        
        $str_len = strlen($testo_temp);
        $testo_temp = substr($testo_temp,0,$str_len - 1);

        
        $shape["textbox"]["n_testi"] = 1;
        $shape["textbox"]["testo"][0]["valore"] = $testo_temp;
        $shape["textbox"]["testo"][0]["colore"] = "black";
        $shape["textbox"]["testo"][0]["font_f"] = "FONT/times.ttf";
        $shape["textbox"]["testo"][0]["font_s"] = "16";
        $shape["textbox"]["testo"][0]["livello"] = "0";
            
       } // fine on_shape = 1
    } // fine gestione tag iniziale textpath
        
    /////////////////////////////////////////////////
    // gestione imagedata ////////////////////////////////
    /////////////////////////////////////////////////
    if(preg_match("/(.*<v:imagedata.*)/",$riga)){
      // path puo' comparire solo dentro shape o shapetype. 
      if($on_shape == 1){
          $shape["fill"] = "none";
         
           if(preg_match("/(.*src=.*)/",$riga)){
            $href = preg_replace("/(.*src=\")(.*)(\".*)/","\$2",$riga);
            while(preg_match("/(.*\".*)/",$href)){
                   $href = preg_replace("/(.*)(\".*)/","\$1",$href);
                }
            $href = preg_replace("/(.*)(#)(.*)/","\$1\$3",$href);
            $href = preg_replace("/(.*)(\s)(.*)/","\$1\$3",$href);
           }
           $estensione = preg_replace("/(.*)(\.)(.*)/","\$3",$href);
           
           $shape["image"][$shape["n_image"]]["estensione"] = $estensione;
           $shape["image"][$shape["n_image"]]["nome_file"]  = $href;
           $shape["n_image"] += 1;
                          
       } // fine on_shape = 1
    } // fine gestione imagedata


    ////////////////////////////////////////////////
    // gestione IMAGE ///////////////////////////
    ////////////////////////////////////////////////
    if(preg_match("/(.*<v:image .*)/",$riga)){
        
       $predef["tipo"] = "image";
       $predef["fill"] = "none";
       $predef["stroke"] = "none";

       predef_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, 
              $n_translate, $livello,$translate_x, $translate_y,
          $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello,
          $w, $h, $n_wh, $w_val, $h_val, $translate_livello,$viewbox_livello, 
          $wh_livello, $colori_vml, $font_temp, $predef, 
              $on_predef, $fill_on, $stroke_on, $fill_color, $stroke_color, $perc_x, $perc_y, $livello);
          
       if(preg_match("/(.*src=.*)/",$riga)){
        $href = preg_replace("/(.*src=\")(.*)(\".*)/","\$2",$riga);
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
      if($crea == 1){   
          $img_w = imagesx($img);
          $img_h = imagesy($img);
          imagecopyresized($image,$img,$predef["x"],$predef["y"],0,0,$predef["w"],$predef["h"],$img_w,$img_h);  
          imagedestroy($img);
      }


    }// fine image
              
    
    /// NB: da fare come ultima operazione alla fine del FOREACH
    /// Decremento il livello dei TAG ///
    if((preg_match("/(.*<\/v:.*)/",$riga)) or(preg_match("/(.*<v:.*\/>.*)/",$riga))){
        // controllo viewBox
        if(($n_viewbox > 0) and ($viewbox_livello[$n_viewbox - 1] == $livello)){
          $n_viewbox -= 1;
        }
        // translate
        while(($n_translate > 0) and ($translate_livello[$n_translate - 1] == $livello)){
          $n_translate -= 1;
        }
        while(($n_stroke > 1) and ($stroke_livello[$n_stroke - 1] == $livello)){
          $n_stroke -= 1;
        }
        while(($n_fill > 1) and ($fill_livello[$n_fill - 1] == $livello)){
          $n_fill -= 1;
        }
        while(($n_font >= 0) and ($font_livello[$n_font] == $livello)){
          $n_font -= 1;
        }       
        while(($n_wh > 0) and ($wh_livello[$n_wh - 1] == $livello)){
          $n_wh -= 1;
        }

        $livello -= 1;
        
    }

             

      }// fine funzione gestione_file

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////FINE DICHIARAZIONE FUNZIONI ///////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function vmlToGif($file)
{
	  

  // risoluzione dello schermo
  $schermo_x = 750;
  $schermo_y = 400;

  //nome del file gif
  //$nome=$_GET["name"];
 
 
  /* MOD */

  if ($_SERVER['argc'] == 0)
  {
	 $nome = $_FILES["file_htm"]["tmp_name"];	
  }
  else
	 $nome = $file;
  
  /* FINE MOD */


  /*
  $url_base = "";
  if(preg_match("/(\/)/",$nome)){
    $url_base = preg_replace("/(.*)(\/)(.*)/","\$1\$2",$nome);
  }
 */
 
 
  if(true){ 
    //@fclose($file);
    $tutte_le_righe = file($nome); 
 
 
   ///////////////////////////////////////////////
   // creazione di un file con un tag per riga ///
   ///////////////////////////////////////////////
   $new_svg = fopen('temp.html',"w");
     
   foreach ($tutte_le_righe as $riga){
        $tutto_il_file .= preg_replace("/\n/"," ",$riga); 
   }

   // cerco il valore del namespace di vml  
   $ns = preg_replace("/(.*)(xmlns)(.*)(=\s*\"urn:schemas-microsoft-com:vml)(.*)/","\$3",$tutto_il_file);
   if(preg_match("/(:)(.*)/",$ns)){
        $ns = preg_replace("/(:)(.*)/","\$2",$ns);
   }
   else{
        $ns = "";  
   }
   $ns = preg_replace("/\s/","",$ns);

   $tutto_il_file = preg_replace("/\t/"," ",$tutto_il_file);
   $tutto_il_file = preg_replace("/</","\n<",$tutto_il_file);
   $tutto_il_file = preg_replace("/.*<!--.*-->.*/","",$tutto_il_file);
   $tutto_il_file = preg_replace("/.*<\?.*\?>.*/","",$tutto_il_file);
   $tutto_il_file = preg_replace("/.*<!D.*>.*/","",$tutto_il_file);
   
   // imposto il namespace di vml a v e imposto il namespace per tutti gli altri elementi a x   
   if($ns == ""){
     $tutto_il_file = preg_replace("/(.*)(<!)(.*)/","\$1Z@#\$3",$tutto_il_file);

     $tutto_il_file = preg_replace("/(.*)(<\S*:)(.*)/","\$1X@#x:\$3",$tutto_il_file);
     $tutto_il_file = preg_replace("/(.*)(<\/\S*:)(.*)/","\$1X@#/x:\$3",$tutto_il_file);    
   
     $tutto_il_file = preg_replace("/(.*)(<\/)(\S*)(.*)/","\$1Y#@v:\$3\$4",$tutto_il_file);
     $tutto_il_file = preg_replace("/(.*)(<)(\S*)(.*)/","\$1\$2v:\$3\$4",$tutto_il_file);
     $tutto_il_file = preg_replace("/(.*)(Y#@v:)(.*)/","\$1</v:\$3",$tutto_il_file);
     
     $tutto_il_file = preg_replace("/(.*)(X@#x:)(.*)/","\$1<x:\$3",$tutto_il_file);
     $tutto_il_file = preg_replace("/(.*)(X@#\/x:)(.*)/","\$1</x:\$3",$tutto_il_file);

     $tutto_il_file = preg_replace("/(.*)(Z@#)(.*)/","\$1<!$3",$tutto_il_file);
   }
   else{
    $tutto_il_file = preg_replace("/(.*)(<!)(.*)/","\$1Z@#\$3",$tutto_il_file);

    $tutto_il_file = preg_replace("/(.*)(<$ns:)(.*)/","\$1X@#$ns:\$3",$tutto_il_file);
    $tutto_il_file = preg_replace("/(.*)(<\/$ns:)(.*)/","\$1X@#/$ns:\$3",$tutto_il_file);

    $tutto_il_file = preg_replace("/(.*)(<\/)(\S*)(.*)/","\$1Y@#xxx:\$3\$4",$tutto_il_file);
    $tutto_il_file = preg_replace("/(.*)(<)(\S*)(.*)/","\$1\$2xxx:\$3\$4",$tutto_il_file);
    $tutto_il_file = preg_replace("/(.*)(Y@#)(.*)/","\$1</\$3",$tutto_il_file);
   
    $tutto_il_file = preg_replace("/(.*)(X@#$ns:)(.*)/","\$1<v:$3",$tutto_il_file);
    $tutto_il_file = preg_replace("/(.*)(X@#\/$ns:)(.*)/","\$1</v:$3",$tutto_il_file);

    $tutto_il_file = preg_replace("/(.*)(Z@#)(.*)/","\$1<!$3",$tutto_il_file);
   }

   fwrite($new_svg,$tutto_il_file);
   fclose($new_svg);

   //////////////////////////////////////////////////////////////////////////////
   ////////////////////////////// gestione colori_vml ///////////////////////////////
   //////////////////////////////////////////////////////////////////////////////
   
   // cerco tutti i colori_vml utilizzati nel documento per poterli preventivamente
   //       allocare
   $tutte_le_righe = file('temp.html');   
   
   $n_colori_vml = 0;
   
   foreach ($tutte_le_righe as $riga){   
   
    $riga_colori_vml = $riga;   
    
    if(preg_match("/(.* color=.*)/",$riga_colori_vml)){
       $colore = preg_replace("/(.* color=\")(.*)(\".*)/","\$2",$riga_colori_vml);
       while(preg_match("/(.*\".*)/",$colore)){
        $colore = preg_replace("/(.*)(\".*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);
      
       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori_vml){
            if($valore_colori_vml[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori_vml;      
            }
            $j += 1;
         }
         if($new_color == 1){
            $valore_colori_vml[$n_colori_vml] = $colore;
            $n_colori_vml += 1;
         }
       }
    }
    if(preg_match("/(.*?[ |;|\"]color:.*)/",$riga_colori_vml)){
       $colore = preg_replace("/(.*?[ |;|\"]color:)(.*)([;|\"].*)/","\$2",$riga_colori_vml);
       while(preg_match("/(.*[;|\"].*)/",$colore)){
            $colore = preg_replace("/(.*)([;|\"].*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);    
       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori_vml){
            if($valore_colori_vml[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori_vml;      
            }
            $j += 1;
         }
         if($new_color == 1){
            $valore_colori_vml[$n_colori_vml] = $colore;
            $n_colori_vml += 1;
         }
       }
    }
    
    if(preg_match("/(.*strokecolor=.*)/",$riga_colori_vml)){
       $colore = preg_replace("/(.* strokecolor=\")(.*)(\".*)/","\$2",$riga_colori_vml);
       while(preg_match("/(.*\".*)/",$colore)){
            $colore = preg_replace("/(.*)(\".*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);
       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori_vml){
            if($valore_colori_vml[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori_vml;      
            }
            $j += 1;
         }
         if($new_color == 1){
            $valore_colori_vml[$n_colori_vml] = $colore;
            $n_colori_vml += 1;
         }
      }               
    }   

    if(preg_match("/(.*fillcolor=.*)/",$riga_colori_vml)){
       $colore = preg_replace("/(.* fillcolor=\")(.*)(\".*)/","\$2",$riga_colori_vml);
       while(preg_match("/(.*\".*)/",$colore)){
            $colore = preg_replace("/(.*)(\".*)/","\$1",$colore); 
       } 
       $colore = preg_replace("/\s/","",$colore);
       if($colore != "none"){
         $j = 0;
         $new_color = 1;
         while($j < $n_colori_vml){
            if($valore_colori_vml[$j] == $colore){
                $new_color = 0; 
                $j = $n_colori_vml;      
            }
            $j += 1;
         }
         if($new_color == 1){
            $valore_colori_vml[$n_colori_vml] = $colore;
            $n_colori_vml += 1;
         }
      }               
    }   
   }

   //////////////////////////////////////////////////////////////////////////////
   /////////////////////////// fine gestione colori_vml /////////////////////////////
   //////////////////////////////////////////////////////////////////////////////

   
     
   ///////////////////////////////////////////
   // adesso gestiamo il nuovo file creato ///
   ///////////////////////////////////////////
   $tutte_le_righe = file('temp.html'); 

   // azzero le pile ed il livello degli elementi
   $n_viewbox = 0;
   $n_translate = 0;
   $n_wh = 0;
   $livello = 0;
   
   $n_fill = 1;
   $n_stroke = 1;
   $stroke_width["n"] = 1;
  
   // imposto le proprieta' di stroke  e fill con i valori di default
   $fill[0] = "black";
   $stroke[0] = "white"; // forse 
   $fill_livello[0] = "-1";
   $stroke_livello[0] = "-1";
   $stroke_width["valore"] = "xx";
   $stroke_width["livello"] = "-1";
   
   $fill_on = 1;
   $stroke_on = 1;
   $fill_color = "white";
   $stroke_color = "black";
   
   // imposto le variabili di gestione del testo
   $in_text = 0;
   $testo[0]["valore"] ="";
   $n_testi = 0;
   $n_font = 0;
   $font_s[0] = 12; // 12: valore di default ??
   $font_livello[0] = -1;
  
   // altre variabili
   $comment = 0;
   $in_shapetype = 0;

   $on_predef = 0; // indica se sono all'interno di una figura predefinita  
   $on_shape = 0; 
   
   $in_textbox = 0;
   
   $vml_start = 1; 
   // primo_group contiene info sul primo elemento group (potrebbe essere o meno il contenitore). 
   //   Contiene le info riguardo alle
   //       dimensioni di w e h (servono per gestire i valori %)
   $primo_group["in"] = "no";
   $primo_group["is_coordsize"] = "no";

   $n_st = 0;


   
   /////////////////////////////////////////////////////////////////////////
   /////////////////////////////////////////////////////////////////////////
   /////////////////// GESTIONE DEL DOCUMENTO ////////////////////////////
   /////////////////////////////////////////////////////////////////////////
   /////////////////////////////////////////////////////////////////////////
   foreach ($tutte_le_righe as $riga){
   
     if(preg_match("/(.*<!--.*)/",$riga)){
        $comment = 1;
     }
     if(preg_match("/(.*<v:shapetype .*)/",$riga)){
        $in_shapetype = 1;
     }
  
   
     if(($comment == 0) and ($in_shapetype == 0)){
        //////////////////////////////
        //////////////////////////////
        // qui imposto w e h /////////
        /////////////////////////////
        
        // sono nel primo tag di vml
        if (($vml_start == 1) and (preg_match("/(.*<v:.*)/",$riga))){
            $vml_start = 0;
            if(preg_match("/(.*<v:group.*)/",$riga)){     
                $primo_group["in"] = "si";
                if(preg_match("/(.* style=.*)/",$riga)){       
                    $style= preg_replace("/(.* style=[\"|'])(.*)(\".*)/","\$2",$riga);
                    while(preg_match("/(.*[\"|'].*)/",$style)){
                        $style = preg_replace("/(.*)([\"|'].*)/","\$1",$style);
                    }
                    $style = " ".$style;
    
                    // imposto font-size
                    $font_temp = 12;
                    if(preg_match("/(.*[ |;]font-size\s?:.*)/",$style)){
                        $font_temp = preg_replace("/(.*[ |;]font-size\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$font_temp)){
                            while(preg_match("/(.*;.*)/",$font_temp)){
                                $font_temp = preg_replace("/(.*)(;.*)/","\$1",$font_temp);
                            }
                        }
                    }
         
                    // imposto WIDTH
                    $w = $schermo_x;
                    if(preg_match("/(.*[ |;]width\s?:.*)/",$style)){
                        $w = preg_replace("/(.*[ |;]width\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$w)){
                            while(preg_match("/(.*;.*)/",$w)){
                                $w = preg_replace("/(.*)(;.*)/","\$1",$w);
                            }
                        }
             
                        $w = preg_replace("/\s/","",$w);
                        $w = converti_vml($w,$font_temp,$schermo_x,1); 
                    }  

                    // imposto HEIGHT
                    $h = $schermo_y;
                    if(preg_match("/(.*[ |;]height\s?:.*)/",$style)){
                        $h = preg_replace("/(.*[ |;]height\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$h)){
                            while(preg_match("/(.*;.*)/",$h)){
                                $h = preg_replace("/(.*)(;.*)/","\$1",$h);
                            }
                        }
                    
                        $h = preg_replace("/\s/","",$h);
                        $h = converti_vml($h,$font_temp,$schermo_y,1);              
                    }

                    // imposto LEFT
                    $x = 0;
                    if(preg_match("/(.*[ |;]left\s?:.*)/",$style)){
                        $x = preg_replace("/(.*[ |;]left\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$x)){
                            while(preg_match("/(.*;.*)/",$x)){
                                $x = preg_replace("/(.*)(;.*)/","\$1",$x);
                            }
                        }
                        $x = preg_replace("/\s/","",$x);
                        $x = converti_vml($x,$font_temp,$schermo_x,1); 
                    }  

                    // imposto TOP
                    $y = 0;
                    if(preg_match("/(.*[ |;]top\s?:.*)/",$style)){
                        $y = preg_replace("/(.*[ |;]top\s?:)(.*?)/","\$2",$style);
                        if(preg_match("/(.*;.*)/",$y)){
                            while(preg_match("/(.*;.*)/",$y)){
                                $y = preg_replace("/(.*)(;.*)/","\$1",$y);
                            }
                        }
                                 
                        $y = preg_replace("/\s/","",$y);
                        $y = converti_vml($y,$font_temp,$schermo_y,1);              
                    }                    
                }
                else{ // non c'e' style
                    $x = 0;
                    $y = 0;
                    $w = $schermo_x;
                    $h = $schermo_y;
                }
            }
            // il primo elemento non e' group, imposto la gif grande come lo schermo
            else{
                $x = 0;
                $y = 0;
                $w = $schermo_x;
                $h = $schermo_y;
            }

            $primo_group["w"] = $w;
            $primo_group["h"] = $h;
  
            //////////////////////////////////
            // creazione dell'immagine GIF ///
            /////////////////////////////////

            // immagine grande quanto lo schermo, in quanto VML non fa clipping
            $image = imagecreate($schermo_x + 200, $schermo_y + 200); 
            
            //$image = imagecreate(ceil($w + $x) + 2, ceil($h + $y) + 2); 
            // +1, se non c'e' non vengono visualizzati i bordi delle 
            //  immagini grandi quanto la figura

            $colori_vml = carica_colori_vml($image, $n_colori_vml, $valore_colori_vml);

            imagefill($image,0,0,$colori_vml["white"]);
            // fine creazione immagine //

        } // fine if, primo elemento vml
    
    
        
        gestione_file_vml($image, $riga, $n_viewbox, $viewbox_x, $viewbox_y, $viewbox_co_x, $viewbox_co_y,
                      $n_translate, $livello, $contenuto_shapetype, $n_st, $translate_x, $translate_y, 
                      $n_stroke, $n_fill, $fill, $stroke, $fill_livello, $stroke_livello, $stroke_width,
                      $w, $h, $n_wh, $w_val, $h_val, $translate_livello, $viewbox_livello, $wh_livello, $colori_vml, 
                      $primo_group, $in_text, $testo, $n_testi,$path_comando, $path_valori, $path_n_valori, 
                      $path_n_comandi, $x_text, $y_text, $n_font, $font_s, $font_livello, $predef, $on_predef,
                      $shape, $on_shape, $fill_on, $stroke_on, $fill_color, $stroke_color, $in_textbox, $url_base);

        if($primo_group["in"] == "si"){ $primo_group["in"] = "no"; }
        
     } // fine if comment == 0 and in_shapetype == 0    
     
     elseif(($comment == 0) and ($in_shapetype == 1)){
        $contenuto_shapetype[$n_st] = $riga;
        $n_st += 1;
     }
    
     if(preg_match("/(.*-->.*)/",$riga)){
        $comment = 0;
     }
     
     if((preg_match("/(.*<\/v:shapetype.*)/",$riga)) or 
       ((preg_match("/(.*<v:shapetype.*)/",$riga)) and (preg_match("/(.*\/>.*)/",$riga)))){
          $in_shapetype = 0;
     }

   } // fine FOREACH piu' esterno


   // Per l'eventuale creazioni di un file gif
   //$image_gif = imagegif($image);

   if ($_SERVER['argc'] == 0)
   {
	   @header('Content-type: image/gif'); 
	   imagegif($image); 
  	   imagedestroy($image); 
   }
   else
	   return $image;
  }
  else
  {
	        echo "<html><head><title>Sorry</title></head><body>Spiacenti,";
		echo "file ".$nome." non trovato</body></html>";
  }
      
}

if ($_SERVER['argc'] == 0)
	vmlToGif(NULL);
 
?>
