<?php
require_once __DIR__ . '/sessao.php';
$id = filter_input(INPUT_POST, 'idFuncao', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: altera_funcoes.php', true, 303);
    exit;
}
$sql = 'SELECT idFuncao, dsFuncao FROM funcoes WHERE idFuncao = ?';
$comando = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($comando, 'i', $id);
mysqli_stmt_execute($comando);
$resultado = mysqli_stmt_get_result($comando);
$linha = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($comando);
if (!$linha) {
    header('Location: altera_funcoes.php', true, 303);
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
        <h2>ALTERAÇÃO DE FUNÇÕES</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a class="voltar" href="altera_funcoes.php">Voltar</a>
        <form method="post" action="alterar_funcoes.php">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <table class="formulario">
            <tr>
                <td><label for="dsFuncao">Nome da Função</label></td>
                <td><input type="text" id="dsFuncao" name="dsFuncao" maxlength="45" value="<?php echo htmlspecialchars($linha['dsFuncao']); ?>" required autofocus></td>
            </tr>
            <tr>
                <td><label for="idFuncao">Código</label></td>
                <td><input type="number" id="idFuncao" name="idFuncao" min="1" value="<?php echo htmlspecialchars($linha['idFuncao']); ?>" readonly></td>
            </tr>
                <tr><td></td><td><input type="submit" value="Alterar"><input type="reset" value="Limpar"></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
