<div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Gerenciar Imagens do Slider</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fas fa-plus"></i> Nova Imagem
            </button>
        </div>
        <div class="card-body">
            <div class="row" id="image-container">
                <?php foreach ($images as $image): ?>
                    <div class="col-md-3 mb-4" data-id="<?= $image['id'] ?>">
                        <div class="card">
                            <img src="/uploads/slider/<?= $image['image_url'] ?>" class="card-img-top" alt="Slider Image">
                            <div class="card-body">
                                <button class="btn btn-danger btn-sm delete-image" data-id="<?= $image['id'] ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload de Nova Imagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" action="/slider-images/store" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="imageInput" class="form-label">Selecione uma imagem</label>
                            <input type="file" class="form-control" name="image" id="imageInput" accept="image/*" required>
                        </div>
                        <div id="imagePreview" style="display: none;">
                            <div class="card">
                                <img src="" alt="Preview" class="card-img-top" id="previewImage" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmUpload" disabled>Confirmar Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('uploadModal');
            const imageInput = document.getElementById('imageInput');
            const previewImage = document.getElementById('previewImage');
            const imagePreview = document.getElementById('imagePreview');
            const confirmUpload = document.getElementById('confirmUpload');
            const uploadForm = document.getElementById('uploadForm');

            // Limpar modal ao fechar
            modal.addEventListener('hidden.bs.modal', function() {
                imageInput.value = '';
                imagePreview.style.display = 'none';
                confirmUpload.disabled = true;
            });

            // Preview da imagem
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                        confirmUpload.disabled = false;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Upload da imagem
            confirmUpload.addEventListener('click', function() {
                const formData = new FormData(uploadForm);

                fetch('/slider-images/store', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Deletar imagem
            document.querySelectorAll('.delete-image').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Tem certeza que deseja excluir esta imagem?')) {
                        const imageId = this.dataset.id;
                        const imageCard = this.closest('.col-md-3');

                        fetch(`/slider-images/delete/${imageId}`, {
                            method: 'POST',
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                imageCard.remove();
                                // Opcional: Mostrar mensagem de sucesso
                                alert('Imagem excluída com sucesso!');
                            } else {
                                alert('Erro ao excluir imagem: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erro ao excluir imagem');
                        });
                    }
                });
            });

            // Sortable initialization
            new Sortable(document.getElementById('image-container'), {
                animation: 150,
                onEnd: function() {
                    const images = Array.from(document.querySelectorAll('#image-container > div')).map(el => el.dataset.id);
                    fetch('/slider-images/update-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            images
                        })
                    });
                }
            });
        });
    </script>