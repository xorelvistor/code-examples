/**
 * Fotogalerie
 * Autor: Radim Jilek, xjilek14
 * Vzniklo v ramci predmetu WAP, FIT VUT, MGM 2014/2015 
 */

/** Volitelne promenne **/
var trida_fotky = "photo";
var trida_titulku = "nazev";
var trida_popisku = "popis";
var sirka_gui = 50; //sirka navigacnich sipek
var b_infoFromImage = false; // dolovat nazev a popisky z atributu obrazku?

/** Programove promenne **/
var nahledy; // globalni seznam nahledu
var b_openGallery = false; //indikuje otevrenou/zavrenou (zobrazenou) galerii
var b_slideshow = false; // indikuje stav spouste slideshow
var aktualni_fotka; // aktualni blok struktury html
/*
<div class="photo">
	<img src="zdroj">
	<div class="nazev">Nazev</div>
	<div class="popis">Popisek</div>
</div>
 */
var intervalId;
var interval = 1; // interval pro slideshow (vychozi - 1s) 
var poradi; // gloabalni promenna uchovavajici poradi soucasne fotky

var pocet_nahledu = 0;
var velikost_nahledu = 0; //px - spocita se v prubehu programu

var h; // vyska clienta
var w; // sirka clienta

window.onload = function() {onReady();};
function onReady() { // jednorazove spusteni pri nacteni skriptu
	if(document.readyState === 'complete') {
		var fotos = document.getElementsByClassName(trida_fotky);
		/* pridani cursoru a reakci na click kazde fotce */
		for (i = 0; i < fotos.length; i++) {
			var picture = fotos[i].getElementsByTagName("img")[0];
			picture.style.cursor = 'pointer';
			picture.onclick = function() { openGallery(this.parentNode);};
			picture.setAttribute("hodnoceni",0);
		}
		
		pocet_nahledu = fotos.length;
		velikost_nahledu = (window.innerWidth * 0.8 * 1.4)/pocet_nahledu;
		
		/* vytvori GUI a prida funkcionalitu */
		createGUI(); 
		createControls();
		
		window.onresize = function (){ onResize();};
		
		/* Ovladani klavesnici */
		document.onkeydown = function (e){ return function (e){ keyReaction(e);}(e);};
	}
}

/** Vytvareni GUI galerie **/
function createGUI() {

	var backgroundGallery = document.createElement("div"); // pozadi
	backgroundGallery.id = "myGalleryBackground";

	var elemGallery = document.createElement("div");
	backgroundGallery.appendChild(elemGallery);
	elemGallery.id = "myGallery";

	/** Hrube deleni GUI galerie **/
	var left = document.createElement("div");
	var center = document.createElement("div");
	var right = document.createElement("div");
	var query = document.createElement("div");
	
	center.id = "centerBlock";
	left.id = "leftBlock";
	right.id = "rightBlock";
	query.id = "imageQuery";

	// Prostor pro nahledy
	for (var i = 0; i < pocet_nahledu; i++) {
		var obal = document.createElement("div");
		query.appendChild(obal);
		
		obal.width = obal.height = velikost_nahledu;
		obal.style.width = obal.style.height = velikost_nahledu;
		obal.style.float = 'left';
	
		obal.innerHTML = "\
			<span class='rank'></span>\
			<img>\
		";
		obal.classList.add("colection","noactive");	
	}
	
	center.style.height = window.innerHeight * 0.8;
	//center.style["max-width"] = window.innerWidth;
	center.style["max-height"] = window.innerHeight;
	var stars = "";
	for(i = 1; i < 6; i++)
		stars += '<div class="empty" name="'+ i + '" onclick="changeRank(' + i + ');"></div>';
	/* Vytvoreni struktury galerie */
	center.innerHTML = '\
		<div id="controlsBlock" >\
			<div id="poradi"></div>\
			<div id="slideshow" title="Spuštění/Zastavení slideshow">\
				<div class="stop stav"></div>\
			</div>\
			<input type="number" id="interval" min="0.5" max="5" value="1" step="0.5" title="Interval přechodu mezi snímky" size="1">[s]\
			<div class="obal"><div class="close" onclick="closeGallery();"></div></div>\
			<div id="hodnoceni" class="star">' 
				+ stars +
			'</div>\
		</div>\
		<div id="imageTitle" ></div>\
		<div id="imageBlock" >\
			<img id="actualImage" class="vertical-center">\
		</div>\
		<div id="imageDescription" ></div>\
	';
	center.appendChild(query);

	right.classList.add("vertical-center");
	left.classList.add("vertical-center");
	
	elemGallery.style.width = window.innerWidth * 0.8;
	elemGallery.style.height = window.innerHeight * 0.8;
	
	backgroundGallery.style.width = "100%";
	backgroundGallery.style.height = window.innerHeight;

	center.appendChild(left);
	elemGallery.appendChild(center);
	center.appendChild(right);
	document.body.appendChild(backgroundGallery);
}

