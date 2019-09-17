<?php
    $app->get('/v1/report/100/cabecera/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT
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

            WHERE a.COMPCABCOD = ?
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

    $app->get('/v1/report/100/detalle/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT
            a.COMPCABCOD                        AS      detalle_comprobante_codigo,
            a.COMPDETLIN                        AS      detalle_comprobante_item,
            a.COMPDETGRA                        AS      detalle_comprobante_gravado,
            a.COMPDETIVA                        AS      detalle_comprobane_iva,
            a.COMPDETTOT                        AS      detalle_comprobante_total,

            b.COMPCTOCOD                        AS      detalle_concepto_codigo,
            b.COMPCTONOM                        AS      detalle_concepto_nombre,

            c.COMPIMPCOD                        AS      detalle_impuesto_codigo,
            c.COMPIMPNOM                        AS      detalle_impuesto_nombre

            FROM COMPDET a
            INNER JOIN COMPCTO b ON a.COMPCTOCOD = b.COMPCTOCOD
            INNER JOIN COMPIMP c ON a.COMPIMPCOD = c.COMPIMPCOD

            WHERE a.COMPCABCOD = ?
            ORDER BY a.COMPCABCOD, a.COMPDETLIN";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {                    
                    $detalle = array(
                        'detalle_comprobante_codigo'        => $row['detalle_comprobante_codigo'],
                        'detalle_comprobante_item'          => $row['detalle_comprobante_item'],
                        'detalle_concepto_codigo'           => $row['detalle_concepto_codigo'],
                        'detalle_concepto_nombre'           => $row['detalle_concepto_nombre'],
                        'detalle_impuesto_codigo'           => $row['detalle_impuesto_codigo'],
                        'detalle_impuesto_nombre'           => $row['detalle_impuesto_nombre'],
                        'detalle_comprobante_gravado'       => $row['detalle_comprobante_gravado'],
                        'detalle_comprobane_iva'            => $row['detalle_comprobane_iva'],
                        'detalle_comprobante_total'         => $row['detalle_comprobante_total']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'comprobante_codigo'                => '',
                        'detalle_comprobante_item'          => '',
                        'detalle_concepto_codigo'           => '',
                        'detalle_concepto_nombre'           => '',
                        'detalle_impuesto_codigo'           => '',
                        'detalle_impuesto_nombre'           => '',
                        'detalle_comprobante_gravado'       => '',
                        'detalle_comprobane_iva'            => '',
                        'detalle_comprobante_total'         => ''
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