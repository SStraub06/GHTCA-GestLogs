function searchLogs(){

let search = document.getElementById("search").value
let site = document.getElementById("site").value
let date_debut = document.getElementById("date_debut").value
let date_fin = document.getElementById("date_fin").value

fetch("../api/search.php",{

method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:
"search="+encodeURIComponent(search)+
"&site="+site+
"&date_debut="+date_debut+
"&date_fin="+date_fin

})

.then(response => response.text())

.then(data => {

document.getElementById("results").innerHTML = data

})

}