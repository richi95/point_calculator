<?php

namespace RedGreenCode\Discounts;

use Spatie\DataTransferObject\DataTransferObject;

class Calculator extends DataTransferObject
{

    public $basePoints = 0;
    public $extraPoints = 0;

    public function baseSubjectsCalculate($data, $mandatory)
    {
        $compulsorySubjects = array('magyar nyelv és irodalom', 'történelem', 'matematika');
        $compulsorySubjectsCount = 0;
        $failedSubjects = '';

        foreach ($data['erettsegi-eredmenyek'] as $d) {

            if (in_array($d['nev'], $compulsorySubjects)) {
                $compulsorySubjectsCount++;
            }

            if ((int)rtrim($d['eredmeny'], '%') < 20) {
                $failedSubjects = $failedSubjects . $d['nev'] . ' ';
                if (strlen($failedSubjects) > 0) {
                    $failedSubjects = $failedSubjects . ',';
                }
            } elseif ($mandatory === $d['nev']) {
                $this->basePoints += (int)rtrim($d['eredmeny'], '%');
            }
        }

        if (strlen($failedSubjects) > 0) {
            return 'hiba, nem lehetséges a pontszámítás a ' . rtrim($failedSubjects, ',') . 'tárgyból elért 20% alatti eredmény miatt';
        }


        if ($compulsorySubjectsCount !== 3) {
            return 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt';
        }

        return $data;
    }

    public function calculate($data, $mandatory, $compulsorySubjects = [])
    {
        $pass = [];

        $data = $this->baseSubjectsCalculate($data, $mandatory);
        if (is_array($data)) {
            foreach ($data['erettsegi-eredmenyek'] as $d) {

                if (in_array($d['nev'], $compulsorySubjects)) {
                    array_push($pass, $d);
                }

                if ($d['tipus'] === 'emelt') {
                    $this->extraPoints += 50;
                }
            }

            if (count($pass) < 1) {
                return 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt';
            }

            $bestSecondarySubjectScore = 0;

            foreach ($pass as $p) {

                if ((int)rtrim($p['eredmeny'], '%') > $bestSecondarySubjectScore) {
                    $bestSecondarySubjectScore = (int)rtrim($p['eredmeny'], '%');
                }
            }

            $this->basePoints += $bestSecondarySubjectScore;
        } else {
            return $data;
        }

        $this->extraPointCalculate($data);

        if ($this->extraPoints > 100) $this->extraPoints = 100;

        return $this->basePoints * 2 + $this->extraPoints . ' (' . $this->basePoints * 2 . ' alappont + ' . $this->extraPoints . ' többletpont)';
    }

    public function extraPointCalculate($data)
    {
        foreach ($data['tobbletpontok'] as $tb) {
            if ($tb['kategoria'] === 'Nyelvvizsga') {
                if ($tb['tipus'] === 'B2') $this->extraPoints += 28;
                if ($tb['tipus'] === 'C1') $this->extraPoints += 40;
            }
        }
    }
}
