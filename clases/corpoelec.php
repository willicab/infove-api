<?php

class Corpoelec {
    function obtenerDeuda($nic){
        $url = "http://cobrosweb.cadafe.com.ve/enlinea/consultadeuda.aspx?nic=$nic";
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
            curl_close($ch);
            $j['error'] = true;
            $j['descripcion'] = $m;
            return json_encode($j);
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
            $j['error'] = ($j['usuario'] == 'y=' ? true : false);
            $j['descripcion'] = ($j['usuario'] == 'y=' ? 'El Usuario no esta Registrado....' : "/corpoelec/deuda");
            return json_encode($j);
        }        
    }
}