<?php

namespace Fector\Harvest;

use Fector\Harvest\Combines\CombineInterface;

/**
 * Class Combine
 * @package Fector\Harvester
 */
class Harvester
{
    /**
     * @var array
     */
    protected $requestData;

    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * Combine constructor.
     * @param array $requestData
     * @param array $config
     */
    public function __construct(array $requestData, array $config = [])
    {
        $this->requestData = $requestData;
        $this->modifiers = $config;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function recycle($model)
    {
        foreach ($this->requestData as $key => $value){
            if ($this->hasModifier($key)){
                $modifier = $this->getModifierInstance($key);
                if ($modifier->isValid($value)){
                    $actions = $modifier->getActions($value);
                    foreach ($actions as $action){
                        $model = call_user_func_array([$model, $action['method']], $action['args']);
                    }
                }
            }
        }
        return $model;
    }

    /**
     * @param string $key
     * @param CombineInterface $modifier
     */
    public function loadModifier(string $key, CombineInterface $modifier): void
    {
        $this->modifiers[$key] = $modifier;
    }

    /**
     * @param string $key
     * @return CombineInterface
     */
    public function getModifierInstance(string $key): CombineInterface
    {
        return $this->modifiers[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasModifier($key): bool
    {
        return (isset($this->modifiers[$key]) && $this->modifiers[$key]);
    }
}