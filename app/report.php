<?php
    $app->get('/v1/report/100/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT

            a.cucuen                        AS      caja_cuenta,
            a.cuope1                        AS      caja_operacion,
            a.cucuot                        AS      caja_cuota,
            CONVERT(date, a.Cufech, 103)    AS      caja_fecha,
            a.cuhora                        AS      caja_hora,
            a.CuMont                        AS      caja_monto,
            a.cumonn                        AS      caja_numero_movimiento,
            a.cufact                        AS      caja_numero_factura,
            a.CURECIBO                      AS      caja_numero_recibo,
            a.ClUsu                         AS      caja_usuario,
            b.bfcant                        AS      operacion_cantidad_cuota,
            c.aanom                         AS      cliente_nombre_completo,
            c.AgDocu                        AS      cliente_documento_tipo,
            c.AaDocu                        AS      cliente_documento_numero


            FROM FSD015 a
            INNER JOIN FSD0122 b ON a.cucuen = b.aacuen AND a.cuope1 = b.bfope1
            INNER JOIN FSD0011 c ON a.cucuen = c.aacuen

            WHERE a.cumonn = ?
            ORDER BY a.Cufech DESC";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql_0, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach ($row['caja_fecha'] as $key => $value) {
                        if($key == 'date'){
                            $fecha = date_format(date_create($value), 'd/m/Y');
                        }
                    }

                    if ($row['caja_cuota'] != 0){
                        $movimiento = 'COBRO DE CUOTA';
                    } else {
                        $movimiento = 'DESEMBOLSO';
                    }

                    if ($row['cliente_documento_tipo'] == 1){
                        $tipoDocumento = 'C.I.P.'
                    } else {
                        $tipoDocumento = 'R.U.C.'
                    }
   
                    $detalle = array(
                        'caja_cuenta'               => $row['caja_cuenta'],
                        'caja_operacion'            => $row['caja_operacion'],
                        'caja_movimiento'           => $movimiento,
                        'caja_cuota'                => $row['caja_cuota'],
                        'caja_fecha'                => $fecha,
                        'caja_hora'                 => $row['caja_hora'],
                        'caja_monto'                => $row['caja_monto'],
                        'caja_numero_movimiento'    => $row['caja_numero_movimiento'],
                        'caja_numero_factura'       => $row['caja_numero_factura'],
                        'caja_numero_recibo'        => $row['caja_numero_recibo'],
                        'caja_usuario'              => $row['caja_usuario'],
                        'operacion_cantidad_cuota'  => $row['operacion_cantidad_cuota'],
                        'cliente_nombre_completo'   => $row['cliente_nombre_completo'],
                        'cliente_documento_tipo'    => $tipoDocumento ,
                        'cliente_documento_numero'  => $row['cliente_documento_numero']
                    );

                    $result[] = $detalle;
                }

                if (isset($result)){
                    header("Content-Type: application/json; charset=utf-8");
                    $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Consulta con exito', 'data' => $result), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
                } else {
                    $detalle = array(
                        'caja_cuenta'               => '',
                        'caja_operacion'            => '',
                        'caja_movimiento'           => '',
                        'caja_cuota'                => '',
                        'caja_fecha'                => '',
                        'caja_hora'                 => '',
                        'caja_monto'                => '',
                        'caja_numero_movimiento'    => '',
                        'caja_numero_factura'       => '',
                        'caja_numero_recibo'        => '',
                        'caja_usuario'              => '',
                        'operacion_cantidad_cuota'  => '',
                        'cliente_nombre_completo'   => '',
                        'cliente_documento_tipo'    => '',
                        'cliente_documento_numero'  => ''
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