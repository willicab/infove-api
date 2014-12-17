<?php
    /*
     * Script para obtener los datos del RIF
     * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
     * Versión 0.1
     * 
     * Parametros (Vía GET o POST)
     * r: Número de RIF
     * 
     * Este escript está liberado bajo los términos de la WTFPL versión 2
     * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
     */

    $url = "http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=".$_REQUEST["r"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER,'http://seniat.gob.ve');
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux i686; rv:32.0) Gecko/20100101 Firefox/32.0');
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    curl_setopt($ch,CURLOPT_TIMEOUT,30);

    $html=curl_exec($ch);
    if($html==false){
        $m=curl_error(($ch));
        error_log($m);
        curl_close($ch);
        $j['error'] = $m;
        print json_encode($j);
        return;
    } else {
        curl_close($ch);

        if (strpos($html, 'rif:numeroRif="') == false) {
            $j['rif'] = $rif;
            $j['error'] = "El RIF $rif no est&aacute; registrado o no existe";
            print json_encode($j);
            return;
        }

        $j['error'] = 0;

        #Obtener RIF
        $npos = strpos($html, 'rif:numeroRif="') + 15;
        $j['rif'] = trim(substr($html, ($npos), (strpos($html, '"', ($npos)) - ($npos))));

        #Obtener Nombre
        $npos = strpos($html, '<rif:Nombre>') + 12;
        $j['nombre'] = utf8_decode(trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos)))));

        #Obtener Agente de retención
        $npos = strpos($html, '<rif:AgenteRetencionIVA>') + 24;
        $j['retencion'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        #Obtener Contribuyente
        $npos = strpos($html, '<rif:ContribuyenteIVA>') + 22;
        $j['contribuyente'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        #Obtener Tasa
        $npos = strpos($html, '<rif:Tasa>') + 10;
        $j['tasa'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        print json_encode($j);
    }