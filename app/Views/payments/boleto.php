<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boleto #<?= $payment['boleto_code'] ?></title>
    <style>
        @page { size: A4; margin: 0; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 1cm; }
        }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .boleto-container { padding: 20px; }
        .bank-info { border-bottom: 1px solid #000; margin-bottom: 20px; }
        .bank-logo { height: 40px; margin-bottom: 10px; }
        .field { margin-bottom: 10px; }
        .field-label { font-size: 10px; color: #666; }
        .field-value { font-size: 14px; font-weight: bold; }
        .barcode { margin: 20px 0; padding: 10px; background: #f8f9fa; text-align: center; }
        .cut-line { border-bottom: 1px dashed #000; margin: 20px 0; position: relative; }
        .cut-line::after { content: "✂"; position: absolute; right: -20px; top: -10px; }
        .payment-info { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #f8f9fa; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="bi bi-x"></i> Fechar
        </button>
    </div>

    <div class="boleto-container">
        <!-- Botões de ação -->
        <div class="no-print" style="margin-bottom: 20px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Imprimir Boleto
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Fechar
            </button>
        </div>

        <!-- Recibo do Sacado -->
        <div class="bank-info">
            <img src="/images/bank-logo.png" alt="Logo do Banco" class="bank-logo">
            <div style="float: right; font-size: 20px;">
                <strong><?= $payment['bank_code'] ?? '001' ?></strong>
            </div>
        </div>

        <div class="payment-info">
            <div class="field">
                <div class="field-label">Beneficiário</div>
                <div class="field-value"><?= htmlspecialchars($payment['institution_name']) ?></div>
            </div>
            <div class="field">
                <div class="field-label">Data de Vencimento</div>
                <div class="field-value"><?= date('d/m/Y', strtotime($payment['due_date'])) ?></div>
            </div>
            <div class="field">
                <div class="field-label">Valor</div>
                <div class="field-value">R$ <?= number_format($payment['amount'], 2, ',', '.') ?></div>
            </div>
        </div>

        <div class="field">
            <div class="field-label">Pagador</div>
            <div class="field-value"><?= htmlspecialchars($payment['student_name']) ?></div>
        </div>

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

        <div style="margin-top: 20px;">
            <div class="field-label">Instruções</div>
            <div>1. Não receber após o vencimento</div>
            <div>2. Em caso de dúvidas, entre em contato com a instituição</div>
            <div>3. Pagamento referente a <?= htmlspecialchars($payment['description']) ?></div>
        </div>
    </div>

    <script>
        // Prevenir que a janela seja redirecionada
        window.onload = function() {
            if(window.opener && !window.opener.closed) {
                document.querySelector('a').addEventListener('click', function(e) {
                    e.preventDefault();
                });
            }
        };
    </script>
</body>
</html>
