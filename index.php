<?php

    
    // 👀 <- I see you...
    session_start();

    $host = 'controle-financeiro-mysql';
    $db = 'controle-financeiro-database';
    $user = 'root';
    $pass = 'root';

    $dsn = "mysql:host=$host;dbname=$db";
    $conexao = new PDO($dsn, $user,$pass);

    $emEdicao = false;

    if (!empty($_POST)) 
    {

        switch ($_POST['action']) 
        {
            case 'inserir' : 
                if(!validarLancamento())
                {
                    setMessage('error', 'Dados inseridos inválidos.');
                    redictTo('index.php');
                }

                $lancamento = [
                    'titulo'            => $_POST['titulo'],
                    'descricao'         => $_POST['descricao'],
                    'valor'             => $_POST['valor'],
                    'data_lancamento'   => $_POST['data_lancamento'],
                    'operacao'          => $_POST['operacao']
                ];

                if(insertLancamento($lancamento))
                {
                    setMessage('success', 'Inserido com sucesso.');
                } 
                else 
                {
                    setMessage('error', 'Erro na inserção.');
                }
                
                redictTo('index.php');
                break;
            
            case 'excluir' :
                if(!empty($_POST['id'])) 
                {
                    $id = $_POST['id'];

                    if (deletarLancamento($id))
                    {
                        setMessage('success', 'Excluido com sucesso.');
                    }
                    else
                    {
                        setMessage('error', 'Erro na exclusão.');
                    }
                }
                redictTo('index.php');
                break;
                
            case 'editar' :
                if(!empty($_POST['id'])) 
                {

                    $emEdicao = true;
                    $id = $_POST['id'];

                    $lancamento = getLancamento($id);
                }
                break;
                
            case 'atualizar' :
                if (!empty($_POST['id']) && validarLancamento())
                {
                    $id = $_POST['id'];

                    $lancamento = [
                        'titulo'            => $_POST['titulo'],
                        'descricao'         => $_POST['descricao'],
                        'valor'             => $_POST['valor'],
                        'data_lancamento'   => $_POST['data_lancamento'],
                        'operacao'          => $_POST['operacao']
                    ];

                    if (updateLancamento($id, $lancamento))
                    {
                        setMessage('success', 'Atualizado com sucesso.');
                    } else
                    {
                        setMessage('error', 'Erro ao atualizar.');
                    }
                }
                redictTo('index.php');
                break;
        }
    }

    function validarLancamento() 
    {
        if (!(isset($_POST['titulo']) && strlen($_POST['titulo']) > 0 && strlen($_POST['titulo']) <= 250))
            return false;
        if (!(isset($_POST['descricao']) && strlen($_POST['descricao']) > 0 && strlen($_POST['descricao']) <= 250))
            return false;
        if (!(isset($_POST['data_lancamento'])))
            return false;
        if (!(isset($_POST['valor'])) && is_numeric($_POST['valor']) && $_POST['valor'] !== 0)
            return false;
        if (!(isset($_POST['operacao']) && strlen($_POST['operacao']) == 1 ))
            return false;

        return true;
    }

    function setMessage ($type, $text) 
    {
        $message = [
            'type' => $type,
            'text' => $text,
        ];

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

    function insertLancamento($lancamento)
    {
        global $conexao;
        
        $sql = 'INSERT INTO lancamentos (titulo, descricao, valor, data_lancamento, operacao) VALUES (?, ?, ?, ?, ?)';
        
        $statement = $conexao->prepare($sql);

        if(!$statement)
            return false;

        $statement->bindValue(1, $lancamento['titulo']);
        $statement->bindValue(2, $lancamento['descricao']);
        $statement->bindValue(3, $lancamento['valor']);
        $statement->bindValue(4, $lancamento['data_lancamento']);
        $statement->bindValue(5, $lancamento['operacao']);

         return $statement->execute();
    }

    function deletarLancamento($id)
    {
        global $conexao;
        
        $sql = "DELETE FROM lancamentos WHERE id = ?";

        $statement = $conexao->prepare($sql);

        $statement->bindValue(1, $id);

        return $statement->execute();
    }

    function getLancamento($id)
    {
        global $conexao;

        $sql = "SELECT * FROM lancamentos WHERE id = ?";

        $statement = $conexao->prepare($sql);

        $statement->bindValue(1, $id);

        if ($statement->execute()) 
        {
            $lancamento = $statement->fetch(PDO::FETCH_ASSOC);
            return $lancamento;
        }

        return false;
    }

    function updateLancamento($id, $lancamento)
    {
        global $conexao;

        $sql = "UPDATE lancamentos SET titulo = ?, descricao = ?, data_lancamento = ?, valor = ?, operacao = ? WHERE id = ?";
        
        $statement = $conexao->prepare($sql);

        $statement->bindValue(1, $lancamento['titulo']);
        $statement->bindValue(2, $lancamento['descricao']);
        $statement->bindValue(3, $lancamento['data_lancamento']);
        $statement->bindValue(4, $lancamento['valor']);
        $statement->bindValue(5, $lancamento['operacao']);
        $statement->bindValue(6, $id);

        return $statement->execute();
    }

    function redictTo($route)
    {
        header('Location: ' . $route);
        die();
    }

    function dd($var) 
    {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
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

        tr, td, th {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }
    </style>
</head>
<body class="bg-indigo-100 pb-8">
    <div>
        <form class=" max-w-min mx-auto bg-fuchsia-200 p-8 rounded-lg mt-8" action="index.php" method="POST">
            <h2 class="min-w-min text-center text-xl">Novo Lançamento</h2>

            <?php
        $message = getMessage();
        if ($message) {
            
            if($message['type'] == 'success')
            {
                echo '<div class="mt-4 mx-auto py-2 rounded bg-green-300 text-center">' . $message["text"] . '</div>';
            }

            if($message['type'] == 'error')
            {
                echo '<div class="mt-4 w-60 mx-auto px-4 rounded bg-red-300 text-center"> ' . $message["text"] . '</div>';
            }

            unsetMessage();
        }?>

            <div class="mt-8">
                <div>
                    <label for="titulo">Titulo: </label>
                    <input class="h-8 pl-2 outline-none border-b-2 border-transparent focus:border-black" type="text" id="titulo" name="titulo" maxlength="250" required value="<?= isset($lancamento['titulo'])?$lancamento['titulo']:'' ?>">
                </div>
                <div class="mt-2">
                    <label  for="descricao">Tipo: </label>
                    <input class="h-8 pl-2 outline-none border-b-2 border-transparent focus:border-black" type="text" id="descricao" name="descricao" maxlength="250" required value="<?= isset($lancamento['descricao'])?$lancamento['descricao']:'' ?>">            
                </div>
                <div class="mt-2">
                    <label for="valor">Valor: </label>
                    <input class="h-8 pl-2 outline-none border-b-2 border-transparent focus:border-black" type="number" id="valor" step="any" name="valor" minlength="0.1" maxlength="250" required value="<?= isset($lancamento['valor'])?$lancamento['valor']:'' ?>">
                </div>
                <div class="mt-2">
                    <label for="data_lancamento">Data Lançamento: </label>
                    <input class="h-8 pl-2 outline-none border-b-2 border-transparent focus:border-black" type="date" id="data_lancamento" name="data_lancamento" value="<?= isset($lancamento['data_lancamento'])? $lancamento['data_lancamento'] : date('Y-m-d') ?>" required>
                </div>
                <div class="mt-2">
                    <label for="operacao">Operação: </label>
                    <br>
                    <select class="h-8 pl-2" name="operacao" id="operacao">
                        <option value="R" <?= !empty($lancamento['operacao']) && $lancamento['operacao'] == 'R'? 'selected': '' ?> >Receita</option>
                        <option value="D" <?= !empty($lancamento['operacao']) && $lancamento['operacao'] == 'D'? 'selected': '' ?> >Despesa</option>
                    </select>
                </div>
                <?php if($emEdicao) : ?>
                <div class="flex space-x mt-2">
                    <input class="duration-300 cursor-pointer rounded text-white bg-black border-b-2 border-black px-4 py-2 mt-4 mr-auto hover:px-6" type="submit" value="Atualizar">
                    <input type="hidden" name="action" value="atualizar">
                    <input type="hidden" name="id" value="<?= $lancamento['id'] ?> ">
                    <a class="mt-auto py-2 hover:underline" href="index.php">Cancelar</a>
                </div>
                <?php else : ?>
                <div class="text-center mt-2">
                    <input class="duration-300 cursor-pointer rounded text-white bg-black border-b-2 border-black px-4 py-2 mt-4 hover:px-6" type="submit" value="Inserir">
                    <input type="hidden" name="action" value="inserir">
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php  
            $lancamentos = getLancamentos();
            if(count($lancamentos) > 0): ?>
            <div>
                <table class="mx-auto mt-8">
                    <tr>
                        <th># ID</th>
                        <th>Título</th>
                        <th>Valor</th>
                        <th>Data Lanc.</th>
                        <th>Operacao</th>
                    </tr>
                    <?php foreach ($lancamentos as $lancamento): ?>
                        <tr>
                            <td><?= $lancamento['id'] ?></td>
                            <td><?= $lancamento['titulo']?></td>
                            <td><?= $lancamento['valor']?></td>
                            <td><?= DateTime::createFromFormat('Y-m-d', $lancamento['data_lancamento'])->format('d/m/Y') ?></td>
                            <td><?= $lancamento['operacao']?></td>
                            <td>
                                <form action="index.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $lancamento['id'] ?>">
                                    <input class="duration-300 cursor-pointer bg-red-400 rounded text-white p-2 hover:bg-red-500 capitalize" type="submit" value="excluir" name="action">
                                </form>
                            </td>
                            <td>
                                <form action="index.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $lancamento['id'] ?>">
                                    <input class="duration-300 cursor-pointer bg-yellow-400 rounded text-white p-2 hover:bg-yellow-500 capitalize" type="submit" value="editar" name="action">
                                </form>
                            </td>
                            
                        </tr>      
                    <?php endforeach; ?>
                <table>
            </div>
            <?php endif; ?>
</body>
</html>