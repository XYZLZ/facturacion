const SweetAlert = (title, text)=>{
    Swal.fire({
        title: title,
            text: text,
            toast: true,
            position: "top-right",
            timer: 2000,
            timerProgressBar:true,
            showConfirmButton:false,
            icon: 'success',
})

}

function mostrarMensaje() {
    $('#mensaje').show();
                setTimeout(()=>{
                    $('#mensaje').hide();
                }, 3000);
}

const myModal = new bootstrap.Modal(document.getElementById('modalUsers'));
let option;

export {SweetAlert, mostrarMensaje, myModal, option};

