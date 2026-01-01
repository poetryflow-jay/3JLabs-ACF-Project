<?php

namespace Kinsta\KMP\Contracts;

interface Autopurgable extends Nameable, Describable, Clearable, Hookable
{
    /**
     * Whether should perform clearing (purging).
     *
     * @return bool True if enabled, false otherwise.
     */
    public function isEnabled(): bool;

    /**
     * Whether this feature is supported in the current site/setup.
     *
     * @return bool True if supported, false otherwise.
     */
    public function isSupported(): bool;
}
