<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Royal Bio - l'épicerie bio</title>
	<link rel="stylesheet" href="/~SAESYS11/styles/paniers.css">

	<!-- INCLUDES LINKS -->
	<link rel="stylesheet" href="/~SAESYS11/styles.css">
</head>

<body>
	<?php include "../include/header.php"; ?>

	<?php
	require_once("../include/connect.inc.php");
	error_reporting(0);

	if (isset($_SESSION) && $_SESSION['access'] === 'oui') {
		echo "<h1>Vous êtes connecté</h1> <br><br>";

		$idClient = $_SESSION['idClient'];

		// On recherche le nombre de panier du client
		$query = "SELECT COUNT(IDPANIER) AS nbe_panier FROM PANIER WHERE IDCLIENT = '$idClient'";
		$nbePanier = oci_parse($connect, $query);
		$result = oci_execute($nbePanier);

		if (!$result) {
			$e = oci_error($nbePanier);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
			print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

			// Redirection avec message d'erreur
			// header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
			exit();
		}

		$nbePanier = oci_fetch_assoc($nbePanier);

		$nbePanier = intval($nbePanier['NBE_PANIER']);

		// echo '<pre>' . print_r($nbePanier, true) . ' <pre>';

		if ($nbePanier < 1) {
			echo "<h6>Vous n'avez aucun panier</h6>";
		} else if ($nbePanier == 1) {
	?>
			<div class="container">
				<form action="#" method="post">
					<div class="left">
						<div class="title">Détail de votre panier</div>
						<div class="produits">
							<div class="produit">
								<div class="produit-img">
									<img src="/~SAESYS11/img/1.png" alt="image du produit">
								</div>
								<div class="produit-infos">
									<div class="produit-title">Article : Aile de poulet</div>
									<div class="produit-prix">Prix : 13.99 €</div>
								</div>
								<div class="produit-btn">
									<input type="number" value="1" min="1">
									<input type="button" class="btn" value="Supprimer">
									<!-- <button class=" btn" onclick="console.log('Click');">Quantité</button>
									<button class="btn" onclick="document.location.href='/~SAESYS11/include/TraitPanier.php';">Supprimer</button> -->
								</div>
							</div>
						</div>
					</div>

					<div class="right">
						<div class="title">Récapitulatif</div>
						<div class="content">
							<div class="recap">
								<div class="recap-content">
									<div class="recap-li">
										<div class="recap-text">4 articles</div>
										<div class="recap-data">1533.72 €</div>
									</div>
									<div class="recap-li">
										<div class="recap-text">Frais de livraison</div>
										<div class="recap-data">Gratuit</div>
									</div>
								</div>
							</div>
							<div class="commander">
								<div class="commander-content">
									<div class="commander-text">Total panier</div>
									<div class="commander-data">1539.72 €</div>
								</div>
								<input type="submit" value="Passer la commande" class="commander-btn">
								<!-- <a href="#" class="commander-btn">Passer la commande</a> -->
							</div>
						</div>
					</div>
				</form>
			</div>
	<?php
		} else if ($nbePanier > 1) {
			echo '<h3>Vos paniers : </h3> <br><br>';

			// les paniers du client
			$query = "SELECT * FROM PANIER WHERE IDCLIENT = '$idClient'";
			$lespaniers = oci_parse($connect, $query);
			$result = oci_execute($lespaniers);

			if (!$result) {
				$e = oci_error($lespaniers);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
				print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

				// Redirection avec message d'erreur
				// header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
				exit();
			}

			$idpaniers = [];

			while (($lepanier = oci_fetch_assoc($lespaniers)) != false) {
				// echo '<pre>' . print_r($lepanier, true) . '</pre>';
				$idpaniers[] = $lepanier['IDPANIER'];
			}

			for ($i = 0; $i < count($idpaniers); $i++) {
				echo "<a href='/~SAESYS11/pages/affichagePanier.php?panier=$idpaniers[$i]'>Panier n°$idpaniers[$i]</a>";
			}

			// echo '<pre>' . print_r($idpaniers, true) . '</pre>';
		}
	} else {
		echo "<h1>Vous n'êtes pas connecté</h1>";
	}

	?>

	<?php include "../include/footer.php"; ?>
</body>

</html>