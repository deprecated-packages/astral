<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeValue;

use Iterator;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;
use Symplify\PackageBuilder\Php\TypeChecker;

final class NodeValueResolverTest extends TestCase
{
    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

    protected function setUp(): void
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        $parentNodeFinder = new ParentNodeFinder(new TypeChecker());
        $this->nodeValueResolver = new NodeValueResolver($simpleNameResolver, new TypeChecker(), $parentNodeFinder);
    }

    /**
     * @dataProvider provideData()
     * @param mixed $expectedValue
     */
    public function test(Expr $expr, $expectedValue): void
    {
        $resolvedValue = $this->nodeValueResolver->resolve($expr, __FILE__);
        $this->assertSame($expectedValue, $resolvedValue);
    }

    public function provideData(): Iterator
    {
        yield [new String_('value'), 'value'];
    }
}
