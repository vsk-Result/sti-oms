<?php

namespace App\Services;

class OrganizationValidator
{
    public function validate(?string $inn, ?string $kpp = null): array
    {
        $errors = [];

        $inn = $this->normalize($inn);
        $kpp = $this->normalize($kpp);

        // --- ИНН ---
        if (!$inn) {
//            $errors['inn'][] = 'ИНН обязателен';
        } elseif (!$this->isValidInn($inn)) {
            $errors['inn'][] = 'Некорректный ИНН';
        }

        // --- Тип ---
        $type = $this->detectType($inn);

        // --- КПП ---
        if ($type === 'company') {
            if (!$kpp) {
//                $errors['kpp'][] = 'КПП обязателен для юрлица.';
            } elseif (!$this->isValidKpp($kpp)) {
                $errors['kpp'][] = 'Некорректный КПП';
            }
        }

//        if ($type === 'ip' && $kpp) {
//            $errors['kpp'][] = 'У ИП не должно быть КПП.';
//        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'type' => $type, // ip | company | unknown
            'inn' => $inn,
            'kpp' => $kpp,
        ];
    }

    private function normalize(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        return preg_replace('/\D/', '', $value);
    }

    public function detectType(?string $inn): string
    {
        if (!$inn) {
            return 'unknown';
        }

        return match (strlen($inn)) {
            12 => 'ip',
            10 => 'company',
            default => 'unknown',
        };
    }

    public function isValidInn(string $inn): bool
    {
        if (!preg_match('/^\d+$/', $inn)) {
            return false;
        }

        return match (strlen($inn)) {
            10 => $this->checkInn10($inn),
            12 => $this->checkInn12($inn),
            default => false,
        };
    }

    public function isValidKpp(string $kpp): bool
    {
        return preg_match('/^\d{9}$/', $kpp) === 1;
    }

    // ------------------------
    // Проверки контрольных сумм
    // ------------------------

    private function checkInn10(string $inn): bool
    {
        $coeffs = [2, 4, 10, 3, 5, 9, 4, 6, 8];

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $coeffs[$i] * $inn[$i];
        }

        $control = ($sum % 11) % 10;

        return $control == $inn[9];
    }

    private function checkInn12(string $inn): bool
    {
        $coeffs1 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $coeffs2 = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

        $sum1 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum1 += $coeffs1[$i] * $inn[$i];
        }

        $control1 = ($sum1 % 11) % 10;

        if ($control1 != $inn[10]) {
            return false;
        }

        $sum2 = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum2 += $coeffs2[$i] * $inn[$i];
        }

        $control2 = ($sum2 % 11) % 10;

        return $control2 == $inn[11];
    }
}