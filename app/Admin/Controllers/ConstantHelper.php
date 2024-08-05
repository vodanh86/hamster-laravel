<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;

class ConstantHelper
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_IN_ACTIVE = 0;
    public const USER_FRIEND_TYPE_FRIEND = 1;

    public static function dateFormatter($dateIn)
    {
        if ($dateIn === null) {
            return "";
        }

        $carbonDateIn = Carbon::parse($dateIn)
            ->setTimezone('Asia/Bangkok');
        return $carbonDateIn->format('d/m/Y - H:i:s');
    }

    public static function dayFormatter($dayIn)
    {
        if ($dayIn === null) {
            return "";
        }

        $carbonDayIn = Carbon::parse($dayIn)->setTimezone('Asia/Bangkok');
        return $carbonDayIn->format('d/m/Y');
    }

    public static function dayHightLightFormatter($dayIn, $type)
    {
        if ($dayIn === null) {
            return "";
        }
        $carbonDayIn = Carbon::parse($dayIn)->setTimezone('Asia/Bangkok');
        $today = Carbon::now('Asia/Bangkok');
        if ($type == "nextDate") {
            if ($carbonDayIn->greaterThan($today)) {
                return "<span class='label label-primary'>{$carbonDayIn->format('d/m/Y')}</span>";
            } elseif ($carbonDayIn->lessThan($today->subDays(3))) {
                return "<span class='label label-danger'>{$carbonDayIn->format('d/m/Y')}</span>";
            } elseif ($carbonDayIn->lessThan($today->subDays(2))) {
                return "<span class='label label-warning'>{$carbonDayIn->format('d/m/Y')}</span>";
            } else {
                return "<span class='label label-warning'>{$carbonDayIn->format('d/m/Y')}</span>";
            }
        } else if ($type == "valueDate") {
            if ($carbonDayIn->lessThan($today)) {
                return "<span class='label label-primary'>{$carbonDayIn->format('d/m/Y')}</span>";
            } else {
                return "<span class='label label-warning'>{$carbonDayIn->format('d/m/Y')}</span>";
            }
        } else {
            return $carbonDayIn->format('d/m/Y');
        }
    }

    public static function moneyFormatter($money)
    {
        return number_format($money, 0, ',', ',') . " VND";
    }

    public static function transactionDetailRecordStatus($value)
    {
        if (array_key_exists($value, Constant::RECORD_STATUS)) {
            return Constant::RECORD_STATUS[$value];
        } else {
            return '';
        }
    }

    public static function transactionGridRecordStatus($value)
    {
        if ($value === 0) {
            return "<span class='label label-warning'>Lưu nháp</span>";
        } elseif ($value === 1) {
            return "<span class='label label-success'>Hiệu lực</span>";
        } elseif ($value === 2) {
            return "<span class='label label-danger'>Huỷ</span>";
        } elseif ($value === 3) {
            return "<span class='label label-default'>Lịch sử</span>";
        }
    }
}
