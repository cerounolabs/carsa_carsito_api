<?php
    $app->get('/v1/000/login/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT

            b.AACUEN                        AS      cliente_cuenta,
            b.AaNom1                        AS		cliente_nombre,
            b.AaApe1                        AS		cliente_apellido,
            b.AgDocu                        AS      cliente_documento_tipo,
            b.AaDocu                        AS      cliente_documento_numero,
            CONVERT(date, b.AaFech, 103)    AS      cliente_fecha_nacimiento,
            a.COMPLOGUUI                    AS      login_uuid,
            a.COMPLOGMAI                    AS      login_mail
            
            FROM COMPLOG a
            INNER JOIN FSD0011 b ON a.COMPLOGDOC = b.AaDocu
            
            WHERE a.COMPLOGUUI = ?
            ORDER BY a.COMPLOGCOD DESC";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach ($row['cliente_fecha_nacimiento'] as $key => $value) {
                        if($key == 'date'){
                            $fecha = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    $detalle = array(
                        'cliente_cuenta'            => $row['cliente_cuenta'],
                        'cliente_nombre'            => $row['cliente_nombre'],
                        'cliente_apellido'          => $row['cliente_apellido'],
                        'cliente_documento_tipo'    => $row['cliente_documento_tipo'],
                        'cliente_documento_numero'  => $row['cliente_documento_numero'],
                        'cliente_fecha_nacimiento'  => $fecha,
                        'login_uuid'                => $row['login_uuid'],
                        'login_mail'                => $row['login_mail']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'cliente_cuenta'	        => '',
                        'cliente_nombre'	        => '',
                        'cliente_apellido'          => '',
                        'cliente_documento_tipo'    => '',
                        'cliente_documento_numero'  => '',
                        'cliente_fecha_nacimiento'  => '',
                        'login_uuid'                => '',
                        'login_mail'                => ''
                    );

                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 204, 'status' => 'ok', 'message' => 'No hay registros', 'data' => $detalle), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                }
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });

    $app->get('/v1/100/top06/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT TOP 6
            a.COMPCABCOD                        AS      comprobante_codigo,
            a.COMPCABCTN                        AS      comprobante_timbrado_numero,
            CONVERT(date, a.COMPCABCTV, 103)    AS      comprobante_timbrado_vencimiento,
            a.COMPCABCNU                        AS      comprobante_numero,
            a.COMPCABCCI                        AS      comprobante_cantidad_impreso,
            a.COMPCABCIM                        AS      comprobante_importe_numero,
            a.COMPCABCIL                        AS      comprobante_importe_letra,

            a.COMPCABMNO                        AS      movimiento_numero_original,
            a.COMPCABMNR                        AS      movimiento_numero_reversion,
            a.COMPCABMUO                        AS      movimiento_usuario_original,
            a.COMPCABMUR                        AS      movimiento_usuario_reversion,
            CONVERT(date, a.COMPCABMFO, 103)    AS      movimiento_fecha_original,
            CONVERT(date, a.COMPCABMFR, 103)    AS      movimiento_fecha_reversion,
            a.COMPCABMHO                        AS      movimiento_hora_original,
            a.COMPCABMHR                        AS      movimiento_hora_reversion,

            a.COMPCABONU                        AS      operacion_numero,
            a.COMPCABOCU                        AS      operacion_cuota,

            a.COMPCABPNO                        AS      persona_nombre,
            a.COMPCABPDO                        AS      persona_documento,
            a.COMPCABPCU                        AS      persona_cuenta,
            a.COMPCABPDI                        AS      persona_direccion,
            a.COMPCABPTE                        AS      persona_telefono,

            b.COMPESTCOD                        AS      estado_codigo,
            b.COMPESTNOM                        AS      estado_nombre,

            c.COMPTIPCOD                        AS      tipo_codigo,
            c.COMPTIPNOM                        AS      tigo_nombre,

            d.COMPCONCOD                        AS      condicion_codigo,
            d.COMPCONNOM                        AS      condicion_nombre,

            e.COMPPAGCOD                        AS      pago_codigo,
            e.COMPPAGNOM                        AS      pago_nombre, 
            
            f.crbanca                           AS      banca_codigo,
            f.crnomb                            AS      banca_nombre

            FROM COMPCAB a
            INNER JOIN COMPEST b ON a.COMPESTCOD = b.COMPESTCOD
            INNER JOIN COMPTIP c ON a.COMPTIPCOD = c.COMPTIPCOD
            INNER JOIN COMPCON d ON a.COMPCONCOD = d.COMPCONCOD
            INNER JOIN COMPPAG e ON a.COMPPAGCOD = e.COMPPAGCOD
            INNER JOIN FST020 f ON a.COMPCABOBA = f.crbanca

            WHERE a.COMPCABPCU = ?
            ORDER BY a.COMPCABCOD DESC";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach ($row['comprobante_timbrado_vencimiento'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_timbrado = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    foreach ($row['movimiento_fecha_original'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_original = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    foreach ($row['movimiento_fecha_reversion'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_reversion = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    if ($row['operacion_cuota'] != 0){
                        $tipo = 'COBRO DE CUOTA';
                    } else {
                        $tipo = 'DESEMBOLSO';
                    }
                    
                    $detalle = array(
                        'comprobante_codigo'                => $row['comprobante_codigo'],
                        'comprobante_tipo'                  => $tipo,
                        'comprobante_timbrado_numero'       => $row['comprobante_timbrado_numero'],
                        'comprobante_timbrado_vencimiento'  => $fecha_timbrado,
                        'comprobante_importe_numero'        => $row['comprobante_importe_numero'],
                        'comprobante_importe_letra'         => $row['comprobante_importe_letra'],
                        'comprobante_cantidad_impreso'      => $row['comprobante_cantidad_impreso'],
                        'comprobante_importe'               => $row['comprobante_importe'],
                        'movimiento_numero_original'        => $row['movimiento_numero_original'],
                        'movimiento_numero_reversion'       => $row['movimiento_numero_reversion'],
                        'movimiento_usuario_original'       => $row['movimiento_usuario_original'],
                        'movimiento_usuario_reversion'      => $row['movimiento_usuario_reversion'],
                        'movimiento_fecha_original'         => $fecha_original,
                        'movimiento_fecha_reversion'        => $fecha_reversion,
                        'movimiento_hora_original'          => $row['movimiento_hora_original'],
                        'movimiento_hora_reversion'         => $row['movimiento_hora_reversion'],
                        'operacion_numero'                  => $row['operacion_numero'],
                        'operacion_cuota'                   => $row['operacion_cuota'],
                        'persona_nombre'                    => $row['persona_nombre'],
                        'persona_documento'                 => $row['persona_documento'],
                        'persona_cuenta'                    => $row['persona_cuenta'],
                        'persona_direccion'                 => $row['persona_direccion'],
                        'persona_telefono'                  => $row['persona_telefono'],
                        'estado_codigo'                     => $row['estado_codigo'],
                        'estado_nombre'                     => $row['estado_nombre'],
                        'tipo_codigo'                       => $row['tipo_codigo'],
                        'tigo_nombre'                       => $row['tigo_nombre'],
                        'condicion_codigo'                  => $row['condicion_codigo'],
                        'condicion_nombre'                  => $row['condicion_nombre'],
                        'pago_codigo'                       => $row['pago_codigo'],
                        'pago_nombre'                       => $row['pago_nombre'],
                        'banca_codigo'                      => $row['banca_codigo'],
                        'banca_nombre'                      => $row['banca_nombre']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'comprobante_codigo'                => '',
                        'comprobante_tipo'                  => '',
                        'comprobante_timbrado_numero'       => '',
                        'comprobante_timbrado_vencimiento'  => '',
                        'comprobante_importe_numero'        => '',
                        'comprobante_importe_letra'         => '',
                        'comprobante_cantidad_impreso'      => '',
                        'comprobante_importe'               => '',
                        'movimiento_numero_original'        => '',
                        'movimiento_numero_reversion'       => '',
                        'movimiento_usuario_original'       => '',
                        'movimiento_usuario_reversion'      => '',
                        'movimiento_fecha_original'         => '',
                        'movimiento_fecha_reversion'        => '',
                        'movimiento_hora_original'          => '',
                        'movimiento_hora_reversion'         => '',
                        'operacion_numero'                  => '',
                        'operacion_cuota'                   => '',
                        'persona_nombre'                    => '',
                        'persona_documento'                 => '',
                        'persona_cuenta'                    => '',
                        'persona_direccion'                 => '',
                        'persona_telefono'                  => '',
                        'estado_codigo'                     => '',
                        'estado_nombre'                     => '',
                        'tipo_codigo'                       => '',
                        'tigo_nombre'                       => '',
                        'condicion_codigo'                  => '',
                        'condicion_nombre'                  => '',
                        'pago_codigo'                       => '',
                        'pago_nombre'                       => '',
                        'banca_codigo'                      => '',
                        'banca_nombre'                      => ''
                    );

                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 204, 'status' => 'ok', 'message' => 'No hay registros', 'data' => $detalle), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                }
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });

    $app->get('/v1/100/top03/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT TOP 3

            a.aacuen                        AS      operacion_cuenta,
            a.BFOPE1                        AS      operacion_numero,
            a.BfCant                        AS      operacion_cuota_cantidad,
            a.BfPend                        AS      operacion_cuota_pendiente,
            (a.BfCant - a.BfPend)           AS      operacion_cuota_cancelado,
            b.BeCta                         AS      operacion_proximo_cuota,
            CONVERT(date, b.Be1Vto, 103)    AS      operacion_proximo_vencimiento,
            c.COBPTOTCU                     AS      operacion_proximo_monto
            
            FROM FSD0122 a
            INNER JOIN FSD0171 b ON a.BFOPE1 = b.BeOpe1 AND a.AACUEN = b.AACUEN
            INNER JOIN COBPEN01 c ON a.BFOPE1 = c.COBPOPE AND a.AACUEN = c.COBPCUE AND b.BeCta = c.COBPCUO
            
            WHERE a.AACUEN = ? AND a.BfEsta = 7
            ORDER BY a.BFOPE1 DESC";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach ($row['operacion_proximo_vencimiento'] as $key => $value) {
                        if($key == 'date'){
                            $fecha = date_format(date_create($value), 'd/m/Y');
                        }
                    }
                    
                    $detalle = array(
                        'operacion_cuenta'              => $row['operacion_cuenta'],
                        'operacion_numero'              => $row['operacion_numero'],
                        'operacion_cuota_cantidad'      => $row['operacion_cuota_cantidad'],
                        'operacion_cuota_pendiente'     => $row['operacion_cuota_pendiente'],
                        'operacion_cuota_cancelado'     => $row['operacion_cuota_cancelado'],
                        'operacion_proximo_cuota'       => $row['operacion_proximo_cuota'],
                        'operacion_proximo_vencimiento' => $fecha,
                        'operacion_proximo_monto'       => $row['operacion_proximo_monto']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'operacion_cuenta'              => '',
                        'operacion_numero'              => '',
                        'operacion_cuota_cantidad'      => '',
                        'operacion_cuota_pendiente'     => '',
                        'operacion_cuota_cancelado'     => '',
                        'operacion_proximo_cuota'       => '',
                        'operacion_proximo_vencimiento' => '',
                        'operacion_proximo_monto'       => ''
                    );

                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 204, 'status' => 'ok', 'message' => 'No hay registros', 'data' => $detalle), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                }
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });

    $app->get('/v1/200/{codigo}/{fechaDesde}/{fechaHasta}/{estadoOperacion}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val01  = $request->getAttribute('codigo');
        $val02  = date_format(date_create($request->getAttribute('fechaDesde')), 'd/m/Y');
        $val03  = date_format(date_create($request->getAttribute('fechaHasta')), 'd/m/Y');
        $val04  = $request->getAttribute('estadoOperacion');
        
        if (isset($val01) && isset($val02) && isset($val03)) {
            $sql_0  = "SELECT
            a.COMPCABCOD                        AS      comprobante_codigo,
            a.COMPCABCTN                        AS      comprobante_timbrado_numero,
            CONVERT(date, a.COMPCABCTV, 103)    AS      comprobante_timbrado_vencimiento,
            a.COMPCABCNU                        AS      comprobante_numero,
            a.COMPCABCCI                        AS      comprobante_cantidad_impreso,
            a.COMPCABCIM                        AS      comprobante_importe_numero,
            a.COMPCABCIL                        AS      comprobante_importe_letra,

            a.COMPCABMNO                        AS      movimiento_numero_original,
            a.COMPCABMNR                        AS      movimiento_numero_reversion,
            a.COMPCABMUO                        AS      movimiento_usuario_original,
            a.COMPCABMUR                        AS      movimiento_usuario_reversion,
            CONVERT(date, a.COMPCABMFO, 103)    AS      movimiento_fecha_original,
            CONVERT(date, a.COMPCABMFR, 103)    AS      movimiento_fecha_reversion,
            a.COMPCABMHO                        AS      movimiento_hora_original,
            a.COMPCABMHR                        AS      movimiento_hora_reversion,

            a.COMPCABONU                        AS      operacion_numero,
            a.COMPCABOCU                        AS      operacion_cuota,

            a.COMPCABPNO                        AS      persona_nombre,
            a.COMPCABPDO                        AS      persona_documento,
            a.COMPCABPCU                        AS      persona_cuenta,
            a.COMPCABPDI                        AS      persona_direccion,
            a.COMPCABPTE                        AS      persona_telefono,

            b.COMPESTCOD                        AS      estado_codigo,
            b.COMPESTNOM                        AS      estado_nombre,

            c.COMPTIPCOD                        AS      tipo_codigo,
            c.COMPTIPNOM                        AS      tigo_nombre,

            d.COMPCONCOD                        AS      condicion_codigo,
            d.COMPCONNOM                        AS      condicion_nombre,

            e.COMPPAGCOD                        AS      pago_codigo,
            e.COMPPAGNOM                        AS      pago_nombre, 
            
            f.crbanca                           AS      banca_codigo,
            f.crnomb                            AS      banca_nombre

            FROM COMPCAB a
            INNER JOIN COMPEST b ON a.COMPESTCOD = b.COMPESTCOD
            INNER JOIN COMPTIP c ON a.COMPTIPCOD = c.COMPTIPCOD
            INNER JOIN COMPCON d ON a.COMPCONCOD = d.COMPCONCOD
            INNER JOIN COMPPAG e ON a.COMPPAGCOD = e.COMPPAGCOD
            INNER JOIN FST020 f ON a.COMPCABOBA = f.crbanca

            WHERE a.COMPCABPCU = ? AND a.COMPCABMFO >= ? AND a.COMPCABMFO <= ? 
            ORDER BY a.COMPCABMFO DESC";

            $sql_1  = "SELECT
            a.COMPCABCOD                        AS      comprobante_codigo,
            a.COMPCABCTN                        AS      comprobante_timbrado_numero,
            CONVERT(date, a.COMPCABCTV, 103)    AS      comprobante_timbrado_vencimiento,
            a.COMPCABCNU                        AS      comprobante_numero,
            a.COMPCABCCI                        AS      comprobante_cantidad_impreso,
            a.COMPCABCIM                        AS      comprobante_importe_numero,
            a.COMPCABCIL                        AS      comprobante_importe_letra,

            a.COMPCABMNO                        AS      movimiento_numero_original,
            a.COMPCABMNR                        AS      movimiento_numero_reversion,
            a.COMPCABMUO                        AS      movimiento_usuario_original,
            a.COMPCABMUR                        AS      movimiento_usuario_reversion,
            CONVERT(date, a.COMPCABMFO, 103)    AS      movimiento_fecha_original,
            CONVERT(date, a.COMPCABMFR, 103)    AS      movimiento_fecha_reversion,
            a.COMPCABMHO                        AS      movimiento_hora_original,
            a.COMPCABMHR                        AS      movimiento_hora_reversion,

            a.COMPCABONU                        AS      operacion_numero,
            a.COMPCABOCU                        AS      operacion_cuota,

            a.COMPCABPNO                        AS      persona_nombre,
            a.COMPCABPDO                        AS      persona_documento,
            a.COMPCABPCU                        AS      persona_cuenta,
            a.COMPCABPDI                        AS      persona_direccion,
            a.COMPCABPTE                        AS      persona_telefono,

            b.COMPESTCOD                        AS      estado_codigo,
            b.COMPESTNOM                        AS      estado_nombre,

            c.COMPTIPCOD                        AS      tipo_codigo,
            c.COMPTIPNOM                        AS      tigo_nombre,

            d.COMPCONCOD                        AS      condicion_codigo,
            d.COMPCONNOM                        AS      condicion_nombre,

            e.COMPPAGCOD                        AS      pago_codigo,
            e.COMPPAGNOM                        AS      pago_nombre, 
            
            f.crbanca                           AS      banca_codigo,
            f.crnomb                            AS      banca_nombre

            FROM COMPCAB a
            INNER JOIN COMPEST b ON a.COMPESTCOD = b.COMPESTCOD
            INNER JOIN COMPTIP c ON a.COMPTIPCOD = c.COMPTIPCOD
            INNER JOIN COMPCON d ON a.COMPCONCOD = d.COMPCONCOD
            INNER JOIN COMPPAG e ON a.COMPPAGCOD = e.COMPPAGCOD
            INNER JOIN FST020 f ON a.COMPCABOBA = f.crbanca
            INNER JOIN FSD0122 g ON a.COMPCABONU = g.bfope1 AND a.COMPCABPCU = g.aacuen

            WHERE a.COMPCABPCU = ? AND a.COMPCABMFO >= ? AND a.COMPCABMFO <= ? AND g.bfEsta = ?
            ORDER BY a.COMPCABCOD DESC";

            if($val04 == 1){
                $parm   = array($val01, $val02, $val03);
                $stmt   = sqlsrv_query($mssqlConn, $sql_0, $parm);
            } else {
                $parm   = array($val01, $val02, $val03, $val04);
                $stmt   = sqlsrv_query($mssqlConn, $sql_1, $parm);
            }

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach ($row['comprobante_timbrado_vencimiento'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_timbrado = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    foreach ($row['movimiento_fecha_original'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_original = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    foreach ($row['movimiento_fecha_reversion'] as $key => $value) {
                        if($key == 'date'){
                            $fecha_reversion = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    if ($row['operacion_cuota'] != 0){
                        $tipo = 'COBRO DE CUOTA';
                    } else {
                        $tipo = 'DESEMBOLSO';
                    }
                    
                    $detalle = array(
                        'comprobante_codigo'                => $row['comprobante_codigo'],
                        'comprobante_tipo'                  => $tipo,
                        'comprobante_timbrado_numero'       => $row['comprobante_timbrado_numero'],
                        'comprobante_timbrado_vencimiento'  => $fecha_timbrado,
                        'comprobante_numero'                => $row['comprobante_numero'],
                        'comprobante_cantidad_impreso'      => $row['comprobante_cantidad_impreso'],
                        'comprobante_importe_numero'        => $row['comprobante_importe_numero'],
                        'comprobante_importe_letra'         => $row['comprobante_importe_letra'],
                        'movimiento_numero_original'        => $row['movimiento_numero_original'],
                        'movimiento_numero_reversion'       => $row['movimiento_numero_reversion'],
                        'movimiento_usuario_original'       => $row['movimiento_usuario_original'],
                        'movimiento_usuario_reversion'      => $row['movimiento_usuario_reversion'],
                        'movimiento_fecha_original'         => $fecha_original,
                        'movimiento_fecha_reversion'        => $fecha_reversion,
                        'movimiento_hora_original'          => $row['movimiento_hora_original'],
                        'movimiento_hora_reversion'         => $row['movimiento_hora_reversion'],
                        'operacion_numero'                  => $row['operacion_numero'],
                        'operacion_cuota'                   => $row['operacion_cuota'],
                        'persona_nombre'                    => $row['persona_nombre'],
                        'persona_documento'                 => $row['persona_documento'],
                        'persona_cuenta'                    => $row['persona_cuenta'],
                        'persona_direccion'                 => $row['persona_direccion'],
                        'persona_telefono'                  => $row['persona_telefono'],
                        'estado_codigo'                     => $row['estado_codigo'],
                        'estado_nombre'                     => $row['estado_nombre'],
                        'tipo_codigo'                       => $row['tipo_codigo'],
                        'tigo_nombre'                       => $row['tigo_nombre'],
                        'condicion_codigo'                  => $row['condicion_codigo'],
                        'condicion_nombre'                  => $row['condicion_nombre'],
                        'pago_codigo'                       => $row['pago_codigo'],
                        'pago_nombre'                       => $row['pago_nombre'],
                        'banca_codigo'                      => $row['banca_codigo'],
                        'banca_nombre'                      => $row['banca_nombre']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'comprobante_codigo'                => '',
                        'comprobante_tipo'                  => '',
                        'comprobante_timbrado_numero'       => '',
                        'comprobante_timbrado_vencimiento'  => '',
                        'comprobante_importe_numero'        => '',
                        'comprobante_importe_letra'         => '',
                        'comprobante_cantidad_impreso'      => '',
                        'comprobante_importe'               => '',
                        'movimiento_numero_original'        => '',
                        'movimiento_numero_reversion'       => '',
                        'movimiento_usuario_original'       => '',
                        'movimiento_usuario_reversion'      => '',
                        'movimiento_fecha_original'         => '',
                        'movimiento_fecha_reversion'        => '',
                        'movimiento_hora_original'          => '',
                        'movimiento_hora_reversion'         => '',
                        'operacion_numero'                  => '',
                        'operacion_cuota'                   => '',
                        'persona_nombre'                    => '',
                        'persona_documento'                 => '',
                        'persona_cuenta'                    => '',
                        'persona_direccion'                 => '',
                        'persona_telefono'                  => '',
                        'estado_codigo'                     => '',
                        'estado_nombre'                     => '',
                        'tipo_codigo'                       => '',
                        'tigo_nombre'                       => '',
                        'condicion_codigo'                  => '',
                        'condicion_nombre'                  => '',
                        'pago_codigo'                       => '',
                        'pago_nombre'                       => '',
                        'banca_codigo'                      => '',
                        'banca_nombre'                      => ''
                    );

                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 204, 'status' => 'ok', 'message' => 'No hay registros', 'data' => $detalle), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                }
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });