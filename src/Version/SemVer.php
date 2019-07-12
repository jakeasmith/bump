<?php

namespace Bump\Version;

use Bump\Version;

class SemVer implements Version
{
    /** @var string The major version */
    private $major;

    /** @var string The minor version */
    private $minor;

    /** @var string The patch version */
    private $patch;

    /** @var bool Determine if the tag is in a valid format. */
    private $is_valid_format = false;

    public function __construct($tag)
    {
        if ($this->init($tag)) {
            $this->is_valid_format = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function exclude()
    {
        return !$this->is_valid_format;
    }

    /**
     * @inheritdoc
     */
    public function bump($touple)
    {
        $touple_map = [
            'patch' => 'bumpPatch',
            'minor' => 'bumpMinor',
            'major' => 'bumpMajor'
        ];

        if (!isset($touple_map[$touple])) {
            throw new \InvalidArgumentException($touple . ' is not an allowed touple');
        }

        return $this->{$touple_map[$touple]}();
    }

    /**
     * Generates the patch bump for the given tag.
     *
     * @return self
     */
    protected function bumpPatch()
    {
        $version = clone $this;
        $version->patch++;
        return $version;
    }

    /**
     * Generates the minor bump for the given tag.
     *
     * @return string
     */
    protected function bumpMinor()
    {
        $version = clone $this;
        $version->minor++;
        $version->patch = 0;
        return $version;
    }

    /**
     * Generates the major bump for the given tag.
     *
     * @return string
     */
    protected function bumpMajor()
    {
        $version = clone $this;
        $version->major++;
        $version->patch = 0;
        $version->minor = 0;
        return $version;
    }

    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @inheritdoc
     */
    public function compare(Version $version)
    {
        $major = $this->spaceShip($this->major, $version->major);
        if ($major !== VERSION::EQUAL) {
            return $major;
        }

        $minor = $this->spaceShip($this->minor, $version->minor);
        if ($minor !== Version::EQUAL) {
            return $minor;
        }

        return $this->spaceShip($this->patch, $version->patch);
    }

    /**
     * Initializes the major, minor, and patch properties.
     *
     * @param string $tag
     *
     * @return bool True if the raw_tag is formatted correctly, false if the format is incorrect.
     */
    protected function init($tag)
    {
        $parts = explode('.', $tag);

        if (count($parts) !== 3) {
            return false;
        }

        list($this->major, $this->minor, $this->patch) = $parts;

        return true;
    }

    /**
     * Performs the same action as `<=>` operation in php 7.
     *
     * @param string|int $instance
     * @param string|int $parameter
     *
     * @return int -1 when instance is less than parameter, 0 if they're equal and 1 if instance is greater than
     *              parameter.
     */
    private function spaceShip($instance, $parameter)
    {
        if ($instance == $parameter) {
            return 0;
        }

        return $instance > $parameter ? 1 : -1;
    }
}
