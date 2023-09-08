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
    $('#nit').val('');
    $('#nombre').val('');
    $('#telefono').val('');
    $('#direccion').val('');
    $('.envio').html('Crear');
    $('#exampleModalLabel').html('Registrar Clientes');
    myModal.show();
    option = 'crear';
   // $('.envio').attr('disabled', 'disabled');
});



$('#userForm').submit(function(e) {
    e.preventDefault();

    if (option == 'crear') {
        let nit = $('#nit').val();
        let nombre = $('#nombre').val()
        let telefono = $('#telefono').val();
        let direccion = $('#direccion').val();
        let action = 'crearUsuario';

        $.post('crudClientes.php', {action, nit, nombre, telefono, direccion}, (response)=>{
            console.log(response);
            $('#userForm').trigger('reset');
            if (response == 'code1') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Todos los campos son obligatorios");
                $('#mensaje').show();
                mostrarMensaje();
            }else if (response == 'code2') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("El nit ya existe");
                $('#mensaje').show();
                mostrarMensaje();
            }else if (response == 'code3') {
                SweetAlert('Crear Usuario', `El Cliente ${nombre} se ha creado correctamente`);
                myModal.hide();
            }else{
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Error al guardar el cliente");
            }
        });
    }

    if (option == 'editar') {
        let nit = $('#nit').val();
        let nombre = $('#nombre').val()
        let telefono = $('#telefono').val();
        let direccion = $('#direccion').val();
        let idEdit = $('#userID').val();
        let action = 'editarUsuario';
        

        $.post('crudClientes.php', {action, nit, nombre, telefono, direccion, id:idEdit}, (response)=>{
            //fetchUsers();
            //$('#userForm').trigger('reset');
            if (response == 'code1') {
                $('#mensaje').addClass("danger");
                $('#mensaje').html("Todos los campos son obligatorios");
                mostrarMensaje();
            }else if (response == 'code2') {
                $('#mensaje').addClass("warning");
                $('#mensaje').html("El nit ya existe");
                mostrarMensaje();
            }else if (response == 'code3') {
                SweetAlert('Crear Usuario', `Cliente actualizado correctamente`);
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

        const nitForm = fila.children[1].innerHTML; 
        const nombreForm = fila.children[2].innerHTML; 
        const telefonoForm = fila.children[3].innerHTML;
        const direccionForm = fila.children[4].innerHTML;

        $('#nit').val(nitForm);
        $('#nombre').val(nombreForm);
        $('#telefono').val(telefonoForm);
        $('#direccion').val(direccionForm);
        $('#userID').val(id);
        $('#exampleModalLabel').html('Editar Clientes');


        option = 'editar';
        $('.envio').html('Editar');
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
                $.post('crudClientes.php', {action:action, idcliente:id}, (response)=>{
                    //fetchUsers();

                    SweetAlert("Eliminar Usuario", `Haz eliminario el usuario con el id ${id}`);
                    location.reload();
                });

            }
        })
    }

    eliminar();
})

