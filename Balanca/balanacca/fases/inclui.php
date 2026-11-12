<?php
require_once __DIR__ . '/sessao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema XYZ</title>
    <link rel="stylesheet" href="../estilos/incluir.css">
    <link rel="stylesheet" href="../estilos/ajustes.css">
</head>
<body>
    <div class="caixa">
        <h2>INCLUSÃO DE FASES</h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <a href="../menu/mostra.php" class="voltar">Voltar ao menu</a>
        <form method="post" action="incluir.php">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <table class="formulario">
            <tr>
                <td><label for="dsFase">Descrição da fase</label></td>
                <td><input type="text" id="dsFase" name="dsFase" maxlength="45" required autofocus></td>
            </tr>
                <tr><td></td><td><input type="submit" value="Incluir"><input type="reset" value="Limpar"></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
