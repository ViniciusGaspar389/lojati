<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "config.php";

header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER["REQUEST_METHOD"];

$rota = $_GET["rota"] ?? "";


// GET - Listar clientes
if ($method === "GET" && $rota === "clientes") {

    try {

        $sql = "SELECT * FROM Clientes";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "sucesso" => true,
            "clientes" => $clientes
        ]);

    } catch (PDOException $e) {

        echo json_encode([
            "sucesso" => false,
            "erro" => $e->getMessage()
        ]);
    }

    exit;
}


// POST - Criar cliente
if ($method === "POST" && $rota === "clientes/create") {

    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = $dados["Nome"] ?? "";
    $email = $dados["Email"] ?? "";
    $telefone = $dados["Telefone"] ?? "";
    $endereco = $dados["Endereco"] ?? "";


    if ($nome === "" || $email === "") {

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Nome e email são obrigatórios."
        ]);

        exit;
    }


    try {

        $sql = "INSERT INTO Clientes 
                (Nome, Email, Telefone, Endereco)
                VALUES
                (:nome, :email, :telefone, :endereco)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":nome", $nome);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":telefone", $telefone);
        $stmt->bindValue(":endereco", $endereco);

        $stmt->execute();


        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Cliente cadastrado com sucesso!",
            "ClienteID" => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {

        echo json_encode([
            "sucesso" => false,
            "erro" => $e->getMessage()
        ]);
    }

    exit;
}


echo json_encode([
    "sucesso" => false,
    "mensagem" => "Rota não encontrada."
]);