<?php

namespace douggonsouza\language;

interface languageInterface
{
    /**
     * Adiciona novo local de etiquetas para a linguagem
     *
     * @param  string $local
     * @param  string $language
     * @return bool
     */
    public function new(string $local, string $language = 'pt-br');

    /**
     * Expõe os valores para a etiqueta
     *
     * @param  mixed $label
     * @return void
     */
    public function get(string $label);
}
?>