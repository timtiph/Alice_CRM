"use strict";
 


var bouton = document.getElementById('controlButtonDiv');
console.log(bouton);
var div = document.getElementById('divContrat');
console.log(div);

if (bouton !== null && bouton instanceof HTMLCollection) {
    bouton.addEventListener("click", function() {
        if (div.style.display === "none") {
        div.style.display = "block";
        } else if (div.style.display === "block") {
        div.style.display = "none";
        }
    })};