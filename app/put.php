<?php
    $app->put('/v1/200/cantidad/{codigo}', function($request) {
        require __DIR__.'/../src/connect.php';

        $val00      = $request->getAttribute('codigo');
        
        if (isset($val00)) {
            $sql    = "UPDATE COMPCAB SET COMPCABCCI = COMPCABCCI + 1 WHERE COMPCABCOD = ?";
            $parm   = array($val00);
            $stmt   = sqlsrv_query($mssqlConn, $sql, $parm);

            if ($stmt === FALSE) {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 204, 'status' => 'failure', 'message' => 'Hubo un error al momento de ACTUALIZAR', 'erros' => sqlsrv_errors()), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            } else {
                header("Content-Type: application/json; charset=utf-8");
                $json = json_encode(array('code' => 200, 'status' => 'ok', 'message' => 'Success UPDATE'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
            }

            sqlsrv_free_stmt($stmt);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            $json = json_encode(array('code' => 400, 'status' => 'error', 'message' => 'Verifique, algún campo esta vacio.'), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        }

        sqlsrv_close($mssqlConn);
        
        return $json;
    });