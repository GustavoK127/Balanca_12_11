<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: inclui.php', true, 303);
    exit;
}
$dsFase = trim((string)($_POST['dsFase'] ?? ''));
if ($dsFase === '' || preg_match_all('/./us', $dsFase) > 45) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: inclui.php', true, 303);
    exit;
}
try {

    $sql = "INSERT INTO fases (dsFase) VALUES (?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 's', $dsFase);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = (int)$erro->getCode() === 1062 ? 'Já existe um registro com esses dados.' : 'Não foi possível salvar os dados.';
}
header('Location: inclui.php', true, 303);
exit;
