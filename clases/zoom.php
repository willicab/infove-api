<?php

class Zoom {
    function obtenerSeguimiento($guia) {
        $url = "https://www.grupozoom.com/tracking/consultarope.php3?tipo=guia&txtcodguias=$guia";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER,'https://www.grupozoom.com/');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux i686; rv:32.0) Gecko/20100101 Firefox/32.0');
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);
        $html=curl_exec($ch);
        $html = str_replace("\n", "", str_replace("\r", "", str_replace("\t", "", $html)));

        if($html==false){
            $m=curl_error(($ch));
            error_log($m);
            curl_close($ch);
            $j['error'] = true;
            $j['descripcion'] = $m;
            return json_encode($j);
        } else {
            $j['error'] = false;
            $j['descripcion'] = "/zoom/seguimiento";
            $npos = strpos($html, 'Referencia</B></td>') + 62;
            $j['referencia'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));

            $npos = strpos($html, 'Estatus</B></td>') + 47;
            $j['estatus'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));

            $npos = strpos($html, 'Tipo de env') + 60;
            $j['tipoenvio'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));
            
            $npos = strpos($html, 'Fecha</B></td>') + 52;
            $j['fecha'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));

            $npos = strpos($html, 'Origen</B></td>') + 46;
            $j['origen'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));

            $npos = strpos($html, 'Destino</B></td>') + 47;
            $j['destino'] = trim(substr($html, ($npos), (strpos($html, '</TD>', ($npos)) - ($npos))));

            preg_match('/Oficina<\/B><\/td><\/tr>(.*)<\/table><\/td>/i', $html, $coincidencias);
            $coincidencias2 = preg_grep('/<td class=normal>(.*)<\/td>/i', explode("\n", str_replace("tr><tr", "tr>\n<tr", str_replace("td><td", "td>\n<td", $coincidencias[1]))));
            $seguimiento = array_reverse($coincidencias2);
            $j['seguimiento'] = array();
            $i = 0;
            foreach($seguimiento as $v) {
                $i++;
                $h = str_replace("</tr>", "", str_replace("</td>", "", $v));
                switch($i) {
                    case 1:
                        $k["oficina"] = $h;
                        break;
                    case 2:
                        $k["motivo"] = $h;
                        break;
                    case 3:
                        $k["estatus"] = $h;
                        break;
                    case 4:
                        $k["fecha"] = $h;
                        $i = 0;
                        $j['seguimiento'][] = $k;
                        $k = null;
                        break;
                }
            }
        }
        return json_encode($j);
    }
}
