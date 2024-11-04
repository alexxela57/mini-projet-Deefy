<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks as t;

class PodcastTrackRenderer extends AudioTrackRenderer {

    public function __construct(t\PodcastTrack $podcast) {
        parent::__construct($podcast);
    }

    public function render(int $selector) : string {
        // Utilise la propriété héritée `audio` pour accéder aux informations du podcast
        $pod = $this->audio;

        if ($selector === Renderer::COMPACT) {
            return "<ul><div><li>{$pod->__get('titre')}</li><li><audio controls><source src='{$pod->__get('file')}' type='audio/ogg'></audio></li></div></ul>";
        } else {
            return "
            <ul><div>
                <li>Titre : {$pod->__get('titre')}</li>
                <li>Auteur : {$pod->__get('auteur')}</li>
                <li>Genre : {$pod->__get('genre')}</li>
                <li>Durée : {$pod->__get('duree')} s</li>
                <li>Date : {$pod->__get('date')}</li>
                <li><audio controls><source src='{$pod->__get('file')}' type='audio/ogg'></audio></li>
            </div></ul>";
        }
    }
}
