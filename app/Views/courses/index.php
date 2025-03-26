<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?? '' ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            <i class="bi bi-plus-circle"></i> Novo Curso
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nome do Curso</th>
                                <th>Duração</th>
                                <th>Carga de trabalho</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($courses)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum curso encontrado. Crie seu primeiro curso!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['code']) ?></td>
                                        <td><?= htmlspecialchars($course['name']) ?></td>
                                        <td><?= htmlspecialchars($course['duration'] ?? 'Not specified') ?></td>
                                        <td><?= $course['workload'] ? htmlspecialchars($course['workload']) . ' hours' : 'Not specified' ?></td>
                                        <td>
                                            <?php if ($course['active']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="courses/show/<?= $course['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-primary" onclick="editCourse(<?= $course['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?= $course['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastro de um novo Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCourseForm" action="/courses/store" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Código do Curso</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nome do Curso</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label">Duração</label>
                            <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g. 2 years, 4 semesters">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="workload" class="form-label">Carga de trabalho (horas)</label>
                            <input type="number" class="form-control" id="workload" name="workload" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requerimentos</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createCourseForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm" action="/courses/update/" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="edit_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_duration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="edit_duration" name="duration" placeholder="e.g. 2 years, 4 semesters">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_workload" class="form-label">Workload (hours)</label>
                            <input type="number" class="form-control" id="edit_workload" name="workload" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_requirements" class="form-label">Requirements</label>
                        <textarea class="form-control" id="edit_requirements" name="requirements" rows="2"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editCourseForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation if needed
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });

    function editCourse(courseId) {
        console.log("Edit course called with ID:", courseId);

        // Show loading indicator in modal
        document.getElementById('edit_name').value = 'Loading...';
        document.getElementById('edit_code').value = 'Loading...';

        // Show the modal immediately
        const modalElement = document.getElementById('editCourseModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        // Get course data via AJAX
        fetch(`/courses/getById?id=${courseId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error fetching course data: ' + response.status);
                }
                return response.json();
            })
            .then(course => {
                console.log("Course data received:", course);

                // Update form action URL
                document.getElementById('editCourseForm').action = `/courses/update/${course.id}`;

                // Fill form fields
                document.getElementById('edit_id').value = course.id;
                document.getElementById('edit_name').value = course.name;
                document.getElementById('edit_code').value = course.code;
                document.getElementById('edit_duration').value = course.duration || '';
                document.getElementById('edit_workload').value = course.workload || '';
                document.getElementById('edit_description').value = course.description || '';
                document.getElementById('edit_requirements').value = course.requirements || '';
                document.getElementById('edit_active').checked = parseInt(course.active) === 1;
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error loading course: ' + error.message);
                // Close the modal on error
                modal.hide();
            });
    }

    function deleteCourse(courseId) {
        if (confirm('Are you sure you want to delete this course?')) {
            fetch(`/courses/delete/${courseId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        showToast('error', data.error || 'Error deleting course');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error deleting course: ' + error.message);
                });
        }
    }

</script>
<?php endpush() ?>