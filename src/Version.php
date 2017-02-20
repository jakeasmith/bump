<?php

namespace Bump;

interface Version
{
    const EQUAL = 0;
    const LESSER = -1;
    const GREATER = 1;

    /**
     * Determine if the tag should be included in the set of computed tags.
     *
     * Check's to make sure that the tag conforms to whatever tagging format you we specify.
     *
     * @return bool
     */
    public function exclude();

    /**
     * @param string $touple The portion of the tag to bump, for sem ver should be major, minor, or patch
     *
     * @return Self A new instance of Version with the correctly bumped touple
     */
    public function bump($touple);

    /**
     * Compares the version against another version.
     *
     * @param Version $version
     *
     * @return int -1 When the instance version is lower than the parameter version, 0 when they're equal, and 1
     *             when the instance version is greater than the parameter version.
     */
    public function compare(Version $version);

    /**
     * @return string THe human readable version of the tag.
     */
    public function getTag();
}
