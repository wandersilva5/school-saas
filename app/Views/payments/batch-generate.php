<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Gerar Mensalidades em Lote</h4>
                    <a href="<?= base_url('payments') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <form action="<?= base_url('payments/batch-generate') ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="month" class="form-label">Mês de Referência</label>
                            <select class="form-select" id="month" name="month" required>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == date('n') ? 'selected' : '' ?>>
                                        <?= strftime('%B', mktime(0, 0, 0, $i, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="year" class="form-label">Ano</label>
                            <select class="form-select" id="year" name="year" required>
                                <?php for($i = date('Y')-1; $i <= date('Y')+1; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="due_day" class="form-label">Dia de Vencimento</label>
                            <input type="number" class="form-select" id="due_day" name="due_day" min="1" max="31" value="10" required>
                        </div>

                        <div class="col-12">
                            <label for="students" class="form-label">Alunos</label>
                            <select class="form-select" id="students" name="students[]" multiple data-choices required>
                                <?php foreach($students as $student): ?>
                                    <option value="<?= $student['id'] ?>">
                                        <?= htmlspecialchars($student['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Gerar Mensalidades
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
