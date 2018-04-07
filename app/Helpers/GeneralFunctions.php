<?php
/**
 * Created by PhpStorm.
 * User: REDSignal
 * Date: 3/22/2018
 * Time: 3:49 PM
 */

namespace App\Helpers;

class GeneralFunctions
{
    static public function cleanNumber($phoneNumber) {
        $phoneNumber = str_replace(' ', '', $phoneNumber); // Replaces all spaces with hyphens.
        $phoneNumber = str_replace('-', '', $phoneNumber); // Replaces all spaces with hyphens.

        return self::cleanCountryCodes(preg_replace('/[^0-9\-]/', '', $phoneNumber)); // Removes special chars.
    }

    static private function cleanCountryCodes($phoneNumber) {
        // Remove Zero Leading
        if($phoneNumber[0] == '0') {
            $phoneNumber = substr($phoneNumber,1);
        }
        // Remove Coutnry
        if($phoneNumber[0] == '9' && $phoneNumber[1] == '2') {
            $phoneNumber = substr($phoneNumber,2);
        }
        // Remove Zero Leading
        if($phoneNumber[0] == '0') {
            $phoneNumber = substr($phoneNumber,1);
        }

        return $phoneNumber;
    }

    static function prepareNumber($phoneNumber) {
        return '92' . $phoneNumber;
    }

    static function prepareNumber4Call($phoneNumber) {
        return '+92' . $phoneNumber;
    }

}