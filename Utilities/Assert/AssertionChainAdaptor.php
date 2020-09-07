<?php

class Container_Utilities_Assert_AssertionChainAdaptor
{
    private $value;

    private $defaultMessage;

    private $defaultPropertyPath;

    /**
     * Return each assertion as always valid.
     *
     * @var bool
     */
    private $alwaysValid = false;

    /**
     * Perform assertion on every element of array or traversable.
     *
     * @var bool
     */
    private $all = false;

    /** @var string|Assertion Class to use for assertion calls */
    private $assertionClassName = 'Assert\Assertion';

    public function __construct($value, $defaultMessage = null, $defaultPropertyPath = null)
    {
        $this->value = $value;
        $this->defaultMessage = $defaultMessage;
        $this->defaultPropertyPath = $defaultPropertyPath;
    }

    /**
     * @param $methodName
     * @param $args
     * @return $this
     * @throws \ReflectionException
     */
    public function __call($methodName, $args)
    {
        if (true === $this->alwaysValid) {
            return $this;
        }
        if (!\method_exists($this->assertionClassName, $methodName)) {
            throw new \RuntimeException("Assertion '" . $methodName . "' does not exist.");
        }
        $reflClass = new ReflectionClass($this->assertionClassName);
        $method = $reflClass->getMethod($methodName);
        \array_unshift($args, $this->value);
        $messageFlagParams = [];
        $params = $method->getParameters();
        foreach ($params as $idx => $param) {
            $paramName = $param->getName();
            if (isset($args[$idx])) {
                if ('message' == $paramName) {
                    $flagIdx = $idx + 1;
                    if (isset($args[$flagIdx])) {
                        $messageFlagParams = $args[$flagIdx];
                        unset($args[$flagIdx]);
                        $args = array_values($args);
                    }
                }
                continue;
            }
            if ('message' == $paramName) {
                $args[$idx] = $this->defaultMessage;
            }
            if ('propertyPath' == $paramName) {
                $args[$idx] = $this->defaultPropertyPath;
            }
        }
        if ($this->all) {
            $methodName = 'all' . $methodName;
        }
        try {
            \call_user_func_array(array($this->assertionClassName, $methodName), $args);
        } catch (\Exception $e) {
            if (empty($messageFlagParams)) {
                throw $e;
            }
            $printMsg = strtr($e->getMessage(), $messageFlagParams);
            throw new \Exception($printMsg, $e->getCode(), $e);
        }
        return $this;
    }

    /**
     * Switch chain into validation mode for an array of values.
     *
     * @return Container_Utilities_Assert_AssertionChainAdaptor
     */
    public function all()
    {
        $this->all = true;
        return $this;
    }

    /**
     * Switch chain into mode allowing nulls, ignoring further assertions.
     *
     * @return Container_Utilities_Assert_AssertionChainAdaptor
     */
    public function nullOr()
    {
        if (null === $this->value) {
            $this->alwaysValid = true;
        }
        return $this;
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    public function setAssertionClassName($className)
    {
        if (!\is_string($className)) {
            throw new LogicException('Exception class name must be passed as a string');
        }
        if ('Assert\Assertion' !== $className && !\is_subclass_of($className, 'Assert\Assertion')) {
            throw new LogicException($className . ' is not (a subclass of) Assert\Assertion');
        }
        $this->assertionClassName = $className;
        return $this;
    }
}