<?php
    /*
     * Script para obtener los datos de la cuenta individual del seguro social
     * Creado por William Cabrera (aka willicab) <cabrerawilliam@gmail.com>
     * Versión 0.1
     * 
     * Parametros (Vía GET o POST)
     * n: Nacionalidad, puede ser V, E o T
     * c: Número de Cédula
     * y: Año de nacimiento
     * m: Mes de nacimiento
     * d: Día de nacimiento
     * 
     * Este escript está liberado bajo los términos de la WTFPL versión 2
     * o superior, puede conseguir una copia en http://www.wtfpl.net/about/
     */

    $url = "http://www.ivss.gob.ve:28083/CuentaIndividualIntranet/CtaIndividual_PortalCTRL";
    $params = "nacionalidad_aseg=".$_REQUEST["n"]."&cedula_aseg=".$_REQUEST["c"]."&y=".$_REQUEST["y"]."&m=".$_REQUEST["m"]."&d=".$_REQUEST["d"];

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch,CURLOPT_REFERER,"http://ivss.gov.ve");
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux i686; rv:32.0) Gecko/20100101 Firefox/32.0');
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    curl_setopt($ch,CURLOPT_TIMEOUT,10);

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
        
        #Verificar si hubo error
        $npos = strpos($html, 'function error');
        if ($npos > 0) {
            $j['error'] = "la C&eacute;dula no esta registrada como asegurado";
            print json_encode($j);
            return;
        }

        #No hubo error
        $j['error'] = 0;

        #Obtener Cédula
        $npos = strpos($html, 'Identidad') + 65;
        $j['cedula'] = trim(substr($html, ($npos), 20));

        #Obtener Nombre
        $npos = strpos($html, 'Apellido') + 60;
        $j['nombre'] = trim(substr($html, ($npos), (strpos($html, '</td>', ($npos)) - ($npos))));

        #Obtener Sexo
        $npos = strpos($html, 'Sexo') + 64;
        $j['sexo'] = trim(substr($html, ($npos), 15));
        
        #Obtener Fecha de Nacimiento
        $npos = strpos($html, '#000000">', strpos($html, 'Nacimiento')) + 9;
        $j['fnac'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        #Obtener Número Patronal
        $npos = strpos($html, '#000000">', strpos($html, 'Patronal')) + 9;
        $j['npat'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        #Obtener Nombre Empresa
        $npos = strpos($html, '#000000">', strpos($html, 'Empresa')) + 9;
        $j['nemp'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        #Obtener Fecha de Ingreso
        $npos = strpos($html, '#000000">', strpos($html, 'Ingreso')) + 9;
        $j['fing'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        #Obtener Estatus del Asegurado
        $npos = strpos($html, '<td width="28%">', strpos($html, 'Estatus del Asegurado')) + 16;
        $j['estatus'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        #Obtener Fecha de Primera Afiliación
        $npos = strpos($html, '<td width="20%">', strpos($html, 'Primera Afiliaci&oacute;n')) + 16;
        $j['afiliacion'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));

        #Obtener Fecha de Contingencia
        $npos = strpos($html, '<td width="28%">', strpos($html, 'Contingencia')) + 16;
        $j['contingencia'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        #Obtener Total Semanas Cotizadas
        $npos = strpos($html, '<td width="19%" align="center">', strpos($html, 'TOTAL SEMANAS COTIZADAS')) + 31;
        $j['semanas'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        #Obtener Total Salarios Cotizados
        $npos = strpos($html, '<td colspan="3" align="center">', strpos($html, 'TOTAL SALARIOS COTIZADOS')) + 31;
        $j['salarios'] = trim(substr($html, ($npos), (strpos($html, '<', ($npos)) - ($npos))));
        
        print json_encode($j);
    }