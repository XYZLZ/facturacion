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
    $('#provedor').val('');
    $('#contacto').val('');
    $('#telefono').val('');
    $('#direccion').val('');

    $('.envio').html('Crear');
    $('#exampleModalLabel').html('Registrar Provedor');
    myModal.show();
    option = 'crear';
   // $('.envio').attr('disabled', 'disabled');
});



$('#userForm').submit(function(e) {
    e.preventDefault();

    if (option == 'crear') {
        let provedor = $('#provedor').val()
        let contacto = $('#contacto').val();
        let telefono = $('#telefono').val();
        let direccion = $('#direccion').val();
        let action = 'crearUsuario';

        $.post('crudProvedores.php', {action, provedor, contacto, telefono, direccion}, (response)=>{
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
                SweetAlert('Crear Usuario', `El provedor ${provedor} se ha creado correctamente`);
                myModal.hide();
            }else{
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Error al crear el usuario");
            }
        });
    }

    if (option == 'editar') {
        let provedor = $('#provedor').val()
        let contacto = $('#contacto').val();
        let telefono = $('#telefono').val();
        let direccion = $('#direccion').val();
        let idEdit = $('#userID').val();
        let action = 'editarUsuario';
        

        $.post('crudProvedores.php', {action, provedor, contacto, telefono, direccion, id:idEdit}, (response)=>{
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
                SweetAlert('Crear Usuario', `provedor actualizado correctamente`);
                myModal.hide();
            }else{
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Error al crear el provedor");
            }
        })
    }
});


// * Modal editar

$(document).on('click', '.btn_edit', (e)=>{
    e.preventDefault();
    const fila = e.target.parentNode.parentNode;
        let id = fila.children[0].innerHTML;

        const provedorForm = fila.children[1].innerHTML; 
        const contasctoForm = fila.children[2].innerHTML; 
        const telForm = fila.children[3].innerHTML;
        const direccionForm = fila.children[4].innerHTML;

        $('#provedor').val(provedorForm)
        $('#contacto').val(contasctoForm);
        $('#telefono').val(telForm);
        $('#direccion').val(direccionForm);
        $('#userID').val(id);

        $('.envio').html('Editar');
        $('#exampleModalLabel').html('Editar Provedor');
        


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
                $.post('crudProvedores.php', {action:action, idproveedor:id}, (response)=>{
                    //fetchUsers();
                    SweetAlert("Eliminar Usuario", `Haz eliminario el usuario con el id ${id}`);
                    location.reload();
                });

            }
        })
    }

    eliminar();
})
