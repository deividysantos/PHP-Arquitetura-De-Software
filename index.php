<?php

$error   = false;
$success = false;
$message = '';

if (!empty($_POST)) {

    if ($_POST['action'] == 'inserir') {
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
                                VALUES ('" . $titulo . "', '" . $descricao . "', " . $concluida . ")");

            if (mysqli_error($link)) {
                $error = true;
                $message = "Houve um erro ao inserir a tarefa.";
            } else {
                $message = "Tarefa inserida com sucesso.";
                $success = true;
            }
        }
    } elseif ($_POST['action'] == 'excluir') {
        $id = $_POST['id'];

        $link = mysqli_connect("database", "root", "root", "php_tarefas");
        mysqli_query ($link, "DELETE FROM tarefas WHERE id = {$id}");    
        
        if (mysqli_error($link)) {
            $error = true;
            $message = "Houve um erro ao excluir a tarefa.";
        } else {                
            $message = "Tarefa deletada com sucesso.";
            $success = true;
        }
    } elseif ($_POST['action'] == 'done' || $_POST['action'] == 'undone') {
        $id = $_POST['id'];
        $action = $_POST['action'];
        
        $link = mysqli_connect("database", "root", "root", "php_tarefas");

        if ($action == 'done') {
            mysqli_query($link, "UPDATE tarefas SET concluida = '1' WHERE id = {$id}");
        } elseif ($action == 'undone') {
            mysqli_query($link, "UPDATE tarefas SET concluida = '0' WHERE id = {$id}");
        }

        if (mysqli_error($link)) {
            $error = true;
            $message = "Houve um erro ao excluir a tarefa.";
        } else {                
            $message = "Tarefa deletada com sucesso.";
            $success = true;
        }
    }
}

function getTarefas() {
    $link = mysqli_connect("database", "root", "root", "php_tarefas");
    $result = mysqli_query($link, "SELECT * FROM tarefas");
    $tarefas = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $tarefas;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
    <style>
        table {
            border: 1px;
            border-style: solid;
            border-collapse: collapse;
            align-items: center;
        }

        table th, tr td{
            border: 1px;
            border-style: solid;
            text-align: center;
        }
    </style>
</head>
<body>

    <div>
        <div style="display: <?=$success ? 'block' : 'none'?>" >
            <div>
                <p><?=$message?></p>
            </div>
        </div>
        <div style="display: <?=$error ? 'block' : 'none'?>" >
            <div>
                <p><?=$message?></p>
            </div>
        </div>
    </div>

    <div>
        <h2>Cadastro de tarefas</h2>
        <form action="index.php" method="POST">
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
                <input type="submit" value="inserir" name="">
                <input type="hidden" name="action" value="inserir">
            </div>
        </form>
    </div>
    <br>

    <div>
        <?php
            $tarefas = getTarefas();
            if (count($tarefas) > 0) {
                echo "<h2>Listagem de Tarefas</h2>";
                echo "<table>";
                    echo "<tr>";
                        echo "<th>";
                            echo "#ID";
                        echo "</th>";
                        echo "<th>";
                            echo "Titulo";
                        echo "</th>";
                        echo "<th>";
                            echo "Descrição";
                        echo "</th>";
                        echo "<th>";
                            echo "Status";
                        echo "</th>";
                        echo "<th>";
                            echo "Excluir";
                        echo "</th>";
                    echo "</tr>";
                    foreach($tarefas as $tarefa) {
                        echo "<tr>";
                            echo "<td> {$tarefa['id']} </td>";
                            echo "<td> {$tarefa['titulo']} </td>";
                            echo "<td> {$tarefa['descricao']} </td>";
                            if ($tarefa['concluida'] == 1) {
                                echo '<td>
                                        <form action="index.php" method="POST">
                                            <input type="hidden" name="id" value="' . $tarefa['id'] . '">
                                            <input type="hidden" name="action" value="undone">
                                            <input type="submit" name="" value="🟢">
                                        </form>
                                    </td>';
                            } else {
                                echo '<td>
                                        <form action="index.php" method="POST">
                                            <input type="hidden" name="id" value="' . $tarefa['id'] . '">
                                            <input type="hidden" name="action" value="done">
                                            <input type="submit" name="" value="⭕">
                                        </form>
                                    </td>';
                            }
                            echo '<td>' .
                                    '<form action="index.php" method="POST">
                                        <input type="hidden" name="id" value="' . $tarefa['id'] . '">
                                        <input type="hidden" name="action" value="excluir">
                                        <input type="submit" name="" value="🗑️">
                                    </form>' .
                                '</td>';
                        echo "</tr>";
                    }
                echo "</table>";
            }
        ?>
    </div>
</body>
</html>