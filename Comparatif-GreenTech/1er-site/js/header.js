function gererChapitres(titre) {

	var chapitres = document.getElementById("chapitres");
	var bouton = document.getElementById("chapitre");
	var header = document.getElementsByTagName('header')[0];
	var Hheigth = header.clientHeight;

	if (chapitres.offsetTop != "-85") {
		chapitres.style.top = "-85px";
		chapitres.style.opacity = "0";
		bouton.innerHTML = titre+" ▼";
	} else {
		chapitres.style.top = Hheigth+"px";
		chapitres.style.opacity = "1";
		bouton.innerHTML = titre+" ▲";
	}
}