import {SweetAlert, mostrarMensaje, myModal} from './crudFunctions.js';
let option;
// * Mostrar datos

function fetchUsers() {
    let table = 'showRows';
    $.ajax({
        url: "ajax.php",
        type: "GET",
        data: {action:table},
        success: function (response) {
            let rows = JSON.parse(response);
            let template = '';
            
            rows.forEach(row => {
                template += `<tr>
                    <td>${row.id}</td>
                    <td>${row.name}</td>
                    <td>${row.last_name}</td>
                    <td>${row.email}</td>
                    <td class="d-flex align-items-center justify-content-evenly">
                    <a href="#" class="btn btn-outline-primary btn_edit" id="edit">Editar</a>
                    <a href="#" class="btn btn-outline-danger btn_delete" id="delete">Eliminar</a>
                        </td>
                </tr>`;
            });

            $('#listUsers').html(template);
        }
    });

}

// * Insertar e editar datos con AJAX 

$('#btnCrear').on('click', function () {
    $('#nombre').val('');
    $('#correo').val('');
    $('#usuario').val('');
    $('#clave').val('');

    $('.envio').html('Crear');
    $('#exampleModalLabel').html('Registrar Usuario');
    myModal.show();
    option = 'crear';
   // $('.envio').attr('disabled', 'disabled');
});



$('#userForm').submit(function(e) {
    e.preventDefault();

    if (option == 'crear') {
        let nombre = $('#nombre').val();
        let correo = $('#correo').val()
        let usuario = $('#usuario').val();
        let clave = $('#clave').val();
        let rol = $('#rol').val();
        let action = 'crearUsuario';

        $.post('crudUsuarios.php', {action, nombre, correo, usuario, clave, rol}, (response)=>{
            $('#userForm').trigger('reset');
            if (response == 'code1') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Todos los campos son obligatorios");
                mostrarMensaje();
            }else if (response == 'code2') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("El usuario o correo ya existe");
                mostrarMensaje();
            }else if (response == 'code3') {
                SweetAlert('Crear Usuario', `El usuario ${usuario} se ha creado correctamente`);
                myModal.hide();
            }else{
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Error al crear el usuario");
            }
        });
    }

    if (option == 'editar') {
        let nombre = $('#nombre').val();
        let correo = $('#correo').val()
        let usuario = $('#usuario').val();
        let clave = $('#clave').val();
        let rol = $('#rol').val();
        let idEdit = $('#userID').val();
        let action = 'editarUsuario';
        

        $.post('crudUsuarios.php', {action, nombre, correo, usuario, clave, rol, id:idEdit}, (response)=>{
            //fetchUsers();
            //$('#userForm').trigger('reset');
            if (response == 'code1') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Todos los campos son obligatorios");
                mostrarMensaje();
            }else if (response == 'code2') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("El usuario o correo ya existe");
                mostrarMensaje();
            }else if (response == 'code3') {
                SweetAlert('Crear Usuario', `Usuario actualizado correctamente`);
                myModal.hide();
            }else{
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Error al crear el usuario");
            }
        })
    }
});


// * Modal editar

$(document).on('click', '.btn_edit', (e)=>{
    e.preventDefault();
    const fila = e.target.parentNode.parentNode;
        let id = fila.children[0].innerHTML;

        const nombreForm = fila.children[1].innerHTML; 
        const correoForm = fila.children[2].innerHTML; 
        const usuarioForm = fila.children[3].innerHTML;
        const claveForm = fila.children[4].innerHTML;

        $('#nombre').val(nombreForm);
        $('#correo').val(correoForm)
        $('#usuario').val(usuarioForm);
        $('#clave').val(claveForm);
        $('#userID').val(id);

        $('.envio').html('Editar');
        $('#exampleModalLabel').html('Editar Usuario');
        


        option = 'editar';
        myModal.show();

});


// * Para eliminar usuarios
$(document).on('click', '.btn_delete', (e)=>{
    async function eliminar() {
        e.preventDefault();
        const fila = e.target.parentNode.parentNode;
        let id = fila.children[0].innerHTML;
        let action = 'delete';
        
        Swal.fire({
            title: 'Estas Seguro de eliminar este registro?',
            html: '<span class="spanModal">No podras reviertir esto</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-danger)',
            confirmButtonText: 'Eliminar',
            background: 'var(--color-white)',
            customClass:{
                title: 'tituloModal'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('crudUsuarios.php', {action:action, idusuario:id}, (response)=>{
                    //fetchUsers();

                    SweetAlert("Eliminar Usuario", `Haz eliminario el usuario con el id ${id}`);
                    location.reload();
                });

            }
        })
    }

    eliminar();
})
