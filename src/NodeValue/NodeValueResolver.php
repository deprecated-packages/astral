<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\UnionType;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver\ClassConstFetchValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\ConstFetchValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\FuncCallValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\MagicConstValueResolver;
use Symplify\PackageBuilder\Php\TypeChecker;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 */
final class NodeValueResolver
{
    private ConstExprEvaluator $constExprEvaluator;

    private ?string $currentFilePath = null;

    private UnionTypeValueResolver $unionTypeValueResolver;

    /**
     * @var array<NodeValueResolverInterface>
     */
    private array $nodeValueResolvers = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private TypeChecker $typeChecker,
        SimpleNodeFinder $simpleNodeFinder,
    ) {
        $this->constExprEvaluator = new ConstExprEvaluator(fn (Expr $expr) => $this->resolveByNode($expr));
        $this->unionTypeValueResolver = new UnionTypeValueResolver();

        $this->nodeValueResolvers[] = new ClassConstFetchValueResolver($this->simpleNameResolver, $simpleNodeFinder);
        $this->nodeValueResolvers[] = new ConstFetchValueResolver($this->simpleNameResolver);
        $this->nodeValueResolvers[] = new MagicConstValueResolver();
        $this->nodeValueResolvers[] = new FuncCallValueResolver($this->simpleNameResolver, $this->constExprEvaluator);
    }

    public function resolveWithScope(Expr $expr, Scope $scope): mixed
    {
        $this->currentFilePath = $scope->getFile();

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException) {
        }

        $exprType = $scope->getType($expr);
        if ($exprType instanceof ConstantScalarType) {
            return $exprType->getValue();
        }

        if ($exprType instanceof UnionType) {
            return $this->unionTypeValueResolver->resolveConstantTypes($exprType);
        }

        return null;
    }

    public function resolve(Expr $expr, string $filePath): mixed
    {
        $this->currentFilePath = $filePath;

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException) {
            return null;
        }
    }

    private function resolveByNode(Expr $expr): mixed
    {
        if ($this->currentFilePath === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($this->nodeValueResolvers as $nodeValueResolver) {
            if (is_a($expr, $nodeValueResolver->getType(), true)) {
                return $nodeValueResolver->resolve($expr, $this->currentFilePath);
            }
        }

        // these values cannot be resolved in reliable way
        if ($this->typeChecker->isInstanceOf(
            $expr,
            [Variable::class, Cast::class, MethodCall::class, PropertyFetch::class, Instanceof_::class]
        )) {
            throw new ConstExprEvaluationException();
        }

        return null;
    }
}
