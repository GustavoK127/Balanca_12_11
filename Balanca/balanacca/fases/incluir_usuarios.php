<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: inclui_usuarios.php', true, 303);
    exit;
}
$nome = trim((string)($_POST['Nome'] ?? ''));
$email = trim((string)($_POST['Email'] ?? ''));
$senha = (string)($_POST['Senha'] ?? '');

if ($nome === '' || preg_match_all('/./us', $nome) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 80 || $senha === '' || preg_match_all('/./us', $senha) > 12) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: inclui_usuarios.php', true, 303);
    exit;
}
try {
    $comando = mysqli_prepare($conexao, 'SELECT Email FROM usuarios WHERE Email = ? LIMIT 1');
    mysqli_stmt_bind_param($comando, 's', $email);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $outro = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    if ($outro) {
        $_SESSION['erro'] = 'Este e-mail já está cadastrado.';
        header('Location: inclui_usuarios.php', true, 303);
    exit;
    }
    $comando = mysqli_prepare($conexao, 'INSERT INTO usuarios (Nome, Email, Senha) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($comando, 'sss', $nome, $email, $senha);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);

} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = 'Não foi possível salvar os dados.';
}
header('Location: inclui_usuarios.php', true, 303);
exit;
