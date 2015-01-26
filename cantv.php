<?php
    /*
     * Script para obtener la deuda de cantv
     * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
     * Versión 0.1
     * 
     * Parametros (Vía GET o POST)
     * a: código de area
     * t: Número de Teléfono
     * 
     * Este escript está liberado bajo los términos de la WTFPL versión 2
     * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
     */

    $url = "http://www.cantv.com.ve/seccion.asp?pid=1&sid=450";
    $params = "sarea=".$_REQUEST["a"]."&stelefono=".$_REQUEST["t"];

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch,CURLOPT_REFERER,'http://www.cantv.com.ve');
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

        #Obtener Saldo Actual
        $npos = strpos($html, 'Saldo actual Bs.') + 118;
        $j['saldoActual'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        #Obtener Ultima Facturación
        $npos = strpos($html, 'Fecha de &uacute;ltima facturaci&oacute;n:') + 132;
        $j['ultimaFacturacion'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        #Obtener Fecha de Corte
        $npos = strpos($html, 'Fecha corte:') + 102;
        $j['fechaCorte'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        #Obtener Fecha de vencimiento
        $npos = strpos($html, 'Fecha de vencimiento:') + 111;
        $j['fechaVencimiento'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        #Obtener Saldo vencido
        $npos = strpos($html, 'Saldo vencido:') + 116;
        $j['saldoVencido'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        #Obtener Monto del último pago realizado:
        $npos = strpos($html, 'Monto del &uacute;ltimo pago realizado:') + 130;
        $j['ultimoPago'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));

        $j['error'] = 0;
        print json_encode($j);
    }

