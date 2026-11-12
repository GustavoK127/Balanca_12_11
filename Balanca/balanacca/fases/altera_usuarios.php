<?php
require_once __DIR__ . '/sessao.php';
$resultado = mysqli_query($conexao, 'SELECT Nome, Email FROM usuarios ORDER BY Nome, Email');
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
<body class="lista">
    <div class="caixa">
        <h2>ALTERAR USUÁRIOS</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a href="../menu/mostra.php" class="voltar">Voltar ao menu</a>
        <div class="tabela">
            <table>
                <thead><tr><th scope="col">Nome</th><th scope="col">E-mail</th><th scope="col">Ação</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($resultado) === 0) { ?>
                    <tr><td colspan="3">Nenhum usuário cadastrado.</td></tr>
                <?php } ?>
                <?php while ($linha = mysqli_fetch_assoc($resultado)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($linha['Nome']); ?></td>
                    <td><?php echo htmlspecialchars($linha['Email']); ?></td>
                    <td><form method="post" action="muda_usuarios.php">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                        <input type="hidden" name="emailOriginal" value="<?php echo htmlspecialchars($linha['Email']); ?>">
                        <input type="submit" value="Alterar">
                    </form></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
