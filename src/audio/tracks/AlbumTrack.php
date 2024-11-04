<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\render as r;
use iutnc\deefy\audio\lists as l;
use iutnc\deefy\audio\tracks as t;
use iutnc\deefy\exception as e;
class AlbumTrack extends t\AudioTrack{
    private string $artiste;
    private string $album;
    private int $annee;
    private int $piste;


    public function __get(string $attrname):mixed{
        if(property_exists($this,$attrname))
            return $this->$attrname;
        throw new e\InvalidPropertyNameException("invalid property : '$attrname'");
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAlbum(string $album): void
    {
        $this->album = $album;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }

    public function setPiste(int $piste): void
    {
        $this->piste = $piste;
    }



    public function __construct(string $titre, string $audioFile, int $piste, string $alb){
        parent::__construct($titre, $audioFile);
        $this->piste = $piste;
        $this->album = $alb;
    }
}