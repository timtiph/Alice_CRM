"use strict";

/**
 * Function to open / close the contacts display
 */

// define the constants
const buttonsContacts = document.querySelectorAll('[data-name^="button-control-view-div-contact"]');
const showContact = document.querySelectorAll('[data-name^="img-show-detail-contact"]');
const hideContact = document.querySelectorAll('[data-name^="img-hide-contact-detail"]');
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
const showContract = document.querySelectorAll('[data-name^="img-show-contract-detail"]');
const hideContract = document.querySelectorAll('[data-name^="img-hide-contract-detail"]');
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

var btnsSupprimer = document.querySelectorAll(".btn-delete");

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
 * Display document description when length > 100
 */
const buttonEye = document.querySelector('.button-eye-document-description');
const descriptionSlice = document.querySelector('.document-description-slice');
const descriptionComplete = document.querySelector('.document-description-complete');
const spanDocInfoDescrib = document.querySelector('.span-info-document-description');


// Hide full description on page load
descriptionComplete.style.display = 'none';

buttonEye.addEventListener('click', () => {
  if (descriptionSlice.style.display === 'none') {
    // If the full description is hidden, show the partial description and hide the full description
    descriptionSlice.style.display = 'block';
    descriptionComplete.style.display = 'none';
  } else {
    // Otherwise, hide the partial description and show the full description
    descriptionSlice.style.display = 'none';
    descriptionComplete.style.display = 'block';
  }

  

});





/**
 * Deletion information not possible when hovering over deleted button that disabled
 */

const deleteBtn = document.getElementById('delete-btn');
deleteBtn.addEventListener('mouseover', function() {
    const modal = document.getElementById('delete-modal');
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
});


