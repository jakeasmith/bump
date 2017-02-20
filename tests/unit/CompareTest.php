<?php

namespace Bump;

use Bump\Version\SemVer;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class CompareTest extends PHPUnit_Framework_TestCase
{
    /** @var Compare */
    private $compare;

    /** @var TagFactory|MockObject */
    private $tag_factory;

    public function setUp()
    {
        $this->tag_factory = $this->getMock(TagFactory::class);
        $this->compare = new Compare($this->tag_factory);
    }

    public function testWithNoTagsReturnsTheCurrentVersion()
    {
        $expected = $this->getMock(Version::class);
        $this->tag_factory->expects($this->once())
            ->method('__invoke')
            ->with('0.0.0')
            ->willReturn($expected);

        $this->assertSame($expected, $this->compare->getLatestTag([]));
    }

    public function testExcludedTagsAreNotIncludedInComparison()
    {
        $base_tag = $this->getMock(Version::class);
        $excluded_tag = $this->getMock(Version::class);

        $this->tag_factory->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(['0.0.0'], ['irregular-tag'])
            ->willReturnOnConsecutiveCalls($base_tag, $excluded_tag);

        $base_tag->expects($this->never())->method('compare');
        $excluded_tag->expects($this->once())
            ->method('exclude')
            ->willReturn(true);

        $this->assertSame($base_tag, $this->compare->getLatestTag(['irregular-tag']));
    }

    public function testCompare()
    {
        $base_tag = $this->getMock(Version::class);
        $lower_tag = $this->getMock(Version::class);
        $upper_tag = $this->getMock(Version::class);


        $this->tag_factory->expects($this->exactly(3))
            ->method('__invoke')
            ->withConsecutive(['0.0.0'], ['1.0.1'], ['1.0.2'])
            ->willReturnOnConsecutiveCalls($base_tag, $lower_tag, $upper_tag);

        $base_tag->expects($this->once())
            ->method('compare')
            ->with($lower_tag)
            ->willReturn(-1);

        $lower_tag->expects($this->once())
            ->method('compare')
            ->with($upper_tag)
            ->willReturn(-1);

        $this->assertSame($upper_tag, $this->compare->getLatestTag(['1.0.1', '1.0.2']));
    }
}

class TagFactory
{
    public function __invoke($tag) {}
}
