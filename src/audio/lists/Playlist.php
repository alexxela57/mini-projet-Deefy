<?php

namespace iutnc\deefy\audio\lists;
use iutnc\deefy\render as r;
use iutnc\deefy\audio\lists as l;
use iutnc\deefy\audio\tracks as t;
class Playlist extends AudioList {

    public function ajouterPiste(t\AudioTrack $piste): void {
        if (!in_array($piste, $this->pistes, true)) {
            $this->pistes[] = $piste;
            $this->nbpistes++;
            $this->duree += $piste->__get("duree");
        }
    }

    public function supprimerPiste(int $indice): void {
        if (isset($this->pistes[$indice])) {
            $this->duree -= $this->pistes[$indice]->__get("duree");
            unset($this->pistes[$indice]);
            $this->pistes = array_values($this->pistes); // Ré-indexe le tableau
            $this->nbpistes--;
        }
    }

    public function ajouterListePistes(array $nouvellesPistes): void {
        foreach ($nouvellesPistes as $piste) {
            if ($piste instanceof AudioTrack) {
                $this->ajouterPiste($piste); // Utilise `ajouterPiste` pour éviter les doublons
            }
        }
    }
}
