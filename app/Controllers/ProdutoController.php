<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Validations\ProdutoValidation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProdutoController
{
    private $model;
    private $validation;
    private $twig;

    public function __construct()
    {
        $this->model = new ProdutoModel();
        $this->validation = new ProdutoValidation();

        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);

        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listar()
    {
        $produtos = $this->model->listarTodos();
        echo $this->twig->render('produtos/listar.html.twig', ['produtos' => $produtos, 'session' => $_SESSION]);
        unset($_SESSION['mensagem']); // Limpa a mensagem da sessão após exibir
    }

    public function criarForm()
    {
        echo $this->twig->render('produtos/criar.html.twig', ['session' => $_SESSION, 'erros' => []]);
        unset($_SESSION['erros']);
    }

    public function criar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = $_POST;
            $erros = $this->validation->validar($dados);

            if (empty($erros)) {
                if ($this->model->criar($dados)) {
                    $_SESSION['mensagem'] = 'Produto criado com sucesso!';
                    header('Location: /produtos');
                    exit();
                } else {
                    echo "Erro ao criar o produto.";
                }
            } else {
                $_SESSION['erros'] = $erros;
                echo $this->twig->render('produtos/criar.html.twig', ['session' => $_SESSION, 'erros' => $erros]);
            }
        }
    }

    public function editarForm($id)
    {
        $produto = $this->model->buscarPorId($id);
        if ($produto) {
            echo $this->twig->render('produtos/editar.html.twig', ['produto' => $produto, 'session' => $_SESSION, 'erros' => []]);
            unset($_SESSION['erros']);
        } else {
            echo "Produto não encontrado.";
        }
    }

    public function editar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = $_POST;
            $erros = $this->validation->validar($dados);

            if (empty($erros)) {
                if ($this->model->atualizar($id, $dados)) {
                    $_SESSION['mensagem'] = 'Produto atualizado com sucesso!';
                    header('Location: /produtos');
                    exit();
                } else {
                    echo "Erro ao atualizar o produto.";
                }
            } else {
                $produto = $this->model->buscarPorId($id);
                $_SESSION['erros'] = $erros;
                echo $this->twig->render('produtos/editar.html.twig', ['produto' => $produto, 'session' => $_SESSION, 'erros' => $erros]);
            }
        }
    }

    public function deletar($id)
    {
        if ($this->model->deletar($id)) {
            $_SESSION['mensagem'] = 'Produto deletado com sucesso!';
            header('Location: /produtos');
            exit();
        } else {
            echo "Erro ao deletar o produto.";
        }
    }

    public function visualizar($id)
    {
        $produto = $this->model->buscarPorId($id);
        if ($produto) {
            echo $this->twig->render('produtos/visualizar.html.twig', ['produto' => $produto]);
        } else {
            echo "Produto não encontrado.";
        }
    }
}