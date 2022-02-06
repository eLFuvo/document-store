<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 12:54
 */

namespace elfuvo\documentStore\entity;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class AbstractEntity
 * @package elfuvo\documentStore\entity
 */
abstract class AbstractEntity implements EntityInterface, JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    protected string $hydrator = ClassMethodsHydrator::class;

    /**
     * @var string|null
     */
    protected ?string $_id = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->_id ?: null;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->_id = $id;
    }

    /**
     * @inheritDoc
     */
    public function populate(array $data): EntityInterface
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = new $this->hydrator();
        $hydrator->hydrate($data, $this);

        return $this;
    }

    /**
     * @return array|null
     */
    public function extract(): ?array
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = new $this->hydrator();

        return $hydrator->extract($this);
    }

    /**
     * @return array|null
     */
    public function jsonSerialize(): ?array
    {
        return $this->extract();
    }

    /**
     * @return array|null
     */
    public function toArray(): ?array
    {
        return $this->extract();
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            call_user_func([$this, 'set' . $name], $value);
        }
    }

    /**
     * @param string $name
     * @return mixed|void
     */
    public function __get(string $name)
    {
        if (method_exists($this, 'get' . $name)) {
            return call_user_func([$this, 'get' . $name]);
        }
        $trace = debug_backtrace();
        trigger_error(
            'Read of property "' . $name . '" not available.' .
            ' file: ' . $trace[0]['file'] .
            ' line: ' . $trace[0]['line'],
            E_USER_NOTICE
        );
    }
}
