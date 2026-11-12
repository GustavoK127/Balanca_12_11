<?php
require_once __DIR__ . '/sessao.php';
$id = filter_input(INPUT_POST, 'idEngenheiro', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: altera_engenheiros.php', true, 303);
    exit;
}
$sql = 'SELECT idEngenheiro, dsNome, nrCREA, dtAdmissao FROM engenheiros' . ' WHERE idEngenheiro = ?';
$comando = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($comando, 'i', $id);
mysqli_stmt_execute($comando);
$resultado = mysqli_stmt_get_result($comando);
$linha = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($comando);
if (!$linha) {
    header('Location: altera_engenheiros.php', true, 303);
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
        <h2>ALTERAÇÃO DE ENGENHEIROS</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a class="voltar" href="altera_engenheiros.php">Voltar</a>
        <form method="post" action="alterar_engenheiros.php">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <table class="formulario">
            <tr>
                <td><label for="dsNome">Nome do Engenheiro</label></td>
                <td><input type="text" id="dsNome" name="dsNome" maxlength="60" value="<?php echo htmlspecialchars($linha['dsNome']); ?>" required autofocus></td>
            </tr>
            <tr>
                <td><label for="nrCREA">Número do CREA</label></td>
                <td><input type="text" id="nrCREA" name="nrCREA" maxlength="20" value="<?php echo htmlspecialchars($linha['nrCREA']); ?>" required></td>
            </tr>
            <tr>
                <td><label for="dtAdmissao">Data de Admissão</label></td>
                <td><input type="date" id="dtAdmissao" name="dtAdmissao" value="<?php echo htmlspecialchars($linha['dtAdmissao']); ?>" required></td>
            </tr>
            <tr>
                <td><label for="idEngenheiro">Código</label></td>
                <td><input type="number" id="idEngenheiro" name="idEngenheiro" min="1" value="<?php echo htmlspecialchars($linha['idEngenheiro']); ?>" readonly></td>
            </tr>
                <tr><td></td><td><input type="submit" value="Alterar"><input type="reset" value="Limpar"></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
