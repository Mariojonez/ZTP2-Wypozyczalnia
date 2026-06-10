<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Service\TagServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TagsDataTransformerTest extends TestCase
{
    public function testTransformReturnsEmptyStringForEmptyCollection(): void
    {
        $transformer = new TagsDataTransformer(
            $this->createStub(TagServiceInterface::class)
        );

        $tags = new ArrayCollection();

        $result = $transformer->transform($tags);

        $this->assertSame('', $result);
    }

    public function testTransformReturnsCommaSeparatedTitles(): void
    {
        $transformer = new TagsDataTransformer(
            $this->createStub(TagServiceInterface::class)
        );

        $tag1 = new Tag();
        $tag1->setTitle('PHP');

        $tag2 = new Tag();
        $tag2->setTitle('Symfony');

        $tags = new ArrayCollection([$tag1, $tag2]);

        $result = $transformer->transform($tags);

        $this->assertSame('PHP, Symfony', $result);
    }

    public function testReverseTransformReturnsEmptyArrayForEmptyString(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $tagService
            ->expects($this->never())
            ->method('findOneByTitle');

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('');

        $this->assertSame([], $result);
    }

    public function testReverseTransformReturnsExistingTags(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $phpTag = new Tag();
        $phpTag->setTitle('PHP');

        $symfonyTag = new Tag();
        $symfonyTag->setTitle('Symfony');

        $tagService
            ->expects($this->exactly(2))
            ->method('findOneByTitle')
            ->willReturnMap([
                ['php', $phpTag],
                ['symfony', $symfonyTag],
            ]);

        $tagService
            ->expects($this->never())
            ->method('save');

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('php,symfony');

        $this->assertCount(2, $result);
        $this->assertSame($phpTag, $result[0]);
        $this->assertSame($symfonyTag, $result[1]);
    }

    public function testReverseTransformCreatesNewTags(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $tagService
            ->expects($this->exactly(2))
            ->method('findOneByTitle')
            ->willReturn(null);

        $tagService
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->isInstanceOf(Tag::class));

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('PHP,Symfony');

        $this->assertCount(2, $result);

        $this->assertInstanceOf(Tag::class, $result[0]);
        $this->assertSame('PHP', $result[0]->getTitle());

        $this->assertInstanceOf(Tag::class, $result[1]);
        $this->assertSame('Symfony', $result[1]->getTitle());
    }

    public function testReverseTransformIgnoresEmptyTagTitles(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $tag = new Tag();
        $tag->setTitle('PHP');

        $tagService
            ->expects($this->once())
            ->method('findOneByTitle')
            ->with('php')
            ->willReturn($tag);

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('php, ,   ');

        $this->assertCount(1, $result);
        $this->assertSame($tag, $result[0]);
    }
}
