<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: altera_funcoes.php', true, 303);
    exit;
}
$id = filter_input(INPUT_POST, 'idFuncao', FILTER_VALIDATE_INT);
$dsFuncao = trim((string)($_POST['dsFuncao'] ?? ''));
if (!$id || $id < 1 || $dsFuncao === '' || preg_match_all('/./us', $dsFuncao) > 45) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: altera_funcoes.php', true, 303);
    exit;
}
try {
    $sql = 'UPDATE funcoes SET dsFuncao = ? WHERE idFuncao = ?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'si', $dsFuncao, $id);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = (int)$erro->getCode() === 1062 ? 'Já existe um registro com esses dados.' : 'Não foi possível salvar os dados.';
}
header('Location: altera_funcoes.php', true, 303);
exit;
