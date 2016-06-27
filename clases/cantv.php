<?php
/*
 * API de información de servicios de Venezuela
 * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
 * Versión 0.2
 *
 * Clase que permite obtener datos de Cantv
 * 
 * Este código está liberado bajo los términos de la WTFPL versión 2
 * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
 */
class Cantv {
    /*
    * Método: obtenerDeuda
    * Descripción: obtiene la deuda del telefono indicado
    * Parámetros:
    * $codigo: Código de area del número de teléfono
    * $telefono: Número de teléfono
    *
    * Retorna: cadena json con los siguientes datos
    * saldoactual: Saldo Actual
    * ultimafacturacion: Ultima Facturación
    * fechacorte: Fecha de Corte
    * fechavencimiento: Fecha de vencimiento
    * saldovencido: Saldo vencido
    * ultimopago: Monto del último pago realizado
    */
    public function obtenerDeuda($codigo = "", $telefono = "") {
        $url = "http://www.cantv.com.ve/seccion.asp?pid=1&sid=450";
        $params = "sarea=".$codigo."&stelefono=".$telefono;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch,CURLOPT_REFERER,'http://www.cantv.com.ve');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux i686; rv:32.0) Gecko/20100101 Firefox/40.0');
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,50);
        curl_setopt($ch,CURLOPT_TIMEOUT,300);
        $html=curl_exec($ch);
        
        if($html==false){
            $m=curl_error(($ch));
            error_log($m);
            curl_close($ch);
            $j["error"] = true;
            $j["descripcion"] = $m;
            print json_encode($j);
            return;
        } else {
            curl_close($ch);
            $j['error'] = false;
            $j["descripcion"] = "/cantv/deuda";
            
            // Obtener Saldo Actual
            $npos = strpos($html, 'Saldo actual Bs.') + 118;
            $j['saldoactual'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            // Obtener Ultima Facturación
            $npos = strpos($html, 'Fecha de &uacute;ltima facturaci&oacute;n:') + 132;
            $j['ultimafacturacion'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            // Obtener Fecha de Corte
            $npos = strpos($html, 'Fecha corte:') + 102;
            $j['fechacorte'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            // Obtener Fecha de vencimiento
            $npos = strpos($html, 'Fecha de vencimiento:') + 111;
            $j['fechavencimiento'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            // Obtener Saldo vencido
            $npos = strpos($html, 'Saldo vencido:') + 116;
            $j['saldovencido'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            // Obtener Monto del último pago realizado:
            $npos = strpos($html, 'Monto del &uacute;ltimo pago realizado:') + 130;
            $j['ultimopago'] = trim(substr($html, ($npos), (strpos($html, '</font>', ($npos)) - ($npos))));
            
            return json_encode($j);
        }
    }
}