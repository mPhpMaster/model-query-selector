<?php
/*
 * Copyright © 2024. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */


if (!function_exists('mqs')) {
    /**
     * @param string|\Illuminate\Database\Eloquent\Model $class
     * @param string|null                                $alias
     *
     * @return \MPhpMaster\ModelQuerySelector\ModelQuerySelector
     */
    function mqs(string|\Illuminate\Database\Eloquent\Model $class = "", ?string $alias = null): \MPhpMaster\ModelQuerySelector\ModelQuerySelector
    {
        return \MPhpMaster\ModelQuerySelector\ModelQuerySelector::make($class, $alias);
    }
}