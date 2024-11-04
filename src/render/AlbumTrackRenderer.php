<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks as t;

class AlbumTrackRenderer extends AudioTrackRenderer {

    public function __construct(t\AlbumTrack $album) {
        parent::__construct($album);
    }

    public function render(int $selector) : string {
        // Utilise la propriété héritée `audio` pour accéder aux informations de l'album
        $alb = $this->audio;

        if ($selector === Renderer::COMPACT) {
            return "<ul><div><li>{$alb->__get('titre')}</li><li><audio controls><source src='{$alb->__get('file')}' type='audio/ogg'></audio></li></div></ul>";
        } else {
            return "
            <ul><div>
                <li>Titre : {$alb->__get('titre')}</li>
                <li>Artiste : {$alb->__get('artiste')}</li>
                <li>Genre : {$alb->__get('genre')}</li>
                <li>Durée : {$alb->__get('duree')} s</li>
                <li>Année : {$alb->__get('annee')}</li>
                <li>Numéro de piste : {$alb->__get('piste')}</li>
                <li>Nom de l'album : {$alb->__get('album')}</li>
                <li><audio controls><source src='{$alb->__get('file')}' type='audio/ogg'></audio></li>
            </div></ul>";
        }
    }
}
