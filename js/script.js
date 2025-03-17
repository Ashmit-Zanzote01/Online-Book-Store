const userBox = document.querySelector('.header .user-box'); // Changed '.account-box' to '.user-box'
const navbar = document.querySelector('.header .navbar');

const userBtn = document.querySelector('#user-btn');
if (userBtn && userBox) {
    userBtn.onclick = () => {
        userBox.classList.toggle('active');
        navbar?.classList.remove('active');
    };
}
const menuBtn = document.querySelector('#menu-btn');
if (menuBtn && navbar) {
    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
        userBox?.classList.remove('active');
    };
}

window.onscroll = () => {
    userBox?.classList.remove('active');
    navbar?.classList.remove('active');
};
