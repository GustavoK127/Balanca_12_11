<?php
require_once __DIR__ . '/sessao.php';
$emailOriginal = (string)($_POST['emailOriginal'] ?? '');
$comando = mysqli_prepare($conexao, 'SELECT Nome, Email FROM usuarios WHERE Email = ? LIMIT 1');
mysqli_stmt_bind_param($comando, 's', $emailOriginal);
mysqli_stmt_execute($comando);
$resultado = mysqli_stmt_get_result($comando);
$linha = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($comando);
if (!$linha) {
    header('Location: altera_usuarios.php', true, 303);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema XYZ</title>
    <link rel="stylesheet" href="../estilos/alterar.css">
    <link rel="stylesheet" href="../estilos/ajustes.css">
</head>
<body>
    <div class="caixa">
        <h2>ALTERAÇÃO DE USUÁRIOS</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a href="altera_usuarios.php" class="voltar">Voltar</a>
        <form method="post" action="alterar_usuarios.php">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <input type="hidden" name="emailOriginal" value="<?php echo htmlspecialchars($linha['Email']); ?>">
            <table class="formulario">
            <tr>
                <td><label for="Nome">Nome</label></td>
                <td><input type="text" id="Nome" name="Nome" maxlength="50" value="<?php echo htmlspecialchars($linha['Nome']); ?>" required autofocus></td>
            </tr>
            <tr>
                <td><label for="Email">E-mail</label></td>
                <td><input type="email" id="Email" name="Email" maxlength="80" value="<?php echo htmlspecialchars($linha['Email']); ?>" required></td>
            </tr>
                <tr>
                    <td><label for="Senha">Nova senha</label></td>
                    <td><input type="password" id="Senha" name="Senha" maxlength="12" autocomplete="new-password"><small>Deixe em branco para manter a senha.</small></td>
                </tr>
                <tr><td></td><td><input type="submit" value="Alterar"><input type="reset" value="Limpar"></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