/** Navaze funkcionalitu na ovladaci prvky GUI **/
function createControls () {
	var right = document.getElementById("rightBlock");
	var left = document.getElementById("leftBlock");	
	
	/* Navigacni sipky */
	var vyska = window.innerHeight /5;
	right.onclick = function() { showFoto(aktualni_fotka.nextElementSibling);};
	//right.ontouchstart = function() { showFoto(aktualni_fotka.nextElementSibling);};
	//right.ontouchend = function() { showFoto(aktualni_fotka.nextElementSibling);};
	right.style["border-right-width"] = 0+"px";
	right.style["border-left-width"] = sirka_gui+"px";
	right.style["border-top-width"] = right.style["border-bottom-width"] = vyska+"px";
	
	
	left.onclick = function() {showFoto(aktualni_fotka.previousElementSibling);};
	//left.ontouchstart = function() {showFoto(aktualni_fotka.previousElementSibling);};
	//left.ontouchend = function() {showFoto(aktualni_fotka.previousElementSibling);};
	left.style["border-right-width"] = sirka_gui+"px";
	left.style["border-left-width"] = 0+"px";
	left.style["border-top-width"] = left.style["border-bottom-width"] = vyska+"px";
	
	/* Ovladani slideshow */
	var slideshow_elem = document.getElementById("slideshow").childNodes[1];
	slideshow_elem.onclick = function() { slideshow(this);};
	var interval_elem = document.getElementById("interval");
	interval_elem.onchange = function () {interval = interval_elem.value;};	
	
	var imageBlock_elem = document.getElementById("imageBlock");
	imageBlock_elem.ontouchstart = function() { showFoto(aktualni_fotka.nextElementSibling);};
	
}

/** Otevre galerii se soucasnou fotkou
 * 
 * @param {vybrany blok s fotkou} foto
 */
function openGallery(foto) {
	b_openGallery = true;
	var galerie = document.getElementById("myGalleryBackground");
	galerie.style.display = "block";
	w = document.getElementById("imageBlock").clientWidth;
	h = document.getElementById("imageBlock").clientHeight;
	nahledy = createThumbnails(foto.parentNode.firstElementChild);
	
	showFoto(foto);
}

/** Vytvori nahledy
 * 
 * @param {type} foto
 * @returns {seznam nahledu}
 */
function createThumbnails(foto) {
	var uzel = foto;	
	for (j = 0; j < pocet_nahledu; j++) {
		var img = document.getElementsByClassName("colection")[j].getElementsByTagName('img')[0];
		img.src = uzel.getElementsByTagName('img')[0].src;

		/*img.style["max-width"] = img.style["max-height"]= velikost_nahledu;*/
		var original_image = uzel.getElementsByTagName("img")[0];
		if(original_image.width > original_image.height) 
			img.width = velikost_nahledu;
		else
			img.height = velikost_nahledu;
		
		/* Zobrazeni fotky po vyberu */
		img.parentNode.onclick = function(nahled) {
			return function() { showFoto(nahled); };
		}(uzel);
		
		img.classList.add("vertical-center");
		img.parentNode.classList.add("noactive");
		
		/* Zobrazeni hodnoceni u nahledu */
		hodnoceni = uzel.getElementsByTagName('img')[0].getAttribute("hodnoceni");
		rank_elem = img.parentNode.firstElementChild;
		if(hodnoceni > 0) {
			rank_elem.innerHTML = hodnoceni + "&#x2605;";
		} else
			rank_elem.classList.add("zero-rank");
		
		if(uzel.nextElementSibling !== null)
			uzel = uzel.nextElementSibling;
		else
			uzel = uzel.parentNode.firstElementChild;
	}
	return img.parentNode.parentNode.getElementsByTagName("img"); // seznam nahledu
}

/** Ziska a zobrazi informace o obrazku
 * 
 * @param {blok aktualni fotky} foto
 */
function showFoto(foto) {
	if(foto === null)
		if(aktualni_fotka.nextElementSibling === null)
			foto = aktualni_fotka.parentNode.firstElementChild;
		else
			foto = aktualni_fotka.parentNode.lastElementChild;
	aktualni_fotka = foto;
	
	// Informace o puvodnim obrazku
	var image = foto.getElementsByTagName("img")[0];
	var title;
	var description;
	if (image.title === "")
		title= foto.getElementsByClassName(trida_titulku)[0].textContent;
	else if (b_infoFromImage) 
		title = image.title; // informace primo z atributu TITLE obrazku
	if (image.alt === "")
		description = foto.getElementsByClassName(trida_popisku)[0].textContent;
	else if (b_infoFromImage)
		description = image.alt; // informace primo z atributu ALT obrazku

	var actImage = document.getElementById("actualImage");
	actImage.src = image.src;
	
	resize(image,actImage); // zmena velikosti na prijatelnou uroven zobrazeni

	// Zobrazeni nahledu
	var aktualni = document.getElementsByClassName("active")[0];
	if(aktualni !== undefined) {
		aktualni.classList.remove("active");
		aktualni.classList.add("noactive");
	}
	for (var i = 0; i < nahledy.length; i++) {
		var nahled = nahledy[i];
		if(nahled.src === actImage.src) {
			poradi = i + 1;
			nahled.parentNode.classList.remove("noactive");
			nahled.parentNode.classList.add("active");
			break;
		}
	}
	document.getElementById("poradi").textContent = poradi+"/"+pocet_nahledu;
	document.getElementById("imageTitle").textContent = title;
	document.getElementById("imageDescription").textContent = description;
	
	showRank(image.getAttribute("hodnoceni"));
}


