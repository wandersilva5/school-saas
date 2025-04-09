<?php

namespace App\Controllers;

use App\Models\Institution;

class BankConfigController extends BaseController
{
    private $institutionModel;

    public function __construct()
    {
        $this->institutionModel = new Institution();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $bankConfig = $this->institutionModel->getBankConfig($institutionId);

            return $this->render('bank-config/index', [
                'pageTitle' => 'Configurações Bancárias',
                'bankConfig' => $bankConfig,
                'currentSection' => 'settings'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/dashboard');
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];
                
                $bankConfig = [
                    'bank_code' => $_POST['bank_code'],
                    'bank_agency' => $_POST['bank_agency'],
                    'bank_account' => $_POST['bank_account'],
                    'bank_wallet' => $_POST['bank_wallet'],
                    'bank_agreement' => $_POST['bank_agreement'],
                    'bank_assignor_name' => $_POST['bank_assignor_name'],
                    'bank_assignor_document' => $_POST['bank_assignor_document'],
                    'bank_assignor_address' => $_POST['bank_assignor_address']
                ];

                $this->institutionModel->updateBankConfig($institutionId, $bankConfig);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Configurações bancárias atualizadas com sucesso!'
                ];

                $this->redirect('/bank-config');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
                ];
                $this->redirect('/bank-config');
            }
        }
    }
}
