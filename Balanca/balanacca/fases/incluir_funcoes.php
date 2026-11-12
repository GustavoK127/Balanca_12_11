<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: inclui_funcoes.php', true, 303);
    exit;
}
$dsFuncao = trim((string)($_POST['dsFuncao'] ?? ''));
if ($dsFuncao === '' || preg_match_all('/./us', $dsFuncao) > 45) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: inclui_funcoes.php', true, 303);
    exit;
}
try {
    $sql = 'INSERT INTO funcoes (dsFuncao) VALUES (?)';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 's', $dsFuncao);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = (int)$erro->getCode() === 1062 ? 'Já existe um registro com esses dados.' : 'Não foi possível salvar os dados.';
}
header('Location: inclui_funcoes.php', true, 303);
exit;
