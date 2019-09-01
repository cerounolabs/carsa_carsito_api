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

    $app->get('/v1/100/top10/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getAttribute('codigo');
        
        if (isset($val01)) {
            $sql    = "SELECT TOP 10

            a.cucuen                        AS      caja_cuenta,
            a.cuope1                        AS      caja_operacion,
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
                        'caja_operacion'            => $row['caja_operacion'],
                        'caja_fecha'                => $fecha,
                        'caja_hora'                 => $row['caja_hora'],
                        'caja_monto'                => $row['caja_monto'],
                        'caja_numero_movimiento'    => $row['caja_numero_movimiento'],
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