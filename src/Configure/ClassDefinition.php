<?php


namespace Faid\Configure;


class ClassDefinition
{

    /**
     * @var string
     */
    private $definitionAlias;

    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $classParams = array();

    /**
     * @var array
     */
    private $startUpMethods = array();

    /**
     * @param string $definitionAlias
     * @throws ConfigureException
     */
    public function __construct($definitionAlias)
    {
        $this->setDefinitionAlias($definitionAlias);
    }

    /**
     * Creates new container definition class
     *
     * @param $definitionAlias
     * @return ClassDefinition
     */
    public static function createNew($definitionAlias)
    {
        return new self($definitionAlias);
    }

    /**
     * Specify class name definition
     *
     * @param $className
     * @return $this
     */
    public function forClass($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Adds class constructor params
     *
     * @param array $classParams
     * @return $this
     */
    public function withParams($classParams = array())
    {
        $this->classParams = $classParams;

        return $this;
    }

    /**
     * Adds a class constructor param
     *
     * @param $param
     * @param bool|false $isShared
     * @return $this
     */
    public function addParam($param, $isShared = false)
    {
        $this->classParams[] = array(
            'param'  => $param,
            'shared' => $isShared
        );

        return $this;
    }

    /**
     * Adds a shared class constructor param
     * (Singelton instance)
     *
     * @param mixed $param
     * @return $this
     */
    public function addSharedParam($param)
    {
        $this->addParam($param, true);

        return $this;
    }

    /**
     * @param MethodDefinition $startUpMethod
     * @return $this
     */
    public function addStartUpMethod(MethodDefinition $startUpMethod)
    {
        $this->startUpMethods[] = $startUpMethod;

        return $this;
    }


    /**
     * @param string $className
     * @throws Exception
     */
    private function setDefinitionAlias($className)
    {
        $this->definitionAlias = $className;
    }

    /**
     * @return string
     */
    public function getDefinitionAlias()
    {
        return $this->definitionAlias;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getClassParams()
    {
        return $this->classParams;
    }

    /**
     * @return array
     */
    public function getStartUpMethods()
    {
        return $this->startUpMethods;
    }
}