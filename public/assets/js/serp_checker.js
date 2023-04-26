
function checkRank(element) {
  let url = document.getElementById("url").innerHTML;

  // Enter the keyword and the url of the site to check (without the https://)
  let keyword = element.children[1].innerHTML;

  // Access the Google API.
  // Select the "Custom Search" option to create a custom search instance.
  // Obtain an API key for query authentication.
  // Specify the CX (custom search engine identifier) you want to use for the search.
  // Perform a search query by including the desired search term (specified after the "q" parameter in the query string).
  // Specify the output format of the search results by including "alt=json" in the query string.
  let api = "https://www.googleapis.com/customsearch/v1?key=AIzaSyBSBSc6PUZG8waNxQ9wOsRSfmTBqaXlrUI&cx=973460c980706448d&q=" + keyword + "&alt=json";

    let request = new XMLHttpRequest();
    request.open('GET', api, true);
    request.onload = function() {
      // If the request succeeds, we retrieve the data and parse them into json
      if (request.status >= 200 && request.status < 400) {
        let data = JSON.parse(request.responseText);

        // We collect the results of the query and store them in an array

        let items = data.items;
        let rank = 0;
        // We go through the table and we get the rank of the entered url
        // We add 1 to the rank because the table starts at 0
        for (let i = 0; i < items.length; i++) {
          if (items[i].link.indexOf(url) > -1) {
            rank = i + 1;
            break;
          }
        }
        let rankElement = document.querySelector("#rank"+element.dataset.keyword)
        if (rank > 0) {

          rankElement.innerHTML = "Le site est au rang: " + rank;
          // rankElement.innerHTML = element.dataset.keyword;

        } else {
          rankElement.innerHTML = "Le site n'est pas dans les 10 premiers résultats : " + rank;
          // rankElement.innerHTML = element.dataset.keyword;
        }
      } else {
        console.log("error"+ element.dataset.keyword);

      }
    };
    request.onerror = function() {
      console.log("error");


    };
    request.send();
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
  keywords.forEach(keyword => {
    checkRank(keyword);
  })

});

// Dynamic display of the form

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

