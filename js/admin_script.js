document.addEventListener("DOMContentLoaded", function () {
	const navbar = document.querySelector('.header .navbar');
	const accountBox = document.querySelector('.header .account-box');

    const menuBtn = document.querySelector('#menu-btn');
    if (menuBtn && navbar) {
        menuBtn.addEventListener("click", () => {
            navbar.classList.toggle('active');
            accountBox?.classList.remove('active');
        });
    }

    const userBtn = document.querySelector('#user-btn');
    if (userBtn && accountBox) {
        userBtn.addEventListener("click", () => {
            accountBox.classList.toggle('active');
            navbar?.classList.remove('active');
        });
    }

    window.addEventListener("scroll", () => {
        navbar?.classList.remove('active');
        accountBox?.classList.remove('active');
    });
});
