<?php

namespace iutnc\deefy\render;

use iutnc\deefy\render as r;
use iutnc\deefy\audio\lists as l;
use iutnc\deefy\audio\tracks as t;
class AudioListRenderer implements r\Renderer {

    private l\AudioList $audiolist;

    public function __construct(l\AudioList $al){
        $this->audiolist = $al;
    }

    public function render(int $selector=1): string {
        $audiolist = $this->audiolist;

        // Titre de la liste
        $str = "<h2>Liste : " . $audiolist->__get("nom") . "</h2>";

        // Affichage du nombre de pistes et de la durée totale
        $str .= "<p>Nombre de pistes : " . $audiolist->__get("nbpistes") . "</p>";
        $str .= "<p>Durée totale : " . $audiolist->__get("duree") . " secondes</p>";

        // Parcours de chaque piste de la liste
        $str .= "<ul>";
        foreach ($audiolist->__get("pistes") as $piste) {
            if ($piste instanceof t\AlbumTrack) {
                $renderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof t\PodcastTrack) {
                $renderer = new PodcastTrackRenderer($piste);
            } else {
                continue; // Skip si le type n'est pas reconnu
            }
            $str .= "<li>" . $renderer->render(1) . "</li>";
        }
        $str .= "</ul>";

        return $str;
    }
}
