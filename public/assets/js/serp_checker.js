  function checkRank() {
  // On saisit le mot clé et l'url du site à vérifier (sans le https://)
    let keyword = document.getElementById("keyword").value;
    let url = document.getElementById("url").value;

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
          document.getElementById("rank").innerHTML = "Le site est au rang: " + rank;
        } else {
          document.getElementById("rank").innerHTML = "Le site n'est pas dans les 10 premiers résultats";
        }
      } else {
        console.log("error");
      }
    };
    request.onerror = function() {
      console.log("error");
    };
    request.send();

  }
