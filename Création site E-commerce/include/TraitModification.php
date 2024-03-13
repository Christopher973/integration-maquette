<?php session_start();

require_once("../include/connect.inc.php");
error_reporting(0);

echo 'Trait Modification <br><br>';

if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['telephone']) && isset($_POST['adresse']) && isset($_POST['motPasse'])) {
    echo 'Champs saisies <br><br>';

    // Regex qui vérifie que le prénom et le nom saisie sont valides
    //  Commence par une majuscule et peux contenir des lettres de l'alphabet ainsi que des tiret
    $regex = '/^[A-Z][a-zA-Z- ]*$/';;

    if ((!preg_match($regex, $_POST['nom'])) || (!preg_match($regex, $_POST['prenom']))) {
        echo 'Les champs nom et prénom doivent commencés par une majuscule et ne pas contenir de chiffre <br><br>';
        exit();
    } else {
        echo 'Les champs nom et prénom sont valide <br><br>';
    }

    // Regex qui vérifie que le mail est valide:
    // Se termine par @ suivi d'une chaîne puis d'un . suivi d'une chaîne
    $regex = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';
    if ((!preg_match($regex, $_POST['email']))) {
        echo "Pour être valide, le mail doit se terminer par @ suivi d'une chaîne puis d'un . suivi d'une chaîne <br><br>";
        exit();
    } else {
        echo 'Le mail est valide <br><br>';
    }

    // Regex qui vérifie que le mot de passe est valide:
    // Contient au minimum 8 caractère dont au minimum : 1 maj, 1 min, 1 chiffre et 1 caractère spécial
    $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<pre>]{8,}$/';
    if ((!preg_match($regex, $_POST['motPasse']))) {
        echo 'Pour être valide, le mot de passe doit :
                <br> - Contenir au minimum 8 caractères,
                <br> - Contenir au minimum 1 majuscule, 
                <br> - Contenir au minimum 1 minuscule,
                <br> - Contenir au minimum 1 chiffre,
                <br> - Contenir au minimum 1 caractère spéciale <br><br>
                ';
        exit();
    } else {
        echo 'Le mot de passe est valide <br><br>';
    }

    // Regex qui vérifie que le num de tel est valide:
    // Contient exactement 10 caractères qui sont des chiffres
    $regex = '/^\d{10}$/';
    if ((!preg_match($regex, $_POST['telephone']))) {
        echo 'Pour être valide, numéro de téléphone doit contenir exactement 10 chiffres';
        exit();
    } else {
        echo 'Le numéro de téléphone est valide <br><br>';
    }

    $email = htmlspecialchars($_POST['email']);
    $query = "SELECT * FROM CLIENTS WHERE MAILCLIENT = '$email'";
    $leclient = oci_parse($connect, $query);
    $result = oci_execute($leclient);

    if (!$result) {
        $e = oci_error($leclient);  // on récupère l'exception liée au pb d'execution de la requete
        print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);
        exit();
    }

    while (($leclient = oci_fetch_assoc($leclient)) != false) {
        echo '<pre>' . print_r($leclient, true) . '</pre>';
        if ($email === $leclient['MAILCLIENT']) {
            echo 'le mail saisit correspond au mail de ce compte';
        } else {
            echo 'le mail saisit ne correspond pas au mail de ce compte';
        }
    }

    $email = $_POST['email'];
    $query = "UPDATE CLIENTS SET NOMCLIENT = :nomClient, PRENOMCLIENT = :prenomClient, MAILCLIENT = :emailClient, TELEPHONECLIENT = :telephoneClient, ADRESSECLIENT = :adresseClient, MOTPASSECLIENT = :motPasseClient WHERE MAILCLIENT = '$email'";

    $update = oci_parse($connect, $query);

    // Variable bindé
    $nomClient = htmlspecialchars($_POST['nom']);
    $prenomClient = htmlspecialchars($_POST['prenom']);
    $emailClient = htmlspecialchars($_POST['email']);
    $telephoneClient = htmlspecialchars($_POST['telephone']);;
    $adresseClient = htmlspecialchars($_POST['adresse']);
    $motPasseClient = password_hash($_POST['motPasse'], PASSWORD_DEFAULT);

    oci_bind_by_name($update, ":nomClient", $nomClient);
    oci_bind_by_name($update, ":prenomClient", $prenomClient);
    oci_bind_by_name($update, ":emailClient", $emailClient);
    oci_bind_by_name($update, ":telephoneClient", $telephoneClient);
    oci_bind_by_name($update, ":adresseClient", $adresseClient);
    oci_bind_by_name($update, ":motPasseClient", $motPasseClient);

    // on exécute la requête
    $result = oci_execute($update);

    // on vérifie les résultats
    if (!$result) {
        $e = oci_error($update);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
        echo '<br><br>';
        print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);

        // Redirection avec message d'erreur
        header('location: /~SAESYS11/index.php?error=Erreur lors de la modification ... Réessayer');
        exit();
    }

    // Commit dans la base de données
    oci_commit($connect);
    oci_free_statement($update);

    // recherche le mail dans la base de donnée
    $query = 'SELECT * FROM clients WHERE MAILCLIENT = :MAILCLIENT';
    $leclient = oci_parse($connect, $query);
    oci_bind_by_name($leclient, ":MAILCLIENT", $email);
    $result = oci_execute($leclient);

    // si erreur de requete alors affichage...
    if (!$result) {
        $e = oci_error($leclient);  // on récupère l'exception liée au pb d'execution de la requete
        print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);
        exit();
    }

    // si pas d'erreur alors on vérifie qu'un client a été trouver
    if (($leclient = oci_fetch_assoc($leclient)) != false && $leclient['MAILCLIENT'] != $_POST['email']
    ) {
        echo '<h1>Un client a été trouvé :</h1> <br>';
        echo '<pre>' . print_r($leclient, true) . '</pre> <br><br>';
        exit();
    } else {
        oci_commit($connect);
    }

    // On parcours les résultats de la requêtes et on modifier les informations de la session
    while (($lesinfos = oci_fetch_assoc($leclient)) != false) {
        $_SESSION['nomClient'] = $lesinfos['NOMCLIENT'];
        $_SESSION['prenomClient'] = $lesinfos['PRENOMCLIENT'];
        $_SESSION['emailClient'] = $lesinfos['MAILCLIENT'];
        $_SESSION['telephoneClient'] = $lesinfos['TELEPHONECLIENT'];
        $_SESSION['adresseClient'] = $lesinfos['ADRESSECLIENT'];
    }
    // Redirection vers la page d'accueil
    header('location: /~SAESYS11/index.php?message=Modification réussite');
} else {
    echo 'Champs non saisies';
}
