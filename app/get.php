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

            a.cucuen                        AS      caja_cuenta,
            a.cuope1                        AS      caja_operacion,
            a.cucuot                        AS      caja_cuota,
            CONVERT(date, a.Cufech, 103)    AS      caja_fecha,
            a.cuhora                        AS      caja_hora,
            a.CuMont                        AS      caja_monto,
            a.cumonn                        AS      caja_numero_movimiento,
            a.cufact                        AS      caja_numero_factura,
            a.CURECIBO                      AS      caja_numero_recibo
            
            FROM FSD015 a

            WHERE a.cucuen = ?
            ORDER BY a.Cufech DESC";

            $parm   = array($val01);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

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
                    
                    $detalle = array(
                        'caja_cuenta'               => $row['caja_cuenta'],
                        'caja_operacion'            => number_format($row['caja_operacion'], 0, ',', ''),
                        'caja_cuota'                => number_format($row['caja_cuota'], 0, ',', ''),
                        'caja_fecha'                => $fecha,
                        'caja_hora'                 => $row['caja_hora'],
                        'caja_monto'                => number_format($row['caja_monto'], 0, ',', ' '),
                        'caja_numero_movimiento'    => number_format($row['caja_numero_movimiento'], 0, ',', ''),
                        'caja_numero_factura'       => $row['caja_numero_factura'],
                        'caja_numero_recibo'        => $row['caja_numero_recibo']
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
                        'caja_cuota'                => '',
                        'caja_fecha'                => '',
                        'caja_hora'                 => '',
                        'caja_monto'                => '',
                        'caja_numero_movimiento'    => '',
                        'caja_numero_factura'       => '',
                        'caja_numero_recibo'        => ''
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
            
            WHERE a.AACUEN = ? AND a.BfEsta = 7 AND b.BeCta = (a.BfCant - a.BfPend + 1)
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
                        'operacion_numero'              => number_format($row['operacion_numero'], 0, ',', ''),
                        'operacion_cuota_cantidad'      => number_format($row['operacion_cuota_cantidad'], 0, ',', ''),
                        'operacion_cuota_pendiente'     => number_format($row['operacion_cuota_pendiente'], 0, ',', ''),
                        'operacion_cuota_cancelado'     => number_format($row['operacion_cuota_cancelado'], 0, ',', ''),
                        'operacion_proximo_cuota'       => number_format($row['operacion_proximo_cuota'], 0, ',', ''),
                        'operacion_proximo_vencimiento' => $fecha,
                        'operacion_proximo_monto'       => number_format($row['operacion_proximo_monto'], 0, ',', ' ')
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

    $app->get('/v1/200/{codigo}/{fechaDesde}/{fechaHasta}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val01  = $request->getAttribute('codigo');
        $val02  = date_format(date_create($request->getAttribute('fechaDesde')), 'd/m/Y');
        $val03  = date_format(date_create($request->getAttribute('fechaHasta')), 'd/m/Y');
        
        if (isset($val01) && isset($val02) && isset($val03)) {
            $sql    = "SELECT

            a.cucuen                        AS      caja_cuenta,
            a.cuope1                        AS      caja_operacion,
            a.cucuot                        AS      caja_cuota,
            CONVERT(date, a.Cufech, 103)    AS      caja_fecha,
            a.cuhora                        AS      caja_hora,
            a.CuMont                        AS      caja_monto,
            a.cumonn                        AS      caja_numero_movimiento,
            a.cufact                        AS      caja_numero_factura,
            a.CURECIBO                      AS      caja_numero_recibo
            
            FROM FSD015 a

            WHERE a.cucuen = ? AND a.Cufech >= ? AND a.Cufech <= ?
            ORDER BY a.Cufech DESC";

            $parm   = array($val01, $val02, $val03);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

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
                    
                    $detalle = array(
                        'caja_cuenta'               => $row['caja_cuenta'],
                        'caja_operacion'            => number_format($row['caja_operacion'], 0, ',', ''),
                        'caja_cuota'                => number_format($row['caja_cuota'], 0, ',', ''),
                        'caja_fecha'                => $fecha,
                        'caja_hora'                 => $row['caja_hora'],
                        'caja_monto'                => number_format($row['caja_monto'], 0, ',', ' '),
                        'caja_numero_movimiento'    => number_format($row['caja_numero_movimiento'], 0, ',', ''),
                        'caja_numero_factura'       => $row['caja_numero_factura'],
                        'caja_numero_recibo'        => $row['caja_numero_recibo']
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
                        'caja_cuota'                => '',
                        'caja_fecha'                => '',
                        'caja_hora'                 => '',
                        'caja_monto'                => '',
                        'caja_numero_movimiento'    => '',
                        'caja_numero_factura'       => '',
                        'caja_numero_recibo'        => ''
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