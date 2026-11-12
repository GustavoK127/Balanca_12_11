<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: exclui.php', true, 303);
    exit;
}
$id = filter_input(INPUT_POST, 'idFase', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: exclui.php', true, 303);
    exit;
}
try {

    $sql = 'DELETE FROM fases WHERE idFase = ?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $id);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = 'Não foi possível excluir. Verifique se o registro está vinculado a outro cadastro.';
}
header('Location: exclui.php', true, 303);
exit;
