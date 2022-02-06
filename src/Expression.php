<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-29
 * Time: 16:29
 */

namespace elfuvo\documentStore;

/**
 * Class Expression
 * @package elfuvo\documentStore
 */
class Expression
{
    /**
     * @var string
     */
    public string $expression = '';

    /**
     * @var array
     */
    public array $params = [];

    /**
     * Expression constructor.
     * @param string $expression - can use some bind values: ':foo in customColumn'
     * @param array $params - bind params - ['foo' => 'bar']
     * @throws \elfuvo\documentStore\Exception
     */
    public function __construct(string $expression, array $params = [])
    {
        if (empty($expression)) {
            throw new Exception('Expression is empty');
        }

        array_walk(
            $params,
            function ($value, $name) {
                $name = preg_replace('#^:#', '', $name);
                $this->params[$name] = $value;
            }
        );
        $this->expression = $this->formatExpression($expression);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \elfuvo\documentStore\Exception
     */
    protected function formatExpression(string $expression): string
    {
        if (preg_match_all('#:([^\s]+)#', $expression, $binds)) {
            foreach ($binds[1] as $paramName) {
                if (!array_key_exists($paramName, $this->params)) {
                    throw new Exception('Bind param ":' . $paramName . '" not found in param values');
                }
            }
        }
        return preg_replace('#`#', '', $expression);
    }

    /**
     * @return bool
     */
    public function hasBind(): bool
    {
        return !empty($this->params);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->expression;
    }
}
