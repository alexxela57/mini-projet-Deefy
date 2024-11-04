<?php

namespace iutnc\deefy\render;

use iutnc\deefy\render as r;
use iutnc\deefy\audio\lists as l;
use iutnc\deefy\audio\tracks as t;
class AudioTrackRenderer implements r\Renderer
{
    public t\AudioTrack $audio;

    function __construct(t\AudioTrack $a)
    {
        $this->audio = $a;
    }

    function render(int $selector): string
    {
        $res="";
        $a=$this->audio;
        switch ($selector) {
            case self::COMPACT :
                $res = "
                <ul>
                <div>
                <li>
                {$a->__get("titre")}
                <audio controls><source src='.{$this->audio->__get("file")}'></audio>
                </li>
                </div>
                </ul>";
                break;
            case self::LONG :
                $res ="";
                break;
            default :
                $res = "veuillez saisir un entier compris entre 1 et 2.";
        }
        return $res;
    }

}