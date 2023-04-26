"use strict";

/**
 * Function to open / close the contacts display
 */

// define the constants
const buttonsContacts = document.querySelectorAll('[data-name^="controlButtonDivContact"]');
const showContact = document.querySelectorAll('[data-name^="imgAddContact"]');
const hideContact = document.querySelectorAll('[data-name^="imgSuppContact"]');
const tablesContacts = document.querySelectorAll('[data-name^="tableContact"]');
const isShownContacts = [];

// we listen to all the buttons
buttonsContacts.forEach((button, index) => {
  isShownContacts[index] = false;
    button.addEventListener('click', () => {

        // we hide
      if (isShownContacts[index]) {
        tablesContacts[index].style.display = 'none';
        isShownContacts[index] = false;
        hideContact[index].style.display = 'none'
        showContact[index].style.display = 'block'
        
        // we show
      } else {
        tablesContacts[index].style.display = 'table';
        isShownContacts[index] = true;
        hideContact[index].style.display = 'block'
        showContact[index].style.display = 'none'
      }
    });
});
 

/**
 * Function to open / close the contracts display
 */

// define the constants
const buttonsContracts = document.querySelectorAll('[data-name^="controlButtonDivContract"]');
const showContract = document.querySelectorAll('[data-name^="imgAddContract"]');
const hideContract = document.querySelectorAll('[data-name^="imgSuppContract"]');
const tablesContracts = document.querySelectorAll('[data-name^="tableContract"]');
const isShownContracts = [];

// we listen to all the buttons
buttonsContracts.forEach((button, index) => {
  isShownContracts[index] = false;
    button.addEventListener('click', () => {

        // wi hide
      if (isShownContracts[index]) {
        tablesContracts[index].style.display = 'none';
        isShownContracts[index] = false;
        hideContract[index].style.display = 'none'
        showContract[index].style.display = 'block'
        
        // we show
      } else {
        tablesContracts[index].style.display = 'table';
        isShownContracts[index] = true;
        hideContract[index].style.display = 'block'
        showContract[index].style.display = 'none'
      }
    });
});


/**
 *  Function opening dialog box for confirmation of customer / contract deletion
 */

var btnsSupprimer = document.querySelectorAll(".buttonRemove");

for (var i = 0; i < btnsSupprimer.length; i++) {
  btnsSupprimer[i].addEventListener("click", function(e) {
    var name = this.getAttribute("data-name"); // Get the value of the "data-name" attribute
    if (!confirm("Êtes-vous sûr de vouloir supprimer l'élément " + name + "?")) {
      e.preventDefault(); // Cancel the default action if the user clicks on "Cancel".
    }
  });
}


/**
 *  Function to hide / show the SIRET and PARTNER inputs of the customer forms
 */

const isStatusCheckboxes = document.querySelectorAll('input[name="customer[isPartner]"], input[name="customer[isProfessional]"], input[name="edit_customer[isPartner]"], input[name="edit_customer[isProfessional]"]');
const inputStatuses = document.querySelectorAll('#createCustomerPartner, #createCustomerSiret, #editCustomerPartner, #editCustomerSiret');


// We hide the input fields when the page is loaded
for (let i = 0; i < inputStatuses.length; i++) {
  inputStatuses[i].style.display = 'none';
}


// We add an event listener on each checkbox isPartner / isProfessional to display the corresponding input
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

/**
 * Deletion information not possible when hovering over deleted button that disabled
 */

const deleteBtn = document.getElementById('delete-btn');
deleteBtn.addEventListener('mouseover', function() {
    const modal = document.getElementById('delete-modal');
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
});