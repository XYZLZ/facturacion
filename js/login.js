const sign_in_btn = document.querySelector("#sign-in-btn"); 
const sign_up_btn = document.querySelector("#sign-up-btn"); 
const container = document.querySelector(".container");

sign_up_btn.addEventListener('click', ()=>{
    container.classList.add("sign-up-mode");

    if (container.classList.contains("sign-up-mode")) {
        localStorage.setItem("sign-up", "true");
    }
});

sign_in_btn.addEventListener('click', ()=>{
    container.classList.remove("sign-up-mode");

    localStorage.setItem("sign-up", "false");
});

if (localStorage.getItem("sign-up") === "true") {
    container.classList.add("sign-up-mode");
}

if (localStorage.getItem("sign-up") === "false") {
    container.classList.remove("sign-up-mode");
}

// para el loader
setTimeout(function() {
    $('.loader_bg').fadeToggle();
}, 1500);

