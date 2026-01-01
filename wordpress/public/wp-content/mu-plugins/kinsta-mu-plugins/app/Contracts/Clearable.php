<?php

namespace Kinsta\KMP\Contracts;

interface Clearable extends Nameable
{
	/**
	 * Clear (purge) something.
	 *
	 * This is usually cache, but can also be anything that needs to be cleared.
	 */
	public function clear(): void;

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
