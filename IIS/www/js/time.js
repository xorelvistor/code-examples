/* Zobrazuje datum a aktualni cas*/
function time() {
	var dny = new Array(
	"Neděle","Pondělí","Úterý","Středa",
	"Čtvrtek","Pátek","Sobota");
	var mesice = new Array(
	"Ledna","Února","Března","Dubna","Května","Června",
	"Července","Srpna","Září","Října","Listopadu","Prosince");
	var ElDatum, ElCas;
	var poradi, den, mesic, rok;
	var cas = new Date();
	ElDatum= document.getElementById("date");
	ElCas = document.getElementById("clock");
	var hod, min, sek;
	poradi = cas.getDate();
	den = dny[cas.getDay()];
	mesic = mesice[cas.getMonth()];
	rok = cas.getFullYear();
	hod = cas.getHours();
	min = cas.getMinutes();
	sek = cas.getSeconds();
	if (hod < 10) hod="0"+hod;
	if (min < 10) min="0"+min;
	if (sek < 10) sek="0"+sek;	
	ElDatum.innerHTML = den+", "+poradi+". "+mesic+" "+rok;
	ElCas.innerHTML = "\t"+hod+":"+min+":"+sek;
}

function goforit(){
	setInterval("time()",10);
}
/* Zajistuje funkcnost panelu */
function view(index) {
	var taby = document.getElementById("taby");
	var seznam = taby.getElementsByTagName("li");
	var delka = seznam.length;
	var obsah = document.getElementsByName("obsah");
	for (var i=0;i < delka;i++) {
		if (i == index) {
			obsah[i].setAttribute('style',"display: block");
			seznam[i].className="active";
		} else {
			obsah[i].setAttribute('style',"display: none");
			seznam[i].className="";
		}
	}
}