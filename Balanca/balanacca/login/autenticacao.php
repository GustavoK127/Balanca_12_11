<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}
require_once __DIR__ . '/../config/conexao.php';
$email = trim((string)($_POST['usuario'] ?? ''));
$senha = (string)($_POST['senha'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    header('Location: index.php?erro=1', true, 303);
    exit;
}
try {
    $comando = mysqli_prepare($conexao, 'SELECT Nome, Email, Senha FROM usuarios WHERE Email = ? LIMIT 1');
    mysqli_stmt_bind_param($comando, 's', $email);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $dados = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    $valida = $dados && $senha === $dados['Senha'];
    if ($valida) {
        session_regenerate_id(true);
        $_SESSION = ['autenticado' => true, 'usuario' => $dados['Nome'], 'dsEmail' => $dados['Email'], 'token' => bin2hex(random_bytes(32))];
        header('Location: ../menu/mostra.php', true, 303);
    exit;
    }
} catch (mysqli_sql_exception $erro) {
    header('Location: index.php?erro=2', true, 303);
    exit;
}
$_SESSION = [];
header('Location: index.php?erro=1', true, 303);
exit;
