<?php
    $mssqlName = "SRVDESA01, 1433";
    $mssqlInfo = array("Database"=>"DESTRASJUD", "UID"=>"czelaya", "PWD"=>"carsa_2019", "CharacterSet"=>"UTF-8", "MultipleActiveResultSets"=>"false");
    $mssqlConn = sqlsrv_connect($mssqlName, $mssqlInfo);