/* global bootstrap: false */
(() => {
  'use strict'
  const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(tooltipTriggerEl => {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
})()

const sidebarMenu = document.getElementById('sidebarMenu');
const sidebarToggleButton = document.getElementsByClassName('navbar-toggler');

// Ajoute un événement de clic sur le document entier
document.addEventListener('click', function(event) {
  // Vérifie si le clic est en dehors de la barre latérale
  if (!sidebarMenu.contains(event.target)) {
    // Supprime la classe "active" de la barre latérale
    sidebarMenu.classList.remove('show');
  }
});




