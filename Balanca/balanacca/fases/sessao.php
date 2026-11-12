<?php
session_start();
if (empty($_SESSION['autenticado']) || !isset($_SESSION['usuario'], $_SESSION['dsEmail'])) {
    header('Location: ../login/index.php', true, 303);
    exit;
}
require_once __DIR__ . '/../config/conexao.php';
$comando = mysqli_prepare($conexao, 'SELECT Nome FROM usuarios WHERE Email = ? LIMIT 1');
mysqli_stmt_bind_param($comando, 's', $_SESSION['dsEmail']);
mysqli_stmt_execute($comando);
$resultado = mysqli_stmt_get_result($comando);
$dados = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($comando);
if (!$dados) {
    header('Location: ../login/sair.php', true, 303);
    exit;
}
$usuario = $dados['Nome'];
$_SESSION['usuario'] = $usuario;
if (empty($_SESSION['token'])) $_SESSION['token'] = bin2hex(random_bytes(32));
