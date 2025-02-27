$('#userModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const userId = button.data('user-id');
    $('#userId').val(userId);
    
    // Carrega o role atual do usuário
    $.ajax({
        url: '/access-management/get-user-role/' + userId,
        method: 'GET',
        success: function(response) {
            if (response && response.role_id) {
                $('#roleSelect').val(response.role_id);
            } else {
                $('#roleSelect').val('');
            }
        },
        error: function(xhr) {
            console.error('Erro ao carregar perfil do usuário:', xhr);
            $('#roleSelect').val('');
        }
    });
});

// Quando clica em salvar
$('#saveAccess').on('click', function() {
    const formData = $('#userAccessForm').serialize();

    $.ajax({
        url: '/access-management/update-role',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                // Fecha a modal
                $('#userModal').modal('hide');
                
                // Atualiza a tabela
                location.reload();
                
                // Mostra mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao atualizar o perfil'
            });
        }
    });
}); 