/** Uzavre/skyje galerii a vse nastavi na default **/
function closeGallery() {
	document.getElementById("myGalleryBackground").style.display = "none";
	b_openGallery = false;
	var play_elem = document.getElementById("slideshow").childNodes[1];
	play_elem.classList.remove("play");
	play_elem.classList.add("stop");
	b_slideshow = false;
	clearInterval(intervalId);	
}

/* Zmeni velikost fotky pro zobrazeni */
function resize(image,actImage) {
	if (image.naturalWidth >= w) {
		actImage.style["max-width"] = w;
		actImage.style["max-height"] = h;
	} else if (image.naturalHeight >= h) {
		actImage.style["max-height"] = h;
		actImage.style["max-width"] = w;
	} else {
		actImage.style["max-width"] = image.naturalWidth;	
	}
}

/** Reaguje na ovladani klavesnici
 * 
 * @param {udalost klavesy} e
 */
function keyReaction(e) {
	var event = window.event || e;
	var target = event.srcElement || event.target;
	var kod = event.keyCode;
	if(target.tagName !== "INPUT" && b_openGallery) {
		if (kod === 27) // esc
			closeGallery();
		else if (kod === 37) // left
			showFoto(aktualni_fotka.previousElementSibling);
		else if (kod === 39) // right
			showFoto(aktualni_fotka.nextElementSibling);
		else if (kod === 36) {// home
			showFoto(aktualni_fotka.parentNode.firstElementChild);
			//event.preventDefault();
		} else if (kod === 35) {// end
			showFoto(aktualni_fotka.parentNode.lastElementChild);
			event.preventDefault();
		} else if (kod === 32) { // space
			var slideshow_elem = document.getElementById("slideshow").childNodes[1];
			slideshow(slideshow_elem);
			event.preventDefault();
		}
	}
	
}

/** Zobrazi hodnoceni u aktualniho snimku
 * 
 * @param {hodnota hodnoceni} rank
 */
function showRank(rank) {
	stars_elem = document.getElementById("hodnoceni").childNodes;
	for(i=0; i < 5;i++) {
		if(i+1 <= rank) { // vyplnena hvezda
			stars_elem[i].classList.remove("empty");
			stars_elem[i].classList.add("noempty");
		} else { // prazdna hvezda
			stars_elem[i].classList.remove("noempty");
			stars_elem[i].classList.add("empty");
		}
	}
}

/** Zmeni a ulozi hodnoceni
 * 
 * @param {nova hodnota hodnoceni} newRank
 */
function changeRank(newRank) {
	var image = aktualni_fotka.getElementsByTagName("img")[0];
	image.setAttribute("hodnoceni",newRank); // Ulozeni noveho hodnoceni
	
	/* Zmena hodnoceni v nahledu */
	var rank_elem = document.getElementsByClassName("active")[0].firstElementChild;
	rank_elem.innerHTML = newRank + "&#x2605;";
	console.log(rank_elem);
	rank_elem.classList.remove("zero-rank");
	/* Zobrazeni noveho hodnoceni */
	showRank(newRank);
}

/** Prizpusobi vzhled pri zmene velikosti okna **/
function onResize() {
	/*console.log("Resize window");*/
	var gallery = document.getElementById("myGalleryBackground");
	document.body.removeChild(gallery);
	velikost_nahledu = (window.innerWidth * 1.2)/pocet_nahledu; // prepocet nahledu
	createGUI(); // zmena velikosti GUI
	createControls();
	//document.getElementById("rightBlock").onclick = function() { showFoto(aktualni_fotka.nextElementSibling);};
	//document.getElementById("leftBlock").onclick = function() {showFoto(aktualni_fotka.previousElementSibling);};
	
	/* nezmeni zobrazenou fotku a probihajici slideshow */
	if (b_openGallery)
		openGallery(aktualni_fotka);
	if (b_slideshow) {
		var play_elem = document.getElementById("slideshow").childNodes[1];
		play_elem.classList.remove("stop");
		play_elem.classList.add("play");
	}		
}

/* Ridi slideshow */
function slideshow(obj) {
	if (b_slideshow) {
		obj.classList.remove("play"); // zmena ikony
		obj.classList.add("stop");
		clearTimeout(intervalId);
		b_slideshow = false;
	} else if (!b_slideshow) {
		obj.classList.remove("stop"); // zmena ikony
		obj.classList.add("play");
		b_slideshow = true;
		intervalId = setInterval(function (){showFoto(aktualni_fotka.nextElementSibling);},interval*1000);
	}
}
