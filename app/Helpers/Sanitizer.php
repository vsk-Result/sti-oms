<?php

namespace App\Helpers;

class Sanitizer
{
    private mixed $value;
    private string $encoding = 'UTF-8';

    public function set(mixed $value): self
    {
        $this->value = $value;
        return $this->trim()->noLineBrakes()->onlyOneSpace()->removeHTMLTags();
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function trim(): self
    {
        $this->value = trim($this->value);
        return $this;
    }

    public function removeHTMLTags(): self
    {
        $this->value = strip_tags($this->value);
        return $this;
    }

    public function onlyOneSpace(): self
    {
        $this->value = preg_replace('/ {2,}/', ' ', $this->value);
        return $this;
    }

    public function noSpaces(): self
    {
        $this->replace(' ', '');
        return $this;
    }

    public function noLineBrakes(): self
    {
        $this->replace("\n", '');
        return $this;
    }

    public function upperCaseFirstWord(): self
    {
        $this->value = mb_strtoupper(mb_substr($this->value, 0, 1, $this->encoding), $this->encoding) .
            mb_substr($this->value, 1, mb_strlen($this->value, $this->encoding), $this->encoding);
        return $this;
    }

    public function upperCaseAllFirstWords(): self
    {
        $this->lowerCase();
        $this->value = mb_convert_case($this->value, MB_CASE_TITLE, $this->encoding);
        return $this;
    }

    public function replace(string $search, string $replace): self
    {
        $this->value = str_replace($search, $replace, $this->value);
        return $this;
    }

    public function lowerCase(): self
    {
        $this->value = mb_strtolower($this->value, $this->encoding);
        return $this;
    }

    public function toAmount(): self
    {
        $this->replace(',', '.');
        $this->value = (float) preg_replace("/[^-.0-9]/", '', $this->value);
        return $this;
    }

    public function toNumber(): self
    {
        $this->value = preg_replace("/[^0-9]/", '', $this->value);
        return $this;
    }

    public function toCode(): self
    {
        $this->replace(',', '.');
        $this->value = preg_replace("/[^.0-9]/", '', $this->value);
        return $this;
    }

    public function toPhone(): self
    {
        $this->value = preg_replace("/[^+0-9]/", '', $this->value);
        return $this;
    }

    public function toEmail(): self
    {
        $this->value = preg_replace("/[^.@a-zA-Z0-9]/", '', $this->value);
        return $this;
    }
}
