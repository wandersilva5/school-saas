<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= $pageTitle ?></h5>
                    <a href="/payments" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="/payments/update/<?= $payment['id'] ?>" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Aluno <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="student_id" name="student_id" required>
                                <option value="">Selecione um aluno</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= $student['id'] == $payment['student_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($student['name']) ?> 
                                        <?= !empty($student['class_name']) ? '(' . htmlspecialchars($student['class_name']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($payment['description']) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="reference_month" class="form-label">Mês de Referência <span class="text-danger">*</span></label>
                            <select class="form-select" id="reference_month" name="reference_month" required>
                                <option value="">Selecione um mês</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == $payment['reference_month'] ? 'selected' : '' ?>>
                                        <?= month_name($i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="reference_year" class="form-label">Ano de Referência <span class="text-danger">*</span></label>
                            <select class="form-select" id="reference_year" name="reference_year" required>
                                <option value="">Selecione um ano</option>
                                <?php for ($year = date('Y') - 1; $year <= date('Y') + 2; $year++): ?>
                                    <option value="<?= $year ?>" <?= $year == $payment['reference_year'] ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="due_date" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="<?= $payment['due_date'] ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="amount" name="amount" value="<?= number_format($payment['amount'], 2, ',', '.') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="discount_amount" class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="discount_amount" name="discount_amount" value="<?= number_format($payment['discount_amount'] ?? 0, 2, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="fine_amount" class="form-label">Multa por Atraso</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="fine_amount" name="fine_amount" value="<?= number_format($payment['fine_amount'] ?? 0, 2, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Pendente" <?= $payment['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Pago" <?= $payment['status'] === 'Pago' ? 'selected' : '' ?>>Pago</option>
                                <option value="Atrasado" <?= $payment['status'] === 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                                <option value="Cancelado" <?= $payment['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="boleto_code" class="form-label">Código do Boleto</label>
                            <input type="text" class="form-control" id="boleto_code" name="boleto_code" value="<?= htmlspecialchars($payment['boleto_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Método de Pagamento</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="">Selecione</option>
                                <option value="Boleto" <?= ($payment['payment_method'] ?? '') === 'Boleto' ? 'selected' : '' ?>>Boleto</option>
                                <option value="Cartão" <?= ($payment['payment_method'] ?? '') === 'Cartão' ? 'selected' : '' ?>>Cartão de Crédito/Débito</option>
                                <option value="PIX" <?= ($payment['payment_method'] ?? '') === 'PIX' ? 'selected' : '' ?>>PIX</option>
                                <option value="Dinheiro" <?= ($payment['payment_method'] ?? '') === 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($payment['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="text-end">
                        <a href="/payments" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Helper function to get month name
function month_name($month) {
    $months = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];
    return $months[$month] ?? $month;
}
?>

<?php push('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
    }
</style>
<?php endpush() ?>

<?php push('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione uma opção'
        });

        // Initialize money mask
        $('.money').mask('#.##0,00', {reverse: true});
    });
</script>
<?php endpush() ?>