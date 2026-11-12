<?php
require_once __DIR__ . '/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['token'], (string)($_POST['token'] ?? ''))) {
    header('Location: altera_engenheiros.php', true, 303);
    exit;
}
$id = filter_input(INPUT_POST, 'idEngenheiro', FILTER_VALIDATE_INT);
$dsNome = trim((string)($_POST['dsNome'] ?? ''));
$nrCREA = trim((string)($_POST['nrCREA'] ?? ''));
$dtAdmissao = trim((string)($_POST['dtAdmissao'] ?? ''));
if (!$id || $id < 1 || $dsNome === '' || preg_match_all('/./us', $dsNome) > 60 || $nrCREA === '' || preg_match_all('/./us', $nrCREA) > 20 || $dtAdmissao === '') {
    $_SESSION['erro'] = 'Preencha os campos corretamente.';
    header('Location: altera_engenheiros.php', true, 303);
    exit;
}
$data = DateTime::createFromFormat('!Y-m-d', $dtAdmissao);
if (!$data || $data->format('Y-m-d') !== $dtAdmissao) {
    $_SESSION['erro'] = 'Informe uma data de admissão válida.';
    header('Location: altera_engenheiros.php', true, 303);
    exit;
}
try {

    $sql = "UPDATE engenheiros SET dsNome = ?, nrCREA = ?, dtAdmissao = ? WHERE idEngenheiro = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'sssi', $dsNome, $nrCREA, $dtAdmissao, $id);
    mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
} catch (mysqli_sql_exception $erro) {
    $_SESSION['erro'] = (int)$erro->getCode() === 1062 ? 'Já existe um registro com esses dados.' : 'Não foi possível salvar os dados.';
}
header('Location: altera_engenheiros.php', true, 303);
exit;
