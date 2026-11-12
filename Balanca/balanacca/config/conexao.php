<?php
$servidor = 'localhost';
$usuario_db = 'root';
$senha_db = '12345';
$banco = 'balanca_mas_nao_cai';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conexao = mysqli_connect($servidor, $usuario_db, $senha_db, $banco);
    mysqli_set_charset($conexao, 'utf8mb4');
} catch (mysqli_sql_exception $erro) {
    die('Erro ao conectar ao banco de dados.');
}
