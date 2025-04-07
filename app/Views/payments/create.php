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
                <form action="/payments/store" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Aluno <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="student_id" name="student_id" required>
                                <option value="">Selecione um aluno</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>">
                                        <?= htmlspecialchars($student['name']) ?> 
                                        <?= !empty($student['class_name']) ? '(' . htmlspecialchars($student['class_name']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="reference_month" class="form-label">Mês de Referência <span class="text-danger">*</span></label>
                            <select class="form-select" id="reference_month" name="reference_month" required>
                                <option value="">Selecione um mês</option>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="reference_year" class="form-label">Ano de Referência <span class="text-danger">*</span></label>
                            <select class="form-select" id="reference_year" name="reference_year" required>
                                <option value="">Selecione um ano</option>
                                <?php for ($year = date('Y') - 1; $year <= date('Y') + 2; $year++): ?>
                                    <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="due_date" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="amount" name="amount" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="discount_amount" class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="discount_amount" name="discount_amount" value="0,00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="fine_amount" class="form-label">Multa por Atraso</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control money" id="fine_amount" name="fine_amount" value="0,00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="generate_boleto" name="generate_boleto" value="1">
                            <label class="form-check-label" for="generate_boleto">Gerar boleto automaticamente</label>
                        </div>
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

        // Set default due date to 10th of reference month
        $('#reference_month, #reference_year').change(function() {
            const month = $('#reference_month').val();
            const year = $('#reference_year').val();
            
            if (month && year) {
                // Default to 10th day of month
                const dueDate = new Date(year, month - 1, 10);
                $('#due_date').val(dueDate.toISOString().split('T')[0]);
            }
        });

        // Set description based on selection
        $('#student_id, #reference_month, #reference_year').change(function() {
            const studentName = $('#student_id option:selected').text();
            const month = $('#reference_month option:selected').text();
            const year = $('#reference_year').val();
            
            if (studentName && month && year) {
                const cleanStudentName = studentName.split('(')[0].trim();
                $('#description').val(`Mensalidade de ${month}/${year} - ${cleanStudentName}`);
            }
        });
    });
</script>
<?php endpush() ?>