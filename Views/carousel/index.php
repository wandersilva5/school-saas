<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Gerenciar Carrossel</h1>
            <a href="/carousel/create" class="btn btn-primary">Adicionar Novo Slide</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (empty($slides)): ?>
                        <div class="text-center py-5">
                            <h4>Nenhum slide encontrado</h4>
                            <p>Clique em "Adicionar Novo Slide" para criar o primeiro slide do carrossel.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">Ordem</th>
                                        <th style="width: 300px;">Imagem</th>
                                        <th>URL da Imagem</th>
                                        <th style="width: 150px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="slides-list">
                                    <?php foreach ($slides as $index => $slide): ?>
                                        <tr data-id="<?= $slide['id']; ?>">
                                            <td>
                                                <span class="badge bg-secondary"><?= $index + 1; ?></span>
                                                <i class="bi bi-arrow-down-up ms-2 text-muted handle" style="cursor: move;"></i>
                                            </td>
                                            <td>
                                                <img src="<?= $slide['image_url']; ?>" alt="Slide <?= $index + 1; ?>" class="img-thumbnail" style="height: 80px; object-fit: cover;">
                                            </td>
                                            <td class="text-truncate" style="max-width: 400px;">
                                                <?= $slide['image_url']; ?>
                                            </td>
                                            <td>
                                                <a href="/carousel/edit/<?= $slide['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                                <a href="/carousel/delete/<?= $slide['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este slide?');">Excluir</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Inicializar Sortable.js para arrastar e soltar
    document.addEventListener('DOMContentLoaded', function() {
        const slidesList = document.getElementById('slides-list');
        
        if (slidesList) {
            const sortable = new Sortable(slidesList, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    // Obter a nova ordem dos slides
                    const slideIds = Array.from(slidesList.querySelectorAll('tr')).map(tr => tr.dataset.id);
                    
                    // Enviar para o servidor
                    const formData = new FormData();
                    formData.append('slideIds', JSON.stringify(slideIds));
                    
                    fetch('/carousel/reorder', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Atualizar os números de ordem
                            slidesList.querySelectorAll('tr').forEach((tr, index) => {
                                tr.querySelector('span.badge').textContent = index + 1;
                            });
                            
                            // Mostrar mensagem de sucesso temporária
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = 'Ordem dos slides atualizada com sucesso! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            
                            document.querySelector('.card-body').prepend(alert);
                            
                            // Remover alerta após 3 segundos
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        } else {
                            console.error('Erro ao reordenar slides:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar ordem:', error);
                    });
                }
            });
        }
    });
</script>
<?php endpush() ?>