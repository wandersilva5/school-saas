<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Configurações Bancárias</h4>

                <form action="/bank-config/update" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Banco</label>
                            <select name="bank_code" class="form-select" required>
                                <option value="">Selecione o banco...</option>
                                <option value="001" <?= ($bankConfig['bank_code'] ?? '') === '001' ? 'selected' : '' ?>>Banco do Brasil</option>
                                <option value="341" <?= ($bankConfig['bank_code'] ?? '') === '341' ? 'selected' : '' ?>>Itaú</option>
                                <option value="033" <?= ($bankConfig['bank_code'] ?? '') === '033' ? 'selected' : '' ?>>Santander</option>
                                <option value="104" <?= ($bankConfig['bank_code'] ?? '') === '104' ? 'selected' : '' ?>>Caixa Econômica</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Agência</label>
                            <input type="text" name="bank_agency" class="form-control" value="<?= $bankConfig['bank_agency'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Conta</label>
                            <input type="text" name="bank_account" class="form-control" value="<?= $bankConfig['bank_account'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Carteira</label>
                            <input type="text" name="bank_wallet" class="form-control" value="<?= $bankConfig['bank_wallet'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Convênio</label>
                            <input type="text" name="bank_agreement" class="form-control" value="<?= $bankConfig['bank_agreement'] ?? '' ?>" required>
                        </div>
                    </div>

                    <h5 class="mt-4">Dados do Cedente</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome/Razão Social</label>
                            <input type="text" name="bank_assignor_name" class="form-control" value="<?= $bankConfig['bank_assignor_name'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CPF/CNPJ</label>
                            <input type="text" name="bank_assignor_document" class="form-control" value="<?= $bankConfig['bank_assignor_document'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Endereço Completo</label>
                        <textarea name="bank_assignor_address" class="form-control" rows="3"><?= $bankConfig['bank_assignor_address'] ?? '' ?></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
