<?php

namespace PhpDocBlockChecker\Test\Unit\DocblockParser;

use PhpDocBlockChecker\DocblockParser\DocblockParser;
use PhpDocBlockChecker\DocblockParser\TagCollection;

class DocblockParserTest extends \PHPUnit\Framework\TestCase
{
    public function testParseComment()
    {
        $comment = <<<EOF
/**
  * @author me
  * @param int \$foo some int
  * @param string \$bar some string
  * @param \DateTimeImmutable \$baz some date
  * @param string ...$args
  * @return stdClass some class
  */
EOF;

        $docblockParser = new DocblockParser();
        $result = $docblockParser->parseComment($comment);

        $this->assertInstanceOf(TagCollection::class, $result);

        $this->assertTrue($result->hasTag('author'));
        $this->assertTrue($result->hasTag('param'));
        $this->assertTrue($result->hasTag('return'));

        $returnTags = $result->getReturnTags();
        $this->assertCount(1, $returnTags);

        $this->assertEquals('some class', $returnTags[0]->getDesc());
        $this->assertEquals('stdClass', $returnTags[0]->getType());

        $expected = [
            [
                'var' => '$foo',
                'type' => 'int',
                'desc' => 'some int',
                'variadic' => false,
            ],
            [
                'var' => '$bar',
                'type' => 'string',
                'desc' => 'some string',
                'variadic' => false,
            ],
            [
                'var' => '$baz',
                'type' => '\DateTimeImmutable',
                'desc' => 'some date',
                'variadic' => false,
            ],
            [
                'var' => '$args',
                'type' => 'string',
                'desc' => 'some date',
                'variadic' => true,
            ],

        ];

        foreach ($result->getParamTags() as $key => $paramTag) {
            $this->assertEquals($expected[$key]['var'], $paramTag->getVar());
            $this->assertEquals($expected[$key]['type'], $paramTag->getType());
            $this->assertEquals($expected[$key]['desc'], $paramTag->getDesc());
            $this->assertEquals($expected[$key]['variadic'], $paramTag->isVariadic());
        }
    }
}
