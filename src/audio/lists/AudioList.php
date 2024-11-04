<?php

namespace iutnc\deefy\audio\lists;
use iutnc\deefy\exception as e;

class AudioList {
    protected string $nom;
    protected int $nbpistes;
    protected int $duree;
    protected array $pistes; // Doit être protégée pour être accessible aux sous-classes

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        throw new e\InvalidPropertyNameException("Propriété inconnue : '$attr'");
    }

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbpistes = count($pistes);
        $this->duree = 0;

        // Calculer la durée totale
        foreach ($pistes as $piste) {
            if ($piste instanceof AudioTrack) {
                $this->duree += $piste->__get("duree");
            }
        }
    }
}
