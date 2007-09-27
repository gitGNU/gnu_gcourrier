<?php
/*
Generate random data for the online demo
Copyright (C) 2007  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your
option) any later version.

GCourrier is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see http://www.gnu.org/licenses/.
*/

setlocale(LC_ALL, 'fr_FR.UTF-8');
#header('Content-type: text/html; charset=UTF-8');
header('Content-type: text/plain');
require_once "Text/Password.php";

function random_pick($array) {
  return $array[rand(0,count($array)-1)];
}

function gen_nom() {
  $nom  = Text_Password::create();
  $nom{0} = strtoupper($nom{0});
  return $nom;
}

function gen_frenchpostalcode() {
  $cp1 = sprintf("%02d", rand(1,95));
  $cp2 = sprintf("%03d", rand(1,999));
  return "$cp1$cp2";
}

// Services
$services[] = array('ACC', "Accueil");
$services[] = array('COMM', "Communication");
$services[] = array('COMPTA', "Comptabilité");
$services[] = array('MAR', "Bureau du maire");
$services[] = array('SEC', "Secrétariat");
$services[] = array('INFO', "Services informatiques");

foreach ($services as $service) {
  echo "INSERT INTO service (libelle, designation) VALUES('{$service[0]}', '{$service[1]}');\n";
}

// Utilisateurs
$prenoms = array("Blandine", "Claudine", "Edith", "Hervé", "Jean-François", "Jean-Luc", "Jean-Paul", "Liliane", "Martine", "Nathalie", "Patricia", "Stéphanie", "Anne-marie", "Bernadette", "Christine", "David", "Francelina", "Georges", "Gérard", "Grégory", "Carine", "Marcel", "Marianne", "Sylvie", "Willy", "Yves", "Test");

foreach($prenoms as $prenom) {
  $nom = gen_nom();
  $service = rand(1+1, count($services)+1); // don't use the 'admin'/1 service
  $login = strtolower($prenom);
  echo "INSERT INTO utilisateur
  (login, nom, prenom, passwd, idService)
  VALUES
  ('$login', '$nom', '$prenom', '', '$service');\n";
}


// En-tête pour accusés de réception
$accuse['expediteur'] = "Mairie de Panel-sur-mer";
$accuse['adresse'] = "Hôtel de Ville";
$accuse['codePostal'] = "62123";
$accuse['ville'] = "Panel-sur-mer";
$accuse['telephone'] = '03 21 66 88 77';

echo "UPDATE accuse SET
  expediteur='{$accuse['expediteur']}',
  adresse='{$accuse['adresse']}',
  codePostal='{$accuse['codePostal']}',
  ville='{$accuse['ville']}',
  telephone='{$accuse['telephone']}';\n";


// Priorités
$priorities[0]['designation'] = 'urgente';
$priorities[0]['nbJours'] = 3;
$priorities[1]['designation'] = 'basse';
$priorities[1]['nbJours'] = 15;

foreach ($priorities as $priority) {
  echo "INSERT INTO priorite (designation, nbJours)
        VALUES('{$priority['designation']}', {$priority['nbJours']});\n";
}


// Fournisseurs
$rues = array("avenue", "rue", "boulevard", "impasse");

$noms_rues = array("Allard", "Henri Becquerel", "René Cassin", "Raoul Briquet",
		   "Kléber", "de la liberté", "du Tchard", "Therry", "Thiers",
		   "Jean Médecin", "Pasteur", "Alphonse de Lamartine",
		   "Fragonard", "Valrose");
$villes = array("Montigny", "St-Martin", "Ailleurs", "Labas",
  "Panel-sur-mer", "Mareil-les-bains");

$nb_fournisseurs = 10;
for ($i = 0; $i < $nb_fournisseurs; $i++) {
  $nom = gen_nom();
  $prenom = $prenoms[rand(0,count($prenoms)-1)];
  $rue = $rues[rand(0,count($rues)-1)];
  $nom_rue = $noms_rues[rand(0,count($noms_rues)-1)];
  $ville = strtoupper($villes[rand(0,count($villes)-1)]);
  $numero = rand(1,90);
  $adresse = "$numero $rue $nom_rue";
  $code_postal = gen_frenchpostalcode();
  $telephone = sprintf("%02d", rand(1,5));
  for ($t = 0; $t < 4; $t++)
    $telephone .= sprintf("%02d", rand(0,99));

  echo "INSERT INTO destinataire (nom, prenom, adresse, codePostal, ville, telephone)
    VALUES ('$nom', '$prenom', '$adresse', '$code_postal', '$ville', '$telephone');\n";
}

// Factures
$date_mairie_start = strtotime('2 years ago');
$now = time();
$nb_factures = 2000;
for ($i = 0; $i < $nb_factures; $i++) {
  $montant = rand(10*100, 10000*100) / 100;
  $montant = strtr($montant, ',', '.'); // fix locale

  $ref = random_pick(array("X", "F")) . rand(1000, 3000);

  $date_mairie = $date_mairie_start;
  $date_mairie += ($now - $date_mairie_start) / $nb_factures * $i;
  $date_mairie = strftime('%Y-%m-%d', $date_mairie);

  $date_facture = strtotime($date_mairie) - rand(0, 86400 * 10);
  $date_facture = strftime('%Y-%m-%d', $date_facture);

  $idFournisseur = rand(1, $nb_fournisseurs);

  $idServiceCreation = rand(1+1, count($services)+1); // don't use the 'admin'/1 service
  $idServiceDest = rand(1+1, count($services)+1); // don't use the 'admin'/1 service

  $priority = rand(1, 3);

  $histo = $services[$idServiceDest-1-1][0];

  echo "INSERT INTO facture
(montant, refFacture, dateFacture, dateFactureOrigine,
observation, validite, idFournisseur,
idServiceCreation, idPriorite, histo, refuse)
VALUES
('$montant', '$ref', '$date_mairie', '$date_facture',
'', 0, $idFournisseur,
$idServiceCreation, $priority, '$histo', 0);\n";
  echo "INSERT INTO estTransmisCopie(idFacture, idService,dateTransmission )
values(LAST_INSERT_ID(), $idServiceDest, '$date_facture');\n";
}

# courrier entrant
# courrier départ
#echo "INSERT INTO courrier
#(libelle, dateArrivee, observation, validite, dateArchivage,
#idDestinataire, idServiceCreation, idPriorite, serviceCourant, type)
#VALUES ()\n";

## plus transmissions
