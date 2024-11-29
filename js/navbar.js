function toggleMenu() {
    const menuList = document.getElementById('menuList'); // Najde hlavní menu
    const menuIcon = document.querySelector('.menu-icon'); // Najde hamburger ikonu

    menuList.classList.toggle('show'); // Přepíná viditelnost menu
    menuIcon.classList.toggle('open'); // Přepíná animaci hamburgeru
}


