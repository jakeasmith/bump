<?php

namespace Bump\Version;

use Bump\Version;
use PHPUnit_Framework_TestCase;

class SemVerTest extends PHPUnit_Framework_TestCase
{
    public function testValuesThatDoNotMatchSemVerFormatThrowException()
    {
        $version = new SemVer('hello.world');
        $this->assertTrue($version->exclude());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBumpThrowsAnExceptionWhenSuppliedInvalidTouple()
    {
        $version = new SemVer('1.0.1');
        $version->bump('your-face');
    }

    /**
     * @param string $touple           The touple to bump, can either be patch, minor, or major
     * @param string $starting_version The starting version
     * @param string $expected_version The expected final version
     *
     * @dataProvider bumpDataProvider
     */
    public function testBumpIncreasesTouplesCorrectly($touple, $starting_version, $expected_version)
    {
        $expected = new SemVer($expected_version);
        $starting = new SemVer($starting_version);

        $this->assertEquals($expected, $starting->bump($touple));
    }

    public function bumpDataProvider()
    {
        return [
            'patch' => ['patch', '1.5.1', '1.5.2'],
            'minor' => ['minor', '1.5.2', '1.6.0'],
            'major' => ['major', '1.5.2', '2.0.0'],
        ];
    }

    public function testGetTag()
    {
        $version = new SemVer('123.456.789');
        $this->assertEquals('123.456.789', $version->getTag());
    }

    /**
     * @param Semver $instance   The object compare is being called on
     * @param Semver $comparator The object being passed into the comparison
     * @param int    $expected   either 1, 0, or -1
     *
     * @dataProvider comparisonDataProvider
     */
    public function testCompare(Semver $instance, Semver $comparator, $expected)
    {
        $this->assertEquals($expected, $instance->compare($comparator));
    }

    public function comparisonDataProvider()
    {
        return [
            'Major Less Than'    => [new SemVer('0.1.1'), new SemVer('1.0.0'), Version::LESSER],
            'Major Greater Than' => [new SemVer('2.1.1'), new SemVer('1.0.0'), Version::GREATER],

            'Minor Less Than'    => [new SemVer('0.1.1'), new SemVer('0.2.0'), Version::LESSER],
            'Minor Greater Than' => [new SemVer('2.1.1'), new SemVer('2.0.0'), Version::GREATER],

            'Patch Less Than'    => [new SemVer('0.0.1'), new SemVer('0.0.3'), Version::LESSER],
            'Patch Greater Than' => [new SemVer('2.1.1'), new SemVer('2.1.0'), Version::GREATER],

            'Patch Greater Than' => [new SemVer('2.1.1'), new SemVer('2.1.1'), Version::EQUAL],
        ];
    }
}
