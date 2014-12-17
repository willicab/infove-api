<?php
    /*
     * Script para obtener la deuda de corpoelec
     * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
     * Versión 0.1
     * 
     * Parametros (Vía GET o POST)
     * n: Número de Nic
     * 
     * Este escript está liberado bajo los términos de la WTFPL versión 2
     * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
     */

    $url = "http://cobrosweb.cadafe.com.ve/enlinea/consultadeuda.aspx?nic=".$_REQUEST["n"];

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER,'http://cadafe.com.ve');
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

        #Obtener NIC
        $npos = strpos($html, 'TextBox1') + 29;
        $j['nic'] = trim(substr($html, ($npos), (strpos($html, '"', ($npos)) - ($npos))));

        #Obtener USUARIO
        $npos = strpos($html, 'TextBox2') + 29;
        $j['usuario'] = trim(substr($html, ($npos), (strpos($html, '"', ($npos)) - ($npos))));

        #Obtener PAGO PENDIENTE
        $npos = strpos($html, 'TextBox7') + 29;
        $j['pendiente'] = trim(substr($html, ($npos), (strpos($html, '"', ($npos)) - ($npos))));

        #Obtener PAGO VENCIDO
        $npos = strpos($html, 'TextBox5') + 29;
        $j['vencido'] = trim(substr($html, ($npos), (strpos($html, '"', ($npos)) - ($npos))));

        # Obtener Error
        $j['error'] = ($j['usuario'] == 'y=' ? 'El Usuario no esta Registrado....' : 0);

        print json_encode($j);
    }