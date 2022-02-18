<?php

if (isset($error) && $error) {
    echo $message;
}
if (isset($success) && $success) {
    echo $message;
}

$error   = false;
$success = false;
$message = '';

if (!empty($_POST)) {
    $titulo    = $_POST['titulo'];;
    $descricao = $_POST['descricao'];
    $concluida = $_POST['concluida'];

    if (empty($titulo)) {
        $error   = true;
        $message = "Dados inseridos inválidos.";
    }

    if (empty($descricao)) {
        $error   = true;
        $message = "Dados inseridos inválidos.";
    }

    if (empty($concluida)) {
        $error   = true;
        $message = "Dados inseridos inválidos.";
    }

    if (!$error) {
        $link = mysqli_connect("database", "root", "root", "php_tarefas");

        mysqli_query($link, "INSERT INTO tarefas (titulo, descricao, concluida) 
                             VALUES ('" . $titulo . "', '" . $descricao . "', '" . $concluida . "')");

        if (mysqli_error($link)) {
            $error = true;
            $msg = "Houve um erro ao inserir a tarefa.";
        } else {
            $message = "Tarefa inserida com sucesso.";
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
</head>
<body>
    <div>
        <h2>Cadastro de tarefas</h2>
        <form action="" method="POST">
            <div>
                <label for="titulo">Titulo:</label><br>
                <input type="text" name="titulo" required minlength="2" maxlength="255">
            </div>
            <div>
                <label for="titulo">Descricao:</label><br>
                <input type="text" name="descricao" required minlength="2" maxlength="500">
            </div>
            <div>
                <label for="">Concluída?</label><br>
                <label for="true">Sim</label>
                <input type="radio" name="concluida" value="true"><br>
                <label for="false">Não</label>
                <input type="radio" name="concluida" value="false" checked><br>

            </div>
            <div>
                <input type="submit" value="Inserir">
            </div>
        </form>
    </div>
    <br>
</body>
</html>