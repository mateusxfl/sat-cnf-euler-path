<?php

    $dados = array(array(2,-2), array(-2,-3));

    $resultado = shell_exec('glucose.py '.escapeshellarg(json_encode($dados)));
    
    echo utf8_encode($resultado);

?>