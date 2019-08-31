<?php
    $app->post('/v1/000/index', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getParsedBody()['usuario_var01'];
        $val02  = $request->getParsedBody()['usuario_var02'];
        $val03  = $request->getParsedBody()['usuario_var03'];
        $val04  = $request->getParsedBody()['usuario_var04'];
        $val05  = $request->getParsedBody()['usuario_var05'];
        $val06  = $request->getParsedBody()['usuario_var06'];
        $val07  = $request->getParsedBody()['usuario_var07'];
        $val08  = $request->getParsedBody()['usuario_var08'];
        $val09  = $request->getParsedBody()['usuario_var09'];
        $val10  = '';//$request->getParsedBody()['usuario_var10'];
        $val11  = date('Ymd H:i:s.v');
        $val12  = date('H:i:s');
        
        if (isset($val01) && isset($val02) && isset($val03) && isset($val04) && isset($val05) && isset($val06) && isset($val07) && isset($val08) && isset($val09)) {
            $sql    = "INSERT INTO COMPLOG (COMPLOGTEC, COMPLOGTDC, COMPLOGDOC, COMPLOGMAI, COMPLOGTEL, COMPLOGFEC, COMPLOGHOR, COMPLOGUUI, COMPLOGPIN, COMPLOGHUI, COMPLOGHUH, COMPLOGHUA, COMPLOGHUR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $parm   = array('A', $val01, $val02, $val03, $val10, $val11, $val12, $val05, $val04, $val06, $val07, $val08, $val09);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ingresar', 'erros' => sqlsrv_errors()), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Ingreso correcto'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });

    $app->get('/v1/000/login/{uuid}', function($request) {
        require __DIR__.'/../src/connect.php';

		$val01  = $request->getAttribute('uuid');
        
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
                    $detalle = array(
                        'cliente_cuenta'            => $row['cliente_cuenta'],
                        'cliente_nombre'            => $row['cliente_nombre'],
                        'cliente_apellido'          => $row['cliente_apellido'],
                        'cliente_documento_tipo'    => $row['cliente_documento_tipo'],
                        'cliente_documento_numero'  => $row['cliente_documento_numero'],
                        'cliente_fecha_nacimiento'  => $row['cliente_fecha_nacimiento'],
                        'login_uuid'                => $row['login_uuid'],
                        'login_mail'                => $row['login_mail'],
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