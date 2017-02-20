<?php

namespace Bump;

class Compare
{
    /** @var callable */
    private $tag_factory;

    public function __construct(callable $tag_factory)
    {
        $this->tag_factory = $tag_factory;
    }

    /**
     * @param string[] $repository_tags The list of tags from your source code repository.
     * @param string   $starting_tag    The earliest possible tag for your tagging system, 0.0.0 for SemVer.
     *
     * @return Version The current tag.
     */
    public function getLatestTag(array $repository_tags, $starting_tag = '0.0.0')
    {
        $current = call_user_func($this->tag_factory, $starting_tag);
        foreach ($repository_tags as $repository_tag) {
            /** @var Version $tag */
            $tag = call_user_func($this->tag_factory, $repository_tag);

            if ($tag->exclude()) {
                continue;
            }

            if ($current->compare($tag) === Version::LESSER) {
                $current = $tag;
            }
        }

        return $current;
    }
}
