# MagPostType

MagPostType est un package permettant d'ajouter un Custom Post Type `vt_kiosque` à un theme WordPress.  
Ce Custom Post Type ajoute deux metadata : un nom et une url pour le fichier PDF.

La classe `VincentTrotot\Mag\MagPostType` paramètre le Custom Post Type tandis que la classe `VincentTrotot\Mag\Mag` est une espèce de wrapper du Post (la classe hérite de la classe `Timber\TimberPost`).

## Installation

```bash
composer require vtrotot/mag-post-type
```

## Utilisation

Votre theme doit instancier la classe `MagPostType`

```php
new VincentTrotot\Mag\MagPostType();
```

Vous pouvez ensuite récupérer un Post de type Mag:

```php
$post = new VincentTrotot\Mag\Mag();
```

Ou récupérer plusieurs posts avec :

```php
$args = [
    'post_type' => 'vt_kiosque',
    ...
];
$posts = new Timber\TimberRequest($args, VincentTrotot\Mag\Mag::class);
```
