<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= base_url('payments') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Voltar
                        </a>
                    </div>
                </div>

                <!-- Informações do Pagamento -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informações do Pagamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nº do Pagamento:</strong> <?= $payment['id'] ?></p>
                                <p><strong>Aluno:</strong> <?= htmlspecialchars($payment['student_name']) ?></p>
                                <p><strong>Descrição:</strong> <?= htmlspecialchars($payment['description']) ?></p>
                                <p><strong>Referência:</strong> <?= month_name($payment['reference_month']) ?>/<?= $payment['reference_year'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($payment['due_date'])) ?></p>
                                <p><strong>Valor:</strong> R$ <?= number_format($payment['amount'], 2, ',', '.') ?></p>
                                <p><strong>Status:</strong> 
                                    <?php if ($payment['status'] === 'Pendente'): ?>
                                        <span class="badge bg-warning">Pendente</span>
                                    <?php elseif ($payment['status'] === 'Pago'): ?>
                                        <span class="badge bg-success">Pago</span>
                                    <?php elseif ($payment['status'] === 'Atrasado'): ?>
                                        <span class="badge bg-danger">Atrasado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Cancelado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Boleto -->
                <?php if (!empty($payment['boleto_code'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informações do Boleto</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Código do Boleto:</strong> <?= htmlspecialchars($payment['boleto_code']) ?></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="<?= htmlspecialchars($payment['boleto_url']) ?>" 
                                       target="_blank" 
                                       class="btn btn-primary">
                                        <i class="bi bi-file-text me-1"></i> Visualizar Boleto
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Ações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ações</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group">
                            <?php if ($payment['status'] !== 'Pago'): ?>
                                <a href="/payments/edit/<?= $payment['id'] ?>" class="btn btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#markPaidModal" 
                                    data-payment-id="<?= $payment['id'] ?>"
                                    data-payment-amount="<?= $payment['amount'] ?>"
                                    title="Registrar Pagamento">
                                    <i class="bi bi-check-circle"></i> Registrar Pagamento
                                </button>
                                <?php if (empty($payment['boleto_code'])): ?>
                                    <button type="button" class="btn btn-secondary" 
                                        onclick="generateBoleto(<?= $payment['id'] ?>)" 
                                        title="Gerar Boleto">
                                        <i class="bi bi-upc"></i> Gerar Boleto
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal" 
                                    data-payment-id="<?= $payment['id'] ?>"
                                    title="Excluir">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">Registrar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="markPaidForm" action="/payments/mark-as-paid/" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Cartão">Cartão de Crédito/Débito</option>
                            <option value="PIX">PIX</option>
                            <option value="Dinheiro">Dinheiro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Valor Pago</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control money" id="payment_amount" name="payment_amount" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este pagamento?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Generate Boleto Modal -->
<div class="modal fade" id="generateBoletoModal" tabindex="-1" aria-labelledby="generateBoletoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateBoletoModalLabel">Gerar Boleto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Deseja gerar um boleto para este pagamento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmGenerateBoleto">Gerar Boleto</button>
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

<?php push('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    // Função para mostrar toast
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastTitle = document.getElementById('toast-title');
        const toastMessage = document.getElementById('toast-message');
        
        toast.classList.remove('bg-success', 'bg-danger');
        toast.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
        toast.classList.add('text-white');
        
        toastTitle.textContent = type === 'success' ? 'Sucesso' : 'Erro';
        toastMessage.textContent = message;
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize money mask
        $('.money').mask('#.##0,00', {reverse: true});

        // Mark as paid modal
        const markPaidModal = document.getElementById('markPaidModal');
        markPaidModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-payment-id');
            const paymentAmount = button.getAttribute('data-payment-amount');
            
            document.getElementById('markPaidForm').action = '/payments/mark-as-paid/' + paymentId;
            document.getElementById('payment_amount').value = paymentAmount.replace('.', ',');
        });

        // Delete modal
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-payment-id');
            
            document.getElementById('confirmDelete').onclick = function() {
                window.location.href = '/payments/delete/' + paymentId;
            };
        });

        // Exibir toast da sessão se existir
        <?php if (isset($_SESSION['toast'])): ?>
            showToast('<?= $_SESSION['toast']['message'] ?>', '<?= $_SESSION['toast']['type'] ?>');
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>
    });

    // Generate boleto function
    let currentPaymentId = null;
    
    function generateBoleto(paymentId) {
        currentPaymentId = paymentId;
        const modal = new bootstrap.Modal(document.getElementById('generateBoletoModal'));
        modal.show();
    }

    // Add event listener for generate boleto confirmation
    document.getElementById('confirmGenerateBoleto').addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('generateBoletoModal'));
        modal.hide();
        
        if (currentPaymentId) {
            fetch(`/payments/generate-boleto/${currentPaymentId}`, {  // Corrigido o caminho
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Boleto gerado com sucesso!\nCódigo: ' + data.boleto_code, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.error || 'Erro ao gerar boleto');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Erro desconhecido ao gerar boleto', 'danger');
            });
        }
    });
</script>
<?php endpush() ?>