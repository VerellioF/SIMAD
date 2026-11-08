<?php

declare(strict_types=1);

namespace Illuminate\Support;

if (! \function_exists('Illuminate\Support\extension_loaded')) {
    function extension_loaded(string $extension): bool
    {
        if ($extension === 'intl') {
            return true;
        }

        return \extension_loaded($extension);
    }
}
