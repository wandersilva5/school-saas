<?php

namespace App\Services;

use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Agente;

class BoletoService {
    private $bank;
    private $config;

    public function __construct($bankConfig) {
        $this->config = $bankConfig;
    }

    public function generateBoleto($payment) {
        $sacado = new Agente($payment['student_name'], $payment['student_document']);
        $cedente = new Agente(
            $this->config['cedente_nome'],
            $this->config['cedente_cpf'],
            $this->config['cedente_endereco'],
            $this->config['cedente_cidade'],
            $this->config['cedente_uf'],
            $this->config['cedente_cep']
        );

        $boleto = new BancoDoBrasil([
            'dataVencimento' => new \DateTime($payment['due_date']),
            'valor' => $payment['amount'],
            'sequencial' => $payment['id'],
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $this->config['agencia'],
            'carteira' => $this->config['carteira'],
            'conta' => $this->config['conta'],
            'convenio' => $this->config['convenio'],
            'descricao' => $payment['description']
        ]);

        // Gera o PDF do boleto
        $pdfContent = $boleto->getOutput();
        
        // Salva o PDF em uma pasta pública
        $filename = 'boletos/boleto_' . $payment['id'] . '.pdf';
        $filepath = __DIR__ . '/../../public/' . $filename;
        
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        file_put_contents($filepath, $pdfContent);

        return [
            'linha_digitavel' => $boleto->getLinhaDigitavel(),
            'nosso_numero' => $boleto->getNossoNumero(),
            'pdf_url' => '/' . $filename
        ];
    }
}
