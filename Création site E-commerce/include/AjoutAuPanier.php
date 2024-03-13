<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Royal Bio - l'épicerie bio</title>
</head>
<body>
</body>

<?php
	session_start();

	if(isset($_POST['fonction'])){
		switch ($_POST['fonction']) {
			case 'ajoutAuPanier':
				ajoutAuPanier($_POST['idProduit'],$_POST['idClient']);
				break;
		}
	}

	function showAlert($msg) {
  		echo "<script>alert('$msg');</script>";
  		header("Refresh: 0; URL=/~SAESYS11/pages/produit.php".$_POST['url']);
		exit();
	}

		function ajoutAuPanier($idProduit, $idClient) {
    require_once("connect.inc.php");
    if (!isset($_SESSION['access']) || $_SESSION['access'] != 'oui') {
        header('Location: /../~SAESYS11/pages/connexion.php');
        exit();
    } else {
        // Validate and sanitize user input
        $idProduit = filter_var($idProduit, FILTER_VALIDATE_INT);
        $idClient = filter_var($idClient, FILTER_VALIDATE_INT);

        // Check if input is valid
        if ($idProduit === false || $idClient === false) {
            showAlert("Erreur : entrée non valide");
        } else {
            try {
                $query = "begin
                                ADD_TO_CART(
                                    P_ID_PANIER => :p_id_panier,
                                    P_ID_CLIENT => :p_id_client,
                                    P_ID_PRODUIT => :p_id_produit
                                );
                            end;";
                $stid = oci_parse($connect, $query);
                oci_bind_by_name($stid, ':p_id_panier', $idClient);
                oci_bind_by_name($stid, ':p_id_client', $idClient);
                oci_bind_by_name($stid, ':p_id_produit', $idProduit);
                oci_execute($stid);
                oci_free_statement($stid);
                oci_close($connect);

                showAlert("Produit ajouté au panier !");
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
}

?>