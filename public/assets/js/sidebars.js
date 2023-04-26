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

// Add event listener on the document
document.addEventListener('click', function(event) {
  // Checks if the click is outside the sidebar
  if (!sidebarMenu.contains(event.target)) {
    // Remove the 'active' class from the sidebar
    sidebarMenu.classList.remove('show');
  }
});




