<?php

namespace Kinsta\KMP;

/**
 * Check whether the global autopurge is enabled.
 */
function is_autopurge_enabled(): bool
{
    if (defined( 'KINSTAMU_DISABLE_AUTOPURGE' ) && KINSTAMU_DISABLE_AUTOPURGE === true) {
        return false;
    }

    $status = get_option('kinsta-autopurge-status', null);

    return $status === 'enabled' || $status === null;
}
