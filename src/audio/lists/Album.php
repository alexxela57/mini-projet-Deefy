<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\render as r;
use iutnc\deefy\audio\lists as l;
use iutnc\deefy\audio\tracks as t;

class Album extends l\AudioList {
    private string $artiste;
    private string $dateSortie;

    public function __construct(string $nom, array $pistes, string $artiste = '', string $dateSortie = '') {
        parent::__construct($nom, $pistes);
        $this->artiste = $artiste;
        $this->dateSortie = $dateSortie;
    }

    public function setArtiste(string $artiste): void {
        $this->artiste = $artiste;
    }

    public function setDateSortie(string $dateSortie): void {
        $this->dateSortie = $dateSortie;
    }

    public function __get(string $attr): mixed {
        if ($attr === 'artiste' || $attr === 'dateSortie') {
            return $this->$attr;
        }
        return parent::__get($attr);
    }
}
