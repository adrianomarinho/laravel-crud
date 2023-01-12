<?php

namespace App\Helpers;

/*
 * Classe para manipular validações de dados
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @date April 20, 2016
 */

class Validations
{

    /**
     * Valida CPF
     * @param  string $cpf CPF
     * @return bool
     */
    public static function cpf($cpf)
    {
        $d1 = 0;
        $d2 = 0;
        $cpf = preg_replace("/[^0-9]/", "", (string)$cpf);
        $ignore_list = [
            '00000000000',
            '01234567890',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999',
        ];

        if (strlen($cpf) != 11 || in_array($cpf, $ignore_list)) {
            return false;
        } else {
            for ($i = 0; $i < 9; $i++) {
                $d1 += $cpf[$i] * (10 - $i);
            }
            $r1 = $d1 % 11;
            $d1 = ($r1 > 1) ? (11 - $r1) : 0;

            for ($i = 0; $i < 9; $i++) {
                $d2 += $cpf[$i] * (11 - $i);
            }
            $r2 = ($d2 + ($d1 * 2)) % 11;
            $d2 = ($r2 > 1) ? (11 - $r2) : 0;

            return (substr($cpf, -2) == $d1 . $d2) ? true : false;
        }
    }

    /**
     * Valida CNPJ
     * @param  string $cnpj CNPJ
     * @return bool
     */
    public static function cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14) {
            return false;
        }
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
    }


    public static function format_mask($value, $format_mask)
    {
        $value = Self::sanitizeValueForMask($value);
        $result = '';
        $position = 0;

        for ($i = 0; $i <= strlen($format_mask) - 1; $i++) {

            if ($format_mask[$i] == '#') {

                if (isset($value[$position]))
                    $result .= $value[$position++];
            } else {

                if (isset($format_mask[$i]))
                    $result .= $format_mask[$i];
            }
        }

        return $result;
    }

    public static function sanitizeValueForMask($string)
    {
        $string = trim($string);
        $string = str_replace(".", "", $string);
        $string = str_replace(",", "", $string);
        $string = str_replace(";", "", $string);
        $string = str_replace("-", "", $string);
        $string = str_replace("_", "", $string);
        $string = str_replace("/", "", $string);
        $string = str_replace(" ", "", $string);

        return $string;
    }

    public static function formatMoney($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public static function formatCpfCnpj($value)
    {
        if (strlen($value) <= 11) {
            return Self::format_mask($value, '###.###.###-##');
        } else {
            return Self::format_mask($value, '##.###.###/####-##');
        }
    }

    public static function formatIdOrdem($value)
    {
        $idOrdem = str_pad($value, 7, 0, STR_PAD_LEFT);
        return Self::format_mask($idOrdem, "#######");
    }

    public static function sanitizeFromInt($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    public static function cleanString($text)
    {
        $utf8 = array(
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u' => 'A',
            '/[ÍÌÎÏ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/–/' => '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u' => ' ', // Literally a single quote
            '/[“”«»„]/u' => ' ', // Double quote
            '/ /' => ' ', // nonbreaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }

    public static function dateUsToBR($string)
    {
        $date = explode('-', $string);
        return $date[2] . '/' . $date[1] . '/' . $date[0];
    }

    public static function dateBrToUs($string)
    {
        $date = explode('/', $string);
        return $date[2] . '-' . $date[1] . '-' . $date[0];
    }

    public static function dateTimeBrToUs($string)
    {
        $dateTime = explode(' ', $string);
        $date = explode('/', $dateTime[0]);
        return $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $dateTime[1];
    }

    public static function dateTimeUsToBr($string)
    {
        $dateTime = explode(' ', $string);
        $date = explode('-', $dateTime[0]);
        return $date[2] . '/' . $date[1] . '/' . $date[0] . ' ' . $dateTime[1];
    }

}
