<?php
require_once __DIR__ . '/../fases/sessao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema XYZ</title>
    <link rel="stylesheet" href="../estilos/menu.css">
    <link rel="stylesheet" href="../estilos/ajustes.css">
</head>
<body>
    <div class="caixa">
        <h2>Seja bem-vindo(a), <?php echo htmlspecialchars($usuario); ?></h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <ul>
            <li><a href="manutencao.php?cadastro=fases">Manutenção de Fases</a></li>
            <li><a href="manutencao.php?cadastro=engenheiros">Manutenção de Engenheiros</a></li>
            <li><a href="manutencao.php?cadastro=funcoes">Manutenção de Funções</a></li>
            <li><a href="manutencao.php?cadastro=usuarios">Manutenção de Usuários</a></li>
            <li><a href="../login/sair.php" class="sair">Sair do Sistema</a></li>
        </ul> 
    </div>
</body>
</html>
