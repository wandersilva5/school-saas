<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto #<?= $payment['boleto_code'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 1cm;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .boleto-container {
            padding: 20px;
        }

        .bank-info {
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
        }

        .bank-logo {
            height: 40px;
            margin-bottom: 10px;
        }

        .field {
            margin-bottom: 10px;
        }

        .field-label {
            font-size: 10px;
            color: #666;
        }

        .field-value {
            font-size: 14px;
            font-weight: bold;
        }

        .barcode {
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            text-align: center;
        }

        .cut-line {
            border-bottom: 1px dashed #000;
            margin: 20px 0;
            position: relative;
        }

        .cut-line::after {
            content: "✂";
            position: absolute;
            right: -20px;
            top: -10px;
        }

        .payment-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="boleto-container">
            <!-- Botões de ação -->
            <div class="no-print" style="margin-bottom: 50px;">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Imprimir Boleto
                </button>
                <button onclick="window.close()" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Fechar
                </button>
            </div>

            <!-- Recibo do Sacado -->
            <div class="bank-info">
                <div style="float: left; font-size: 20px;">
                    <img src="/images/bank-logo.png" alt="Logo do Banco" class="bank-logo">
                </div>
                <div style="float: left; font-size: 20px;">
                    <strong><?= htmlspecialchars($payment['bank_assignor_name']) . ' | ' .  $payment['bank_code'] . ' | ' . $payment['boleto_code'] ?></strong>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <td colspan="5" style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Local de Pagamento</div>
                        <div>Pagável em qualquer banco até o vencimento</div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Data de Vencimento</div>
                        <div class="field-value"><?= date('d/m/Y', strtotime($payment['due_date'])) ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Beneficiário</div>
                        <div><?= htmlspecialchars($payment['institution_name']) ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Agência/Código Beneficiário</div>
                        <div class="field-value"><?= $payment['bank_agency'] . '/' . $payment['bank_account'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Data de Documento</div>
                        <div class="field-value"><?= date('d/m/Y') ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">No. Do documento</div>
                        <div class="field-value"><?= $payment['id'] ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Espécie doc.</div>
                        <div class="field-value">DM</div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Aceite</div>
                        <div class="field-value">N</div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Data Processamento</div>
                        <div class="field-value"><?= date('d/m/Y') ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Nosso Número</div>
                        <div class="field-value"><?= $payment['bank_agreement'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Uso do Banco</div>
                        <div class="field-value"></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Carteira</div>
                        <div class="field-value"><?= $payment['bank_wallet'] ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Espécie</div>
                        <div class="field-value">R$</div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Quantidade</div>
                        <div class="field-value"></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Valor</div>
                        <div class="field-value"></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">(=) Valor do Documento</div>
                        <div class="field-value">R$ <?= number_format($payment['amount'], 2, ',', '.') ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" rowspan="5" style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Instruções</div>
                        <div class="field-value">
                            1. Não receber após o vencimento<br>
                            2. Em caso de dúvidas, entre em contato com a instituição<br>
                            3. Pagamento referente a <?= htmlspecialchars($payment['description']) ?>
                            <br>
                            4. Não receber após o vencimento
                            5. Em caso de dúvidas, entre em contato com a instituição<br>
                        </div>
                    </td>
                    <td style="border: 1px solid #000; padding: 15px;">
                        <div class="field-label">(-) Descontos/Abatimento</div>
                        <div class="field-value"></div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 20px;">
                        <div class="field-label"> </div>
                        <div class="field-value"> </div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 15px;">
                        <div class="field-label">(+) Mora/Multa</div>
                        <div class="field-value"></div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 20px;">
                        <div class="field-label"> </div>
                        <div class="field-value"> </div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 15px;">
                        <div class="field-label">(=) Valor Cobrado</div>
                        <div class="field-value"></div>
                    </td>
                </tr>
                <tr style="border-left: 1px solid #000;border-right : 1px solid #000;border-top : 1px solid #000;">
                    <td colspan="3" style="padding: 15px;">
                        <div class="field-label">Pagador</div>
                        <div class="field-value"><?= htmlspecialchars($payment['student_name']) ?></div>
                    </td>
                    <td colspan="2" style="padding: 15px;">
                        <div class="field-label">CPF/CNPJ</div>
                        <div class="field-value"><?= htmlspecialchars($payment['student_name']) ?></div>
                    </td>
                </tr>
                <tr style="border-left: 1px solid #000;border-right : 1px solid #000;">
                    <td colspan="6"  style="padding: 15px;">
                        <div class="field-label">Endereço</div>
                        <div class="field-value"><?= htmlspecialchars($payment['student_name']) ?></div>
                    </td>
                </tr>
                <tr style="border-left: 1px solid #000;border-right : 1px solid #000;border-bottom : 1px solid #000;">
                    <td colspan="5"  style="padding: 15px;">
                        <div class="field-label">Sacador/Avalista:</div>
                        <div class="field-value"></div>
                    </td>
                </tr>
            </table>


            

            <div class="barcode">
                <div style="font-family: monospace; font-size: 16px; margin-bottom: 10px;">
                    <?= chunk_split($payment['boleto_code'], 5, ' ') ?>
                </div>
                <!-- Aqui vai o código de barras -->
                <img src="data:image/png;base64,<?= $barcode_image ?>" alt="Código de Barras">
            </div>

            <div class="cut-line"></div>

            <!-- Ficha de Compensação -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Local de Pagamento</div>
                        <div>Pagável em qualquer banco até o vencimento</div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px; width: 170px;">
                        <div class="field-label">Data de Vencimento</div>
                        <div class="field-value"><?= date('d/m/Y', strtotime($payment['due_date'])) ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Beneficiário</div>
                        <div><?= htmlspecialchars($payment['institution_name']) ?></div>
                    </td>
                    <td style="border: 1px solid #000; padding: 5px;">
                        <div class="field-label">Nosso Número</div>
                        <div class="field-value"><?= $payment['boleto_code'] ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <script>
            // Prevenir redirecionamentos indesejados
            window.onload = function() {
                document.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                    });
                });
            };
        </script>
    </div>
</body>

</html>