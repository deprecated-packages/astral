<?php

declare(strict_types=1);

namespace Symplify\Astral\StaticFactory;

use PhpParser\NodeFinder;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PackageBuilder\Php\TypeChecker;

final class NodeValueResolverStaticFactory
{
    public static function create(): NodeValueResolver
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new SimpleNodeFinder(new TypeChecker(), new NodeFinder());

        return new NodeValueResolver($simpleNameResolver, new TypeChecker(), $simpleNodeFinder);
    }
}
