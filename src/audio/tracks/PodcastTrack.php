<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack{
    private string $auteur;
    private string $date;

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function __get(string $attrname):mixed{
        if(property_exists($this,$attrname))
            return $this->$attrname;
            throw new InvalidPropertyNameException("invalid property : '$attrname'");
    }


    public function __construct(string $titre, string $audioFile){
        parent::__construct($titre, $audioFile);
    }
}