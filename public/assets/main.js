/* navbar.js */
const navToggler = document.getElementById('navbar-toggler');
const navItems = document.getElementById('navbar-items');
const darkenBackground = document.getElementById('darken-background');

const MENU_STATE = {
    CLOSED: 0,
    OPEN: 1
}
let navMenuState = MENU_STATE.CLOSED;

const openNavMenu = () => {
    if (navMenuState === MENU_STATE.CLOSED) {
        navItems.classList.add('open');
        navToggler.innerHTML = '<i class="fa fa-close"></i>'
        darkenBackground.classList.add('show');
        navMenuState = MENU_STATE.OPEN;
        return true;
    }
    return false;
}

const closeNavMenu = () => {
    if (navMenuState === MENU_STATE.OPEN) {
        navItems.classList.remove('open');
        darkenBackground.classList.remove('show');
        navToggler.innerHTML = '<i class="fa fa-bars"></i>'
        navMenuState = MENU_STATE.CLOSED;
        return true;
    }
    return false;
}

navToggler.addEventListener('click', () => {
    openNavMenu() || closeNavMenu();
});

darkenBackground.addEventListener('click', closeNavMenu);

window.addEventListener('scroll', closeNavMenu);

window.addEventListener('resize', closeNavMenu);

