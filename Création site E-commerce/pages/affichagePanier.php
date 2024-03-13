<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Bio - l'épicerie Bio</title>
    <link rel="stylesheet" href="/~SAESYS11/styles/panier.css">

    <!-- INCLUDES LINKS -->
    <link rel="stylesheet" href="/~SAESYS11/styles.css">
</head>

<body>
    <?php include "../include/header.php"; ?>
    <div class="container">
        <form action="#" method="post">
            <div class="left">
                <div class="title">Détail de votre panier</div>
                <div class="produits">

                    <?php
                    require_once("../include/connect.inc.php");
                    error_reporting(0);

                    if (isset($_SESSION) && $_SESSION['access'] === 'oui') {

                        $idpanier = $_GET['panier'];

                        // echo $idpanier;

                        // On recherche les informations du panier de l'article
                        $query = "SELECT * FROM PANIER WHERE IDPANIER = '$idpanier'";
                        $lepanier = oci_parse($connect, $query);
                        $result = oci_execute($lepanier);

                        if (!$result) {
                            $e = oci_error($lepanier);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
                            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

                            // Redirection avec message d'erreur
                            // header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
                            exit();
                        }

                        $lepanier = oci_fetch_assoc($lepanier);
                        $totalPanier = $lepanier['PRIXPANIER'];

                        // On recherche le nombre d'article du panier
                        $query = "SELECT COUNT(IDPRODUIT) AS TOTALARTICLE FROM CONTENUPANIER WHERE IDPANIER = '$idpanier'";
                        $totalArticle = oci_parse($connect, $query);
                        $result = oci_execute($totalArticle);

                        if (!$result) {
                            $e = oci_error($totalArticle);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
                            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

                            // Redirection avec message d'erreur
                            // header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
                            exit();
                        }

                        $totalArticle = oci_fetch_assoc($totalArticle);
                        $totalArticle = intval($totalArticle['TOTALARTICLE']);

                        // On recherche les informations du contenu du panier
                        $query = "SELECT * FROM CONTENUPANIER WHERE IDPANIER = '$idpanier'";
                        $lespaniers = oci_parse($connect, $query);
                        $result = oci_execute($lespaniers);

                        if (!$result) {
                            $e = oci_error($lespaniers);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
                            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

                            // Redirection avec message d'erreur
                            // header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
                            exit();
                        }

                        /*
        * DEBOGGAGE ERREUR : UN SEULE PRODUIT S'AFFICHE

        while (($lepanier = oci_fetch_assoc($lepanier)) != false) {
            echo '<pre>' . print_r($lepanier, true) . '</pre>';
        }
        exit();
        */

                        while (($lepanier = oci_fetch_assoc($lespaniers)) != false) {
                            $idproduit = $lepanier['IDPRODUIT'];

                            // On recherche les informations des produit du panier
                            $query = "SELECT * FROM PRODUIT WHERE IDPRODUIT = '$idproduit'";

                            $produits = oci_parse($connect, $query);
                            $result = oci_execute($produits);

                            if (!$result) {
                                $e = oci_error($produits);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
                                print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

                                // Redirection avec message d'erreur
                                // header('location: /~SAESYS11/pages/connexion.php?error=Erreur connexion ... Réessayer');
                                exit();
                            }


                    ?>

                            <?php

                            while (($produit = oci_fetch_assoc($produits)) != false) {

                            ?>

                                <div class="produit">
                                    <div class="produit-img">
                                        <img src="/~SAESYS11/img/<?php echo $produit['IDPRODUIT'] ?>.png" alt="image du produit">
                                    </div>
                                    <div class="produit-infos">
                                        <div class="produit-title">Article : <?php echo $produit['NOMPRODUIT'] ?></div>
                                        <div class="produit-prix">Prix : <?php echo $produit['PRIXPRODUIT'] ?> €</div>
                                    </div>
                                    <div class="produit-btn">
                                        <input type="number" value="1" min="1">
                                        <input type="button" class="btn" value="Supprimer">
                                    </div>
                                </div>

                            <?php
                            }

                            ?>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="right">
                <div class="title">Récapitulatif</div>
                <div class="content">
                    <div class="recap">
                        <div class="recap-content">
                            <div class="recap-li">
                                <div class="recap-text"><?php echo $totalArticle ?> article(s)</div>
                                <div class="recap-data"><?php echo $totalPanier ?> €</div>
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
                            <div class="commander-data"><?php echo $totalPanier ?> €</div>
                        </div>
                        <input type="submit" value="Passer la commande" class="commander-btn">
                    </div>
                </div>
            </div>
        </form>

        <?php include "../include/footer.php"; ?>
</body>

</html>