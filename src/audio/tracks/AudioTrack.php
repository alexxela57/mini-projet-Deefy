<?php

namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception as e;

class AudioTrack {
    protected string $titre;
    protected string $genre;
    protected int $duree;
    protected string $file;


    public function setGenre(string $g):void{
        $this->genre = $g;
    }
    public function setDuree(int $d):void{
        if($d>=0)
            $this->duree = $d;
        else throw new e\InvalidPropertyValueException("invalid value for length");
    }

    public function __construct(string $titre, string $audioFile){
        $this->titre=$titre;
        $this->file=$audioFile;
    }

    public function __toString():string{
        return json_encode($this);
    }

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        throw new e\InvalidPropertyNameException("Propriété inconnue : '$attr'");
    }
}