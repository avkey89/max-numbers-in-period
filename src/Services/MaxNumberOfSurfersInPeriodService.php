<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\VisitRepository;
use DateTimeImmutable;

class MaxNumberOfSurfersInPeriodService
{
    private VisitRepository $visitRepository;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    public function handler($minDate, $maxDate)
    {
        $minDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->prepareInputDate($minDate));
        $maxDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->prepareInputDate($maxDate, 'max'));

        $errorMessage = '';
        if ($minDate === false) {
            $errorMessage = "Неверный формат начальной даты";
        } elseif ($maxDate === false) {
            $errorMessage = "Неверный формат конечной даты";
        } elseif ($maxDate < $minDate) {
            $errorMessage = "Начальная дата не может быть больше конечной даты";
        } elseif ($maxDate->diff($minDate)->format('%Y-%M-%D %H:%I:%S') > '00-00-01 00:00:00') {
            $errorMessage = "Максимальный период равен 1 день";
        } elseif ($maxDate->diff($minDate)->format('%Y-%M-%D %H:%I:%S') == '00-00-00 00:00:00') {
            $errorMessage = "Минимальный период равен 1 секунда";
        }

        if (mb_strlen($errorMessage) > 0) {
            return ["status"=>"error", "message"=>$errorMessage];
        }

        $findResult = $this->visitRepository->findByPeriod($minDate, $maxDate);
        $maxCount = 0;
        if (!empty($findResult)) {
            $enterCount = 0;
            $current = 0;
            $leaveCount = 0;
            foreach($findResult as $i=>$item) {
                $current = $current + (int)$item["movement"];

                if ($current < $leaveCount) {
                    $leaveCount = $current;
                }

                if ($current > $enterCount) {
                    $enterCount = $current;
                }
            }
            $maxCount = $enterCount - $leaveCount;
        }

        return ["status"=>"success", "value"=>$maxCount];
    }

    private function prepareInputDate($inputDate, $type = 'min')
    {
        $checkPartTime = explode(' ', $inputDate);
        if (empty($checkPartTime[1])) {
            $inputDate = $inputDate . ' ' . ($type == 'min' ? '00:00:00' : '23:59:59');
        }
        return $inputDate;
    }
}