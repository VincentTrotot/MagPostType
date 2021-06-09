<?php

namespace VincentTrotot\Mag;

class Mag extends \Timber\Post
{
    public $name;
    public $url;

    public function __construct($pid = null)
    {
        parent::__construct($pid);
        $this->name = $this->meta('name');
        $this->url = $this->meta('url');
    }

    public function getLastMags($nb = 1)
    {
        return new \Timber\PostQuery([
            'post_type' => 'vt_kiosque',
            'posts_per_page' => $nb
        ], Mag::class);
    }
}
