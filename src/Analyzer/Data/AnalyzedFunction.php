<?php
/**
 * @copyright 2017-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\TypeInference\Analyzer\Data;

use gossi\docblock\Docblock;

/**
 * Holds analyzed data for a function within a class and namespace. This data
 * includes argument- and return types from calls.
 */
class AnalyzedFunction
{
    /**
     * @var AnalyzedClass
     */
    private $class;

    /**
     * @var string
     */
    private $function_name;

    /**
     * @var string|null
     */
    private $defined_return_type;

    /**
     * @var AnalyzedParameter[]
     */
    private $defined_parameters;

    /**
     * @var AnalyzedCall[]
     */
    private $collected_arguments = [];

    /**
     * @var AnalyzedReturn[]
     */
    private $collected_returns = [];

    /**
     * @var bool
     */
    private $has_return_declaration;

    /**
     * @var Docblock
     */
    private $docblock;

    /**
     * @param AnalyzedClass $class
     * @param string $function_name
     * @param string|null $return_type
     * @param bool $has_return_declaration
     * @param AnalyzedParameter[] $parameters
     */
    public function __construct(
        AnalyzedClass $class,
        string $function_name,
        string $return_type = null,
        bool $has_return_declaration = false,
        array $parameters = []
    ) {
        $this->class                  = $class;
        $this->function_name          = $function_name;
        $this->defined_return_type    = $return_type;
        $this->has_return_declaration = $has_return_declaration;
        $this->defined_parameters     = $parameters;
    }

    /**
     * Appends an AnalyzedCall to a list with all analyzed calls.
     *
     * @param AnalyzedCall $call
     */
    public function addCollectedArguments(AnalyzedCall $call): void
    {
        $this->collected_arguments[] = $call;
    }

    /**
     * Appends an array of AnalyzedCall to a list with all analyzed calls.
     *
     * @param AnalyzedCall[] $calls
     */
    public function addAllCollectedArguments(array $calls): void
    {
        $this->collected_arguments = array_merge($this->collected_arguments, $calls);
    }

    /**
     * Appends an AnalyzedReturn to a list with all analyzed returns.
     *
     * @param AnalyzedReturn $return
     */
    public function addCollectedReturn(AnalyzedReturn $return): void
    {
        $this->collected_returns[] = $return;
    }

    /**
     * Appends an array of AnalyzedReturn to a list with all analyzed returns.
     *
     * @param array $returns
     */
    public function addAllCollectedReturns(array $returns): void
    {
        $this->collected_returns = array_merge($this->collected_returns, $returns);
    }

    /**
     * @return string
     */
    public function getFunctionName(): string
    {
        return $this->function_name;
    }

    /**
     * @return AnalyzedClass
     */
    public function getClass(): AnalyzedClass
    {
        return $this->class;
    }

    /**
     * @return AnalyzedCall[]
     */
    public function getCollectedArguments(): array
    {
        return $this->collected_arguments;
    }

    /**
     * @return AnalyzedReturn[]
     */
    public function getCollectedReturns(): array
    {
        return $this->collected_returns;
    }

    /**
     * @return string|null
     */
    public function getDefinedReturnType(): ?string
    {
        return $this->defined_return_type;
    }

    /**
     * @param string $defined_return_type
     */
    public function setDefinedReturnType(string $defined_return_type): void
    {
        $this->defined_return_type = $defined_return_type;
    }

    /**
     * Sets the class in which this function is declared.
     *
     * @param AnalyzedClass $class
     */
    public function setClass(AnalyzedClass $class): void
    {
        $this->class = $class;
    }

    /**
     * @return bool
     */
    public function hasReturnDeclaration(): bool
    {
        return $this->has_return_declaration;
    }

    /**
     * @return AnalyzedParameter[]
     */
    public function getDefinedParameters(): array
    {
        return $this->defined_parameters;
    }

    /**
     * @param AnalyzedParameter[] $updated_parameters
     */
    public function setDefinedParameters(array $updated_parameters): void
    {
        $this->defined_parameters = $updated_parameters;
    }

    /**
     * @return Docblock|null
     */
    public function getDocblock(): ?Docblock
    {
        return $this->docblock;
    }

    /**
     * @param Docblock $docblock
     */
    public function setDocblock(Docblock $docblock): void
    {
        $this->docblock = $docblock;
    }
}
