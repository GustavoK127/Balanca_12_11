<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: exclui_usuarios.php', true, 303);
    exit;
}
$email = (string)($_POST['emailOriginal'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: exclui_usuarios.php', true, 303);
    exit;
}
try {
    $comando = mysqli_prepare($conexao, 'DELETE FROM usuarios WHERE Email = ?');
    mysqli_stmt_bind_param($comando, 's', $email);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    if (strcasecmp($_SESSION['dsEmail'], $email) === 0) {
        header('Location: ../login/sair.php', true, 303);
    exit;
    }
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = 'Não foi possível excluir o usuário.';
}
header('Location: exclui_usuarios.php', true, 303);
exit;
