
function checkRank(elements) {
  let url = document.getElementById("url").value;
  elements.forEach(element => {

  // On saisit le mot clé et l'url du site à vérifier (sans le https://)
  let keyword = document.querySelector(".keyword").innerHTML;

  // ON saisit l'api google custom search avec la clé après key et le moteur de recherche (créé sur l'api google) à utiliser après cx. On passe le mot clé en paramètre de la query string après q et on précise que l'on veut du json en sortie après alt
  let api = "https://www.googleapis.com/customsearch/v1?key=AIzaSyBSBSc6PUZG8waNxQ9wOsRSfmTBqaXlrUI&cx=973460c980706448d&q=" + keyword + "&alt=json";

    let request = new XMLHttpRequest();
    request.open('GET', api, true);
    request.onload = function() {
      //Si la requête aboutit, on récupère les données et on les parse en json
      if (request.status >= 200 && request.status < 400) {
        let data = JSON.parse(request.responseText);

        // On récupère les résultats de la requête et on les stocke dans un tableau

        let items = data.items;
        let rank = 0;
        // On parcourt le tableau et on récupère le rang de l'url saisie
        //  On ajoute 1 au rang car le tableau commence à 0
        for (let i = 0; i < items.length; i++) {
          if (items[i].link.indexOf(url) > -1) {
            rank = i + 1;
            break;
          }
        }
        if (rank > 0) {

          document.querySelector(".rank").innerHTML = "Le site est au rang: " + rank;
        } else {
          document.querySelector(".rank").innerHTML = "Le site n'est pas dans les 10 premiers résultats";
        }
      } else {
        console.log("error"+ element.dataset.keyword);

      }
    };
    request.onerror = function() {
      console.log("error");


    };
    request.send();
  });
}

// function test(elements) {
//   elements.forEach(element => {
//     let rank = document.querySelector("#rank"+element.dataset.keyword)
//       rank.innerHTML = "test" + element.dataset.keyword;
//   })
// }

let keywords = document.querySelectorAll(".keyword-container");
let searchButton = document.querySelector("#serp");
searchButton.addEventListener("click", function() {
  checkRank(keywords);
});

// Affichage dynamique du form

$serpInfoForm = document.querySelector(".serp-info-form");
$addSerpButton = document.querySelector(".add-serp-button");
$closeSerpFormButton = document.querySelector(".close-serp-form");

$serpInfoForm.style.display = "none";

$addSerpButton.addEventListener("click", function(){
  $addSerpButton.style.display = "none";
  $serpInfoForm.style.display = "flex";
})

$closeSerpFormButton.addEventListener("click", function(){
  $addSerpButton.style.display = "block";
  $serpInfoForm.style.display = "none";
})

