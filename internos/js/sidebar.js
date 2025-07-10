// Obtén la referencia a la sidebar
const sidebar = document.getElementById('sidebar');

// Expande la sidebar cuando el mouse pasa sobre ella
sidebar.addEventListener('mouseenter', function () {
    sidebar.classList.add('active');
    document.querySelector('.content').classList.add('active');
});

// Contraer la sidebar cuando el mouse se retira
sidebar.addEventListener('mouseleave', function () {
    sidebar.classList.remove('active');
    document.querySelector('.content').classList.remove('active');
});