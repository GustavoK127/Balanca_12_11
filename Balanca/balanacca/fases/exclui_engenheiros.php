<?php
require_once __DIR__ . '/sessao.php';
$sql = 'SELECT idEngenheiro, dsNome, nrCREA, dtAdmissao FROM engenheiros' . ' ORDER BY idEngenheiro';
$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema XYZ</title>
    <link rel="stylesheet" href="../estilos/excluir.css">
    <link rel="stylesheet" href="../estilos/ajustes.css">
</head>
<body>
    <div class="caixa">
        <h2>EXCLUIR ENGENHEIROS</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a href="../menu/mostra.php" class="voltar">Voltar ao menu</a>
        <div class="tabela">
            <table>
                <thead><tr><th scope="col">Código</th><th scope="col">Nome</th><th scope="col">CREA</th><th scope="col">Data de Admissão</th><th scope="col">Ação</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($resultado) === 0) { ?>
                    <tr><td colspan="5">Nenhum registro cadastrado.</td></tr>
                <?php } ?>
                <?php while ($linha = mysqli_fetch_assoc($resultado)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($linha['idEngenheiro']); ?></td>
                    <td><?php echo htmlspecialchars($linha['dsNome']); ?></td>
                    <td><?php echo htmlspecialchars($linha['nrCREA']); ?></td>
                    <td><?php echo htmlspecialchars($linha['dtAdmissao'] ? date('d/m/Y', strtotime($linha['dtAdmissao'])) : ''); ?></td>
                    <td><form method="post" action="excluir_engenheiros.php" onsubmit="return confirm('Deseja excluir este registro?');">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                        <input type="hidden" name="idEngenheiro" value="<?php echo (int)$linha['idEngenheiro']; ?>">
                        <input type="submit" value="Excluir">
                    </form></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
