<?php

    session_start();

    $host = 'controle-financeiro-mysql';
    $db = 'controle-financeiro-database';
    $user = 'root';
    $pass = 'root';

    $dsn = "mysql:host=$host;dbname=$db";
    // $conexao = new PDO($dsn, $user,$pass);

    $emEdicao = false;

    if (!empty($_POST)) 
    {

        switch ($_POST['action']) 
        {
            case 'inserir' : 
                if(validarLancamento()) 
                {
                    $lancamento = [];
                    $lancamento['nome'] = $_POST['nome'];
                    $lancamento['tipo'] = $_POST['tipo'];
                    $lancamento['valor'] = $_POST['valor'];
                    $lancamento['operacao'] = $_POST['operacao'];

                    $sql = 'INSERT INTO lancamentos (nome, tipo, valor, operacao) VALUES (?, ?, ?, ?)';

                    $statement = $conexao->prepare($sql);
                    
                    $statement->bindValue(1, $lancamento['nome']);
                    $statement->bindValue(2, $lancamento['tipo']);
                    $statement->bindValue(3, $lancamento['valor']);
                    $statement->bindValue(4, $lancamento['operacao']);

                    if ($statement->execute()) 
                    {
                        setMessage('success', 'Inserido com sucesso.');
                        header('Location: index.php');
                    }
                    else 
                    {
                        setMessage('error', 'Erro na inserção.');
                    }

                } else
                {
                    setMessage('error', 'Dados inseridos inválidos.');
                }
                header('Location: index.php');
                break;
            
            case 'excluir' :
                if(!empty($_POST['id'])) 
                {
                    $id = $_POST['id'];

                    $sql = "DELETE FROM lancamentos WHERE id = ?";
                    
                    $statement = $conexao->prepare($sql);

                    $statement->bindValue(1, $id);

                    if ($statement->execute())
                    {
                        setMessage('success', 'Excluido com sucesso.');
                        header('Location: index.php');
                    }
                    else
                    {
                        setMessage('error', 'Erro na exclusão.');
                        header('Location: index.php');
                    }
                }
                break;

            case 'editar' :
                if(!empty($_POST['id'])) 
                {

                    $emEdicao = true;
                    $id = $_POST['id'];

                    $sql = "SELECT * FROM lancamentos WHERE id = ?";

                    $statement = $conexao->prepare($sql);

                    $statement->bindValue(1, $id);

                    if ($statement->execute())
                    {
                        $lancamento = $statement->fetch(PDO::FETCH_ASSOC);
                    } 
                }

                break;
                
            case 'atualizar' :
                if (!empty($_POST['id']))
                {
                    $id = $_POST['id'];
                    $lancamento = [];
                    $lancamento['nome'] = $_POST['nome'];
                    $lancamento['tipo'] = $_POST['tipo'];
                    $lancamento['valor'] = $_POST['valor'];
                    $lancamento['operacao'] = $_POST['operacao'];

                    $sql = "UPDATE lancamentos SET nome = ?, tipo = ?, valor = ?, operacao = ? WHERE id = ?";

                    $statement = $conexao->prepare($sql);

                    $statement->bindValue(1, $lancamento['nome']);
                    $statement->bindValue(2, $lancamento['tipo']);
                    $statement->bindValue(3, $lancamento['valor']);
                    $statement->bindValue(4, $lancamento['operacao']);
                    $statement->bindValue(5, $id);

                    if ($statement->execute())
                    {
                        setMessage('success', 'Atualizado com sucesso.');
                        header('Location: index.php');
                    } else
                    {
                        setMessage('error', 'Erro ao atualizar.');
                        header('Location: index.php');
                    }
                }
                break;
        }

    }

    function validarLancamento() 
    {
        if (!(isset($_POST['nome']) && strlen($_POST['nome']) > 0 && strlen($_POST['nome']) <= 250))
            return false;
        if (!(isset($_POST['tipo']) && strlen($_POST['tipo']) > 0 && strlen($_POST['tipo']) <= 250))
            return false;
        if (!(isset($_POST['valor'])) && is_numeric($_POST['valor']) && $_POST['valor'] !== 0)
            return false;
        if (!(isset($_POST['operacao']) && strlen($_POST['operacao']) == 1 ))
            return false;

        return true;
    }

    function setMessage ($type, $text) 
    {
        $message = [];
        $message['type'] = $type;
        $message['text'] = $text;

        $_SESSION['message'] = $message;
    }

    function unsetMessage()
    {
        unset($_SESSION['message']);
    }

    function getMessage () 
    {
        return isset($_SESSION['message'])? $_SESSION['message'] : false;
    }

    function getLancamentos ()
    {
        global $conexao;

        $sql = "SELECT * FROM lancamentos";
        
        $statement = $conexao->prepare($sql);

        $statement->execute();

        if($statement->rowCount() > 0) {
            $lancamentos = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $lancamentos;
        }

        return [];
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        table {
            border: 1px solid black;
            border-collapse: collapse;
        }

        tr td {
            border: 1px solid black;
        }
    </style>
</head>
<body class="bg-indigo-300 p-4">
    <?php
        $message = getMessage();
        if($message) {
            echo "<pre>";
            var_dump($message);
            echo "</pre>";
            unsetMessage();
        }
    ?>
    <div>
        <form action="index.php" method="POST">
            <h2 class="min-w-min text-center text-xl">Novo ançamento</h2>

            <div class="max-w-min mx-auto mt-8">
                <div>
                    <label for="nome">Titulo: </label>
                    <input type="text" name="titulo" maxlength="250" required value="<?= isset($lancamento['nome'])?$lancamento['nome']:'' ?>">
                </div>
                <div>
                    <label for="nome">Tipo: </label>
                    <input type="text" name="tipo" maxlength="250" required value="<?= isset($lancamento['tipo'])?$lancamento['tipo']:'' ?>">            
                </div>
                <div>
                    <label for="valor">Valor: </label>
                    <input type="number" step="any" name="valor" minlength="0.1" maxlength="250" required value="<?= isset($lancamento['valor'])?$lancamento['valor']:'' ?>">
                </div>
                <label for="operacao">Valor: </label>
                <select name="operacao" id="">
                    <option value="R" <?= !empty($lancamento['operacao']) && $lancamento['operacao'] == 'R'? 'selected': '' ?> >Receita</option>
                    <option value="D" <?= !empty($lancamento['operacao']) && $lancamento['operacao'] == 'D'? 'selected': '' ?> >Despesa</option>
                </select>
                <?php if($emEdicao) : ?>
                <div>
                    <input type="submit" value="Atualizar">
                    <input type="hidden" name="action" value="atualizar">
                    <input type="hidden" name="id" value="<?= $lancamento['id'] ?> ">
                    <a href="index.php">Cancelar</a>
                </div>
                <?php else : ?>
                <div class="text-center">
                    <input class="duration-300 cursor-pointer rounded text-white bg-black border-2 border-black px-4 py-2 mt-4 hover:bg-white hover:text-black" type="submit" value="Inserir">
                    <input type="hidden" name="action" value="inserir">
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php  
            $lancamentos = getLancamentos();
            if(count($lancamentos) > 0) {

                echo "<div>";
                echo "<table>";
                foreach ($lancamentos as $lancamento): ?>
                    <tr>
                        <td><?= $lancamento['id'] ?></td>
                        <td><?= $lancamento['nome']?></td>
                        <td><?= $lancamento['tipo']?></td>
                        <td><?= $lancamento['valor']?></td>
                        <td><?= $lancamento['operacao']?></td>
                        <td>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="id" value="<?= $lancamento['id'] ?>">
                                <input type="submit" value="excluir" name="action">
                            </form>
                        </td>
                        <td>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="id" value="<?= $lancamento['id'] ?>">
                                <input type="submit" value="editar" name="action">
                            </form>
                        </td>
                        
                    </tr>      
                <?php endforeach;
                echo "<table>";
                echo "</div>";
            }
        ?>
    </div>
</body>
</html>