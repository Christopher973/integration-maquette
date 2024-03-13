<?php session_start();
require('FPDF/fpdf.php');
class PDF extends FPDF {
    // En-têtes de colonne
    var $headers;
    var $w = array(40, 35, 40, 45, 30, 30, 30, 30);

    function Header() {
        // En-tête
        $this->SetFont('Arial','',12);
        $this->Cell(0,6,'Commande',0,1,'C');
        $this->Ln();
        // En-têtes des colonnes
        for($i=0;$i<count($this->headers);$i++)
            $this->Cell($this->w[$i],7,$this->headers[$i],1,0,'C');
        $this->Ln();
    }

    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial','I',8);
        // Numéro de page
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function LoadData($var) {
        require_once("connect.inc.php");
        error_reporting(0);
        $idCommande=$var;
        $query = 'SELECT MAX(TO_NUMBER(IDCOMMANDE)) as max_number FROM COMMANDE WHERE IDCLIENT=:idclie';
        $stid = oci_parse($connect, $query);
        oci_bind_by_name($stid, ':idclie', $idCommande);
        $result = oci_execute($stid);
        if (!$result) {
            $e = oci_error($stid);  // on récupère l'exception liée au pb d'execution de la requete (violation PK par exemple)
            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);
            exit();
        }
        $row = oci_fetch_array($stid, OCI_ASSOC);
        $max_idcom = $row['MAX_NUMBER'];
        
        
        $query = 'SELECT * FROM commande WHERE idCommande = :idCommande';
        $stid = oci_parse($connect, $query);
        oci_bind_by_name($stid, ':idCommande', $max_idcom);
        oci_execute($stid);
        $invoice_data = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
        oci_free_statement($stid);
        oci_close($connect);
        return $invoice_data;
    }

    function ImprovedTable($data) {
        // Largeurs des colonnes
        $this->headers = array('Id Commande', 'Id Paiement', 'Id Client', 'Date Commande', 'Frais Livraison', 'Adresse Livraison', 'Nom Livraison', 'Telephone Livraison');
        for($i=0;$i<count($data);$i++)
            $this->Cell($this->w[$i],6,$data[$i],'LR');
        $this->Ln();
        // Data
        $this->SetFont('Arial','',12);
        foreach($data as $row) {
            // var_dump ($row);
            //echo $row[0];
            $this->Cell($this->w[0],6,$row['IDCOMMANDE'],'LR');
            $this->Cell($this->w[1],6,$row['idPaiement'],'LR');
            $this->Cell($this->w[2],6,$row['idClient'],'LR');
            $this->Cell($this->w[3],6,$row['dateCommande'],'LR');
            $this->Cell($this->w[4],6,$row['fraisLivraison'],'LR');
            $this->Cell($this->w[5],6,$row['adresseLivraison'],'LR');
            $this->Cell($this->w[6],6,$row['nomLivraison'],'LR');
            $this->Cell($this->w[7],6,$row['telephoneLivraison'],'LR');
            $this->Ln();
        }
        // Trait de terminaison
        $this->Cell(array_sum($this->w),0,'','T');
    }
}

$pdf = new PDF();
echo '1';
$pdf->AliasNbPages();
echo '1';
$data = $pdf->LoadData($_SESSION['idClient']);
echo '1';
$pdf->AddPage();
echo '1';
$pdf->ImprovedTable($data);
echo '1';
// $filename="C:\Users\Jerome\Downloads\test.pdf";
$pdf->Output('I',"./test.pdf",true);
echo '1';

header('Location:../pages/connexion.php')
?>