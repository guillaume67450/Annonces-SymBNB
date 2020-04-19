# Annonces-SymBNB







Quelques exemples d'utilisation du repository :

// Méthode find : permet de retrouver un enregistrement grâce à son identifiant
$ad = $repo->find(332) ;

$ad = $repo->findOneBy([
    'title' => "Annonce corrigée !",
    'id' => 320
]);

$ads = $repo->findBy([],[], 5, 0); 
// on prend toutes les annonces sans critère de recherche, sans ordre particulier, en en prenant 5 à partir de 0