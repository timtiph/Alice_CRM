"use strict";


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
