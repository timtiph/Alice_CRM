"use strict";

/**
 * Fonction pour open les div contacts et contrats 
 */

const buttonsContacts = document.querySelectorAll('[data-name^="controlButtonDivContact"]');
const showContact = document.querySelectorAll('[data-name^="imgAddContact"]');
const hideContact = document.querySelectorAll('[data-name^="imgSuppContact"]');
const tablesContacts = document.querySelectorAll('[data-name^="tableContact"]');
const isShownContacts = [];

buttonsContacts.forEach((button, index) => {
  isShownContacts[index] = false;
    button.addEventListener('click', () => {

        // on cache
      if (isShownContacts[index]) {
        tablesContacts[index].style.display = 'none';
        isShownContacts[index] = false;
        hideContact[index].style.display = 'none'
        showContact[index].style.display = 'block'
        
        // on affiche
      } else {
        tablesContacts[index].style.display = 'table';
        isShownContacts[index] = true;
        hideContact[index].style.display = 'block'
        showContact[index].style.display = 'none'
      }
    });
});
 

const buttonsContracts = document.querySelectorAll('[data-name^="controlButtonDivContract"]');
const showContract = document.querySelectorAll('[data-name^="imgAddContract"]');
const hideContract = document.querySelectorAll('[data-name^="imgSuppContract"]');
const tablesContracts = document.querySelectorAll('[data-name^="tableContract"]');
const isShownContracts = [];


buttonsContracts.forEach((button, index) => {
  isShownContracts[index] = false;
    button.addEventListener('click', () => {

        // on cache
      if (isShownContracts[index]) {
        tablesContracts[index].style.display = 'none';
        isShownContracts[index] = false;
        hideContract[index].style.display = 'none'
        showContract[index].style.display = 'block'
        
        // on affiche
      } else {
        tablesContracts[index].style.display = 'table';
        isShownContracts[index] = true;
        hideContract[index].style.display = 'block'
        showContract[index].style.display = 'none'
      }
    });
});

/**
 *  Fonction ouverture boite de dialogue pour confirmation suppression client / contrat
 */

var btnsSupprimer = document.querySelectorAll(".buttonRemove");

for (var i = 0; i < btnsSupprimer.length; i++) {
  btnsSupprimer[i].addEventListener("click", function(e) {
    var name = this.getAttribute("data-name"); // Récupérer la valeur de l'attribut "data-name"
    if (!confirm("Êtes-vous sûr de vouloir supprimer l'élément " + name + "?")) {
      e.preventDefault(); // Annuler l'action par défaut si l'utilisateur clique sur "Annuler"
    }
  });
}

/**
 *  Fonction pour cacher / montrer les inputs SIRET et PARTNER des form customer 
 */

const isStatusCheckboxes = document.querySelectorAll('input[name="customer[isPartner]"], input[name="customer[isProfessional]"], input[name="edit_customer[isPartner]"], input[name="edit_customer[isProfessional]"]');
const inputStatuses = document.querySelectorAll('#createCustomerPartner, #createCustomerSiret, #editCustomerPartner, #editCustomerSiret');


// On cache les champs input au chargement de la page
for (let i = 0; i < inputStatuses.length; i++) {
  inputStatuses[i].style.display = 'none';
}

// On ajoute un écouteur d'événements sur chaque case à cocher isPartner / isProfessional
for (let i = 0; i < isStatusCheckboxes.length; i++) {
  const isStatusCheckbox = isStatusCheckboxes[i];
  const inputStatus = inputStatuses[i];

  if (isStatusCheckbox.checked) {
    inputStatus.style.display = 'block';
  } else {
    inputStatus.style.display = 'none';
  }
  

  isStatusCheckbox.addEventListener('change', function() {
    if (isStatusCheckbox.checked) {
      inputStatus.style.display = 'block';
    } else {
      inputStatus.style.display = 'none';
    }
  });
}
