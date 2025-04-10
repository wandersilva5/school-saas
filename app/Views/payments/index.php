<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= base_url('payments/create') ?>" class="btn btn-primary me-2">
                            <i class="bi bi-plus-circle"></i> Novo Pagamento
                        </a>
                        <a href="/payments/batch-generate" class="btn btn-secondary">
                            <i class="bi bi-list-check"></i> Gerar Mensalidades
                        </a>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-white-50 text-uppercase mb-1">Pagamentos Pendentes</div>
                                        <div class="h5 mb-0 font-weight-bold"><?= isset($stats['upcoming_count']) ? $stats['upcoming_count'] : 0 ?></div>
                                    </div>
                                    <div class="fa-2x text-white-50">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-success text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-white-50 text-uppercase mb-1">Total Pago</div>
                                        <div class="h5 mb-0 font-weight-bold">R$ <?= number_format(isset($stats['total_paid']) ? $stats['total_paid'] : 0, 2, ',', '.') ?></div>
                                    </div>
                                    <div class="fa-2x text-white-50">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-info text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-white-50 text-uppercase mb-1">Valor Pendente</div>
                                        <div class="h5 mb-0 font-weight-bold">R$ <?= number_format(isset($stats['total_pending']) ? $stats['total_pending'] : 0, 2, ',', '.') ?></div>
                                    </div>
                                    <div class="fa-2x text-white-50">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-white-50 text-uppercase mb-1">Pagamentos Atrasados</div>
                                        <div class="h5 mb-0 font-weight-bold"><?= isset($stats['overdue_count']) ? $stats['overdue_count'] : 0 ?></div>
                                    </div>
                                    <div class="fa-2x text-white-50">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="/payments">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Todos</option>
                                        <option value="Pendente" <?= isset($filters['status']) && $filters['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="Pago" <?= isset($filters['status']) && $filters['status'] === 'Pago' ? 'selected' : '' ?>>Pago</option>
                                        <option value="Atrasado" <?= isset($filters['status']) && $filters['status'] === 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                                        <option value="Cancelado" <?= isset($filters['status']) && $filters['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="student_id" class="form-label">Aluno</label>
                                    <select class="form-select" id="student_id" name="student_id">
                                        <option value="">Todos</option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?= $student['id'] ?>" <?= isset($filters['student_id']) && $filters['student_id'] == $student['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($student['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="due_date_from" class="form-label">Vencimento (De)</label>
                                    <input type="date" class="form-control" id="due_date_from" name="due_date_from" value="<?= isset($filters['due_date_from']) ? $filters['due_date_from'] : '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="due_date_to" class="form-label">Vencimento (Até)</label>
                                    <input type="date" class="form-control" id="due_date_to" name="due_date_to" value="<?= isset($filters['due_date_to']) ? $filters['due_date_to'] : '' ?>">
                                </div>
                                <div class="col-12 text-end">
                                    <a href="/payments" class="btn btn-outline-secondary me-2">Limpar</a>
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Payments Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Aluno</th>
                                <th>Descrição</th>
                                <th>Referência</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted mb-0">Nenhum pagamento encontrado.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['id'] ?></td>
                                        <td><?= htmlspecialchars($payment['student_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['description']) ?></td>
                                        <td><?= month_name($payment['reference_month']) ?>/<?= $payment['reference_year'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($payment['due_date'])) ?></td>
                                        <td>R$ <?= number_format($payment['amount'], 2, ',', '.') ?></td>
                                        <td>
                                            <?php if ($payment['status'] === 'Pendente'): ?>
                                                <span class="badge bg-warning">Pendente</span>
                                            <?php elseif ($payment['status'] === 'Pago'): ?>
                                                <span class="badge bg-success">Pago</span>
                                            <?php elseif ($payment['status'] === 'Atrasado'): ?>
                                                <span class="badge bg-danger">Atrasado</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Cancelado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/payments/show/<?= $payment['id'] ?>" class="btn btn-sm btn-info" title="Detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($payment['status'] !== 'Pago'): ?>
                                                    <a href="/payments/edit/<?= $payment['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#markPaidModal"
                                                        data-payment-id="<?= $payment['id'] ?>"
                                                        data-payment-amount="<?= $payment['amount'] ?>"
                                                        title="Registrar Pagamento">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <?php if (empty($payment['boleto_code'])): ?>
                                                        <button type="button" class="btn btn-sm btn-secondary"
                                                            onclick="generateBoleto(<?= $payment['id'] ?>)"
                                                            title="Gerar Boleto">
                                                            <i class="bi bi-upc"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('payments/view-boleto/' . $payment['id']) ?>"
                                                            target="_blank"
                                                            class="btn btn-sm btn-secondary"
                                                            title="Visualizar Boleto">
                                                            <i class="bi bi-file-text"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-payment-id="<?= $payment['id'] ?>"
                                                        title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Toast para mensagens -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toast-title">Mensagem</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message"></div>
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
function month_name($month)
{
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
        $('.money').mask('#.##0,00', {
            reverse: true
        });

        // Mark as paid modal
        const markPaidModal = document.getElementById('markPaidModal');
        markPaidModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-payment-id');
            const paymentAmount = button.getAttribute('data-payment-amount');

            document.getElementById('markPaidForm').action = '/payments/mark-as-paid/' + paymentId;
            document.getElementById('payment_amount').value = paymentAmount.replace('.', ',');
        });

        // Delete modal
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
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
            fetch(`/payments/generate-boleto/${currentPaymentId}`, { // Corrigido o caminho
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