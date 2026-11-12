<?php
require_once __DIR__ . '/../fases/sessao.php';
$cadastros = [
    'fases' => ['Fases', 'inclui.php', 'consulta.php', 'altera.php', 'exclui.php'],
    'engenheiros' => ['Engenheiros', 'inclui_engenheiros.php', 'consulta_engenheiros.php', 'altera_engenheiros.php', 'exclui_engenheiros.php'],
    'funcoes' => ['Funções', 'inclui_funcoes.php', 'consulta_funcoes.php', 'altera_funcoes.php', 'exclui_funcoes.php'],
    'usuarios' => ['Usuários', 'inclui_usuarios.php', 'consulta_usuarios.php', 'altera_usuarios.php', 'exclui_usuarios.php']
];
$cadastro = $cadastros[$_GET['cadastro'] ?? 'fases'] ?? null;
if (!$cadastro) {
    header('Location: mostra.php', true, 303);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema XYZ</title>
    <link rel="stylesheet" href="../estilos/manutencao.css">
    <link rel="stylesheet" href="../estilos/ajustes.css">
</head>
<body>
    <div class="caixa">
        <h2>Aqui é a Manutenção de <?php echo htmlspecialchars($cadastro[0]); ?>, <?php echo htmlspecialchars($usuario); ?></h2>
        <?php if (isset($_SESSION['erro'])) { ?>
            <p style="color:#b91c1c"><?php echo htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); } ?>
        <ul>
            <li><a href="../fases/<?php echo $cadastro[1]; ?>">Incluir</a></li>
            <li><a href="../fases/<?php echo $cadastro[2]; ?>">Consultar</a></li>
            <li><a href="../fases/<?php echo $cadastro[3]; ?>">Alterar</a></li>
            <li><a href="../fases/<?php echo $cadastro[4]; ?>">Excluir</a></li>
            <li><a href="mostra.php">Voltar ao menu</a></li>
            <li><a href="../login/sair.php" class="sair">Sair do Sistema</a></li>
        </ul>
    </div>
</body>
</html>
