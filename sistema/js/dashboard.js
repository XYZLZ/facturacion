const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const themeToggler = document.querySelector(".theme-toggler");


// mostrar el menu
menuBtn.addEventListener('click', ()=>{
    sideMenu.style.display = 'block';
});

// ocultar el menu
closeBtn.addEventListener('click', ()=>{
    sideMenu.style.display = 'none';
});

// cambiar tema
themeToggler.addEventListener('click', ()=>{
    document.body.classList.toggle("dark-theme-variables");

    themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
    themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");

    // * save the dark mode

    if (document.body.classList.contains("dark-theme-variables")) {
        localStorage.setItem('dark-mode', 'true');
    }else{
        localStorage.setItem('dark-mode', 'false');
    }

});

// obtain the actual mode

if (localStorage.getItem('dark-mode') === 'true') {
    document.body.classList.add("dark-theme-variables");
    themeToggler.querySelector("span:nth-child(2)").classList.add("active");
    themeToggler.querySelector("span:nth-child(1)").classList.remove("active");
}else{
    document.body.classList.remove("dark-theme-variables");
}
