<?php
	include "../includeAdmin/secuPageAdmin.php";
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifiication produit</title>

    <!-- INCLUDES LINKS -->
    <link rel="stylesheet" href="/~SAESYS11/styles.css">
    <link rel="stylesheet" href="../styleAdmin/gestion.css">
    <?php include "../includeAdmin/headerAdmin.php";?>
</head>
<body>
    <main>
        <?php 
            require_once("../includeAdmin/connect.inc.php");
            $numP = $_GET['pidProduit'];
            $query = "SELECT * FROM PRODUIT WHERE IDPRODUIT = :idP";
            $produits = oci_parse($connect, $query);
            oci_bind_by_name($produits, ":idP", $numP);
            $result = oci_execute($produits);

            // On vérifie que des résultats sont trouvés
            if($result == false){
                echo 'Aucun client';
            }
            $infoProduit = oci_fetch_assoc($produits);
        ?>
    
    <input type="submit" value="Retour" onclick="document.location.href='./GestionProduits.php'"/>

    <form method='POST' enctype="multipart/form-data">
        <fieldset>
            <legend> Modification des informations du produit </legend><BR>

            Id du produit: <input type='text' name='idProduit' value="<?php echo ($infoProduit['IDPRODUIT'])?>" readonly /><BR></BR>
            Id de catégorie : <input type='text' name='idCategorie' value="<?php echo ($infoProduit['IDCATEGORIE'])?>" readonly/><BR></BR>
            Nom du produit : <input type='text' name='nomProduit' value="<?php echo($infoProduit['NOMPRODUIT'])?>" required/><BR><BR> <!--error header-->
            Prix du produit : <input type='text' name='prixProduit' value="<?php echo($infoProduit['PRIXPRODUIT'])?>" required/><BR><BR>
            Fournisseur du produit : <input type='text' name='fournisseurProduit' value="<?php echo($infoProduit['FOURNISSEURPRODUIT'])?>" required/><BR><BR>
            Description du produit : <input type='text' name='descriptionProduit' value="<?php echo($infoProduit['DESCRIPTIONPRODUIT'])?>" required/><BR><BR> <!--error header-->
            Composition du produit : <input type='text' name='compositionProduit' value="<?php echo($infoProduit['COMPOSITIONPRODUIT'])?>" /><BR><BR> <!--error header-->
            Quantité du stock : <input type='text' name='quantiteStock' value="<?php echo($infoProduit['QUANTITESTOCK'])?>"/><BR><BR>
            Promotion : <input type="checkbox" name='promo' id="promo" onclick="extraCheck()"/><BR></BR> 
            <div id="extraDiv">Prix Promo : </div><BR></BR>
            <script>
                function extraCheck() {
                    if (document.getElementById('promo').checked) { 
                        var extraInput = document.createElement("input");
                        extraInput.type = "text";
                        extraInput.name = "prixPromo";
                        extraInput.id = "prixPromo";
                        document.getElementById('extraDiv').appendChild(extraInput);
                    } else {
                        var extra = document.getElementById('prixPromo');
                        extra.remove();
                    }
                }
            </script>

            <input type="file" name="monfichier" /><BR><BR>

            <input type='submit' name='submit' value='Valider'/><BR></BR>
        </fieldset>
    </form><BR><BR>
    </main>


    <?php 
    $isValide=true;
    // var_dump($_FILES['monfichier']);
    // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
    if (!empty($_FILES['monfichier']) AND $_FILES['monfichier']['error'] == 0) {
        // echo "<h1>string</h1>";
        // Testons si l'extension est autorisée
        $infosfichier = pathinfo($_FILES['monfichier']['name']);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('jpeg','gif','png','jpg');

        $pattern="/".$infoProduit['IDPRODUIT']."/"; //pattern pour comparer les noms des fichiers avec l'id du bien
        if (in_array($extension_upload, $extensions_autorisees) &&  5000000 > $_FILES["monfichier"]["size"]) {
            if (!preg_match($pattern,$_FILES['monfichier']['name'])) {
                $isValide=false;
                exit("<br>Veuillez renommer votre fichier comme votre identifiant de poduit<br>");
            }elseif ($isValide==true) {
                    // On peut valider le fichier et le stocker définitivement
                    move_uploaded_file($_FILES['monfichier']['tmp_name'], '../../img' . basename($_FILES['monfichier']['name']));
            }
        }else {
            echo "Le fichier n'est pas du bon type ou il est trop volumineux !<BR>";
        }
    }

    // Si l'ID du produit est envoyé dans le formulaire
    if(isset($_POST['idProduit'])){
        if(isset($_POST['promo'])){
                    $promo='o';
                    $prixPromo=htmlspecialchars($_POST['prixPromo'],ENT_QUOTES, 'UTF-8');
            }else{
                    $promo='n';
                    $prixPromo=NULL;
        }     
        // Si le formulaire est soumis
        if(isset($_POST['submit'])){
            // Variable bindé
            $nomP = htmlspecialchars($_POST['nomProduit'],ENT_QUOTES, 'UTF-8'); 
            $prixP = htmlspecialchars($_POST['prixProduit'],ENT_QUOTES, 'UTF-8'); 
            $fourniP = htmlspecialchars($_POST['fournisseurProduit'],ENT_QUOTES, 'UTF-8'); 
            $descriP = htmlspecialchars($_POST['descriptionProduit'],ENT_QUOTES, 'UTF-8'); 
            $compoP = htmlspecialchars($_POST['compositionProduit'],ENT_QUOTES, 'UTF-8'); 
            $quantiteP = htmlspecialchars($_POST['quantiteStock'],ENT_QUOTES, 'UTF-8');
            $promoP=$promo;
            $prixPromoP=$prixPromo;   

            // Requête de mise à jour de produit dans la base de données
            $query2 = "UPDATE Produit SET NOMPRODUIT=:NOM, PRIXPRODUIT=:PRIX, FOURNISSEURPRODUIT=:FOURNISSEUR, DESCRIPTIONPRODUIT=:DESCRIPTION, COMPOSITIONPRODUIT=:COMPO, QUANTITESTOCK=:STOCK, PROMOTION=:PROMO, PRIXPROMO=:PRIXP WHERE IDPRODUIT=:ID";

            // Préparation de la requête avec Oracle
            $update = oci_parse($connect, $query2);

            // Liaison des variables de formulaire aux paramètres de requête
            oci_bind_by_name($update, ":NOM", $nomP);
            oci_bind_by_name($update, ':PRIX', $prixP);
            oci_bind_by_name($update, ':FOURNISSEUR', $fourniP);
            oci_bind_by_name($update, ':DESCRIPTION', $descriP);
            oci_bind_by_name($update, ':COMPO', $compoP);
            oci_bind_by_name($update, ':STOCK', $quantiteP);
            oci_bind_by_name($update, ':STOCK', $quantiteP);
            oci_bind_by_name($update, ':PROMO', $promo);
            oci_bind_by_name($update, ':PRIXP', $prixPromo);
            oci_bind_by_name($update, ':ID', $_POST['idProduit']);

            // On exécute la requête de mise à jour
            oci_execute($update);

            // On commit les changements
            oci_commit($connect);

            // // On ferme la connexion à la base de données
            oci_close($connect);

            // On redirige l'utilisateur vers la page GestionClient.php
            header("location: /~SAESYS11/admin/pagesAdmin/GestionProduits.php");
            
        }
    }
    ?>




    <?php include "../includeAdmin/footerAdmin.php"; ?>
</body>
</html>