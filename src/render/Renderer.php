<?php

namespace iutnc\deefy\render;

interface Renderer{
    const LONG=2;
    const COMPACT=1;

    public function render(int $selector):string;
}