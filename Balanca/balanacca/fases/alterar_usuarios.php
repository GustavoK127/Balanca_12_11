<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: altera_usuarios.php', true, 303);
    exit;
}
$nome = trim((string)($_POST['Nome'] ?? ''));
$email = trim((string)($_POST['Email'] ?? ''));
$senha = (string)($_POST['Senha'] ?? '');
$emailOriginal = (string)($_POST['emailOriginal'] ?? '');

if ($nome === '' || preg_match_all('/./us', $nome) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 80 || preg_match_all('/./us', $senha) > 12 || !filter_var($emailOriginal, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: altera_usuarios.php', true, 303);
    exit;
}
try {
    $comando = mysqli_prepare($conexao, 'SELECT Email FROM usuarios WHERE Email = ? LIMIT 1');
    mysqli_stmt_bind_param($comando, 's', $email);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $outro = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    if ($outro && strcasecmp($outro['Email'], $emailOriginal) !== 0) {
        $_SESSION['erro'] = 'Este e-mail já está cadastrado.';
        header('Location: altera_usuarios.php', true, 303);
    exit;
    }
    if ($senha === '') {
        $comando = mysqli_prepare($conexao, 'UPDATE usuarios SET Nome = ?, Email = ? WHERE Email = ?');
        mysqli_stmt_bind_param($comando, 'sss', $nome, $email, $emailOriginal);
    } else {
        $comando = mysqli_prepare($conexao, 'UPDATE usuarios SET Nome = ?, Email = ?, Senha = ? WHERE Email = ?');
        mysqli_stmt_bind_param($comando, 'ssss', $nome, $email, $senha, $emailOriginal);
    }
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    if (strcasecmp($_SESSION['dsEmail'], $emailOriginal) === 0) {
        $_SESSION['usuario'] = $nome;
        $_SESSION['dsEmail'] = $email;
    }
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = 'Não foi possível salvar os dados.';
}
header('Location: altera_usuarios.php', true, 303);
exit;
