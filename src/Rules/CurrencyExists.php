<?php

namespace Gamebetr\Api\Rules;

use Carbon\Carbon;
use Exception;
use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class CurrencyExists implements Rule
{
    /**
     * Passes.
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Cache::remember('currency-exists-'.$value, Carbon::now()->addHour(), function () use ($value) {
            try {
                Paybetr::getCurrency($value);
                return true;
            } catch (Exception $e) {
                return false;
            }
        });
    }

    /**
     * Message.
     * @return string
     */
    public function message()
    {
        return ':attribute does not exist';
    }
}
