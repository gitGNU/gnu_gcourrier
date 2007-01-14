<?php
setlocale(LC_ALL, 'fr_FR.UTF-8');
#header('Content-type: text/html; charset=UTF-8');
header('Content-type: text/plain');
require_once "Text/Password.php";

$prenoms = array("Blandine", "Claudine", "Edith", "Hervé", "Jean-François", "Jean-Luc", "Jean-Paul", "Liliane", "Martine", "Nathalie", "Patricia", "Stéphanie", "Anne-marie", "Bernadette", "Christine", "David", "Francelina", "Georges", "Gérard", "Grégory", "Carine", "Marcel", "Marianne", "Sylvie", "Willy", "Yves");

$noms = array();
foreach($prenoms as $prenom) {
  $nom  = Text_Password::create();
  $nom{0} = strtoupper($nom{0});
  $noms[] = $nom;
  $service = rand(2, 7);
  $login = strtolower($prenom);
  echo "INSERT INTO utilisateur
  (login, nom, prenom, passwd, idService)
  VALUES
  ('$login', '$nom', '$prenom', '', '$service');\n";
}

$services[] = array('ACC', "Accueil");
$services[] = array('COMM', "Communication");
$services[] = array('COMPTA', "Comptabilité");
$services[] = array('MAR', "Bureau du maire");
$services[] = array('SEC', "Secrétariat");
$services[] = array('INFO', "Services informatiques");

foreach ($services as $service) {
  echo "INSERT INTO service (libelle, designation) VALUES('{$service[0]}', '{$service[1]}');\n";
}

$rues = array("avenue", "rue", "boulevard", "impasse");

$noms_rues = array("Allard", "Henri Becquerel", "René Cassin", "Raoul Briquet", "Kléber", "de la liberté", "du Tchard", "Therry", "Thiers", "Jean Médecin", "Pasteur", "Alphonse de Lamartine", "Fragonard", "Valrose");

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


$priorities[0]['designation'] = 'urgente';
$priorities[0]['nbJours'] = 3;
$priorities[1]['designation'] = 'basse';
$priorities[1]['nbJours'] = 15;


foreach ($priorities as $priority) {
  echo "INSERT INTO priorite (designation, nbJours)
        VALUES('{$priority['designation']}', {$priority['nbJours']});\n";
}

# fournisseurs

# facture
# courrier entrant
# courrier départ
## plus transmissions
