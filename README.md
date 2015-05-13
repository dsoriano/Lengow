Lengow module v1.0
===

* [English](#english).
* [French](#francais).

Authors: [Benjamin Perche](benjamin@thelia.net) and [Romain Ducher](rducher@openstudio.fr)

1. Installation
---

Install it as a Thelia module by downloading the zip archive and extracting it in ```thelia/local/modules``` or by uploading it with the backoffice (at ```/admin/modules```),
or by requiring it with Composer:

```json

"require": {
    "thelia/lengow-module": "~1.0"
}
```

2. Usage
---

This module enables you to export your catalog to marketplaces, price comparison sites and more with [Lengow](http://www.lengow.fr/).

For this, go to Thelia's back office and Lengow module's configuration. There you will be able to configure your export to Lengow.
Thelia gives to Lengow an URL to enable him to import the products you want to show. However, you can also ask for a manual export by clicking on the corresponding button.


3. Configuring Lengow
---

There are several parameters to configure:

* Delivery price: Delivery costs
* Free delivery price: Minimal price to not pay delivery costs
* Minimum available product sale element: If product sale elements are lower than this number, do not include the product sale element in the export.
* Cache time: Thelia keeps the export in its cache for a given amount of time. Write it here in seconds.
* Attributes, categories, brands and products that you want to exclude from the export. You can use a smart search to write your products.
* Maximal number of results to return while looking for products to exclude: When you use the smart search for writing your products to exclude, it returns you a limited
amount of products. Precise this number here. Write 0 if you do not want a limit.


***

<a name="francais"></a>

Module Lengow v1.0
===

* [Anglais](#english).
* [Français](#francais).
   
Auteurs : [Benjamin Perche](benjamin@thelia.net) et [Romain Ducher](rducher@openstudio.fr)


1. Installation
---

Installez-le comme un module Thelia classique en téléchargeant son archive ZIP et en l'extrayant dans ```thelia/local/modules```, ou via le backoffice dans ```/admin/modules```.
ou en l'ajoutant dans les composants requis via Composer :

```json

"require": {
    "thelia/lengow-module": "~1.0"
}
```

2. Utilisation
---

Ce module vous permet d'exporter votre catalogue vers des sites de ventes en ligne et des comparateurs de prix avec [Lengow](http://www.lengow.fr/).

Pour cela, allez dans le back office de Thelia, sur la page de configuration du module Lengow. Vous y trouverez de quoi configurer votre export Lengow.
Thelia met à disposition de Lengow une URL qu'il peut utiliser pour importer les produits que vous voulez montrer. Cependant vous pouvez toujours demander un export manuel
des données en cliquant sur le bouton correspondant dans la page de configuration du module.


3. Configurer Lengow
---

Il y a plusieurs paramètres à configurer pour vos exports Lengow :

* Frais de ports
* Prix minimal au delà duque les frais de ports peuvent être offerts.
* Nombre de déclinaisons minimales du produit minimal. En deça de ce nombre le produit ne doit pas apparaître dans les exports.
* Thelia garde le fichier d'exports dans son cache pendant un certain temps. Précisez-le ici.
* Attributs, catégories, marques et produits spécifiques à exclure de l'export. Un filtre de recherche est à votre disposition pour les références de produits.
* Quand vous utilisez le filtre de recherche pour les références de produits, celui-ci retourne un nombre limité de produits. Précisez ce nombre ici. Écrivez 0 si vous ne voulez pas de limites.
