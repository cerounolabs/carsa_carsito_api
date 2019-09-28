<?php
    $mssqlName = "SRVPRIM01\GRUPOSQLSRV2, 1433";
    $mssqlInfo = array("Database"=>"BDPRODUC", "UID"=>"mifactura", "PWD"=>"carsa_2019", "CharacterSet"=>"UTF-8", "MultipleActiveResultSets"=>"false");
    $mssqlConn = sqlsrv_connect($mssqlName, $mssqlInfo);