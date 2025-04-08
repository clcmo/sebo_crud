<?php

namespace App\Validations;

class ProdutoValidation
{
    public function validar(array $data): array
    {
        $erros = [];

        if (empty($data['nome'])) {
            $erros['nome'] = 'O nome é obrigatório.';
        }

        if (empty($data['descricao'])) {
            $erros['descricao'] = 'A descrição é obrigatória.';
        }

        if (!isset($data['preco']) || !is_numeric($data['preco']) || $data['preco'] <= 0) {
            $erros['preco'] = 'O preço deve ser um número maior que zero.';
        }

        return $erros;
    }
}