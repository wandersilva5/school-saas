<!-- Modal de Detalhes -->
<div class="modal fade" id="alunoInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="infoLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>

                <!-- Área de exibição de detalhes existentes -->
                <div id="infoDisplay" style="display: none;">
                    <div class="alert alert-info mb-4">
                        Informações detalhadas do aluno
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Matrícula:</label>
                            <p id="info_matricula">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Data de Nascimento:</label>
                            <p id="info_data_nascimento">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Gênero:</label>
                            <p id="info_genero">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo Sanguíneo:</label>
                            <p id="info_tipo_sanguineo">-</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Endereço:</label>
                            <p id="info_endereco">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contato de Emergência:</label>
                            <p id="info_contato_emergencia">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Telefone de Emergência:</label>
                            <p id="info_telefone_emergencia">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Plano de Saúde:</label>
                            <p id="info_plano_saude">-</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Observações de Saúde:</label>
                            <p id="info_obs_saude">-</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Escola Anterior:</label>
                            <p id="info_escola_anterior">-</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Observações Gerais:</label>
                            <p id="info_observacoes">-</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" onclick="showEditForm()">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <!-- Formulário para adicionar/editar informações -->
                <form id="infoForm" style="display: none;" action="/alunos/update-info" method="POST">
                    <input type="hidden" id="edit_info_id" name="info_id">
                    <input type="hidden" id="edit_aluno_id" name="aluno_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="edit_matricula" name="registration_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="edit_data_nascimento" name="birth_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_genero" class="form-label">Gênero</label>
                            <select class="form-select" id="edit_genero" name="gender" required>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                                <option value="O">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                            <select class="form-select" id="edit_tipo_sanguineo" name="blood_type">
                                <option value="">Selecione</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">Endereço</h5>
                        </div>
                        <div class="col-md-9 mb-3">
                            <label for="edit_rua" class="form-label">Rua/Avenida</label>
                            <input type="text" class="form-control" id="edit_rua" name="address_street" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="edit_numero" name="address_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="edit_complemento" name="address_complement">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="edit_bairro" name="address_district" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="edit_cidade" name="address_city" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="edit_estado" name="address_state" required maxlength="2">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="edit_cep" name="address_zipcode" required>
                        </div>
                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">Informações de Emergência</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_contato_emergencia" class="form-label">Contato de Emergência</label>
                            <input type="text" class="form-control" id="edit_contato_emergencia" name="emergency_contact" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_telefone_emergencia" class="form-label">Telefone de Emergência</label>
                            <input type="text" class="form-control telefone" id="edit_telefone_emergencia" name="emergency_phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_plano_saude" class="form-label">Plano de Saúde</label>
                            <input type="text" class="form-control" id="edit_plano_saude" name="health_insurance">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_obs_saude" class="form-label">Observações de Saúde</label>
                            <textarea class="form-control" id="edit_obs_saude" name="health_observations" rows="3"></textarea>
                            <small class="text-muted">Alergias, condições médicas, medicações, etc.</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_escola_anterior" class="form-label">Escola Anterior</label>
                            <input type="text" class="form-control" id="edit_escola_anterior" name="previous_school">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_observacoes" class="form-label">Observações Gerais</label>
                            <textarea class="form-control" id="edit_observacoes" name="observation" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="cancelEditBtn" class="btn btn-outline-secondary" style="display: none;" onclick="cancelEdit()">Cancelar Edição</button>
                <button type="submit" form="infoForm" id="saveInfoBtn" class="btn btn-primary" style="display: none;">Salvar</button>
            </div>
        </div>
    </div>
</div>