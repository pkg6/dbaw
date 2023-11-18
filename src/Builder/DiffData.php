<?php

namespace Pkg6\DBALW\Builder;


/**
 * @property Builder[] sourceBuilders
 * @property Builder[] targetBuilders
 */
class DiffData
{
    /**
     * @var Builder[]
     */
    protected $sourceBuilders;
    /**
     * @var Builder[]
     */
    protected $targetBuilders;

    /**
     * @param Builder[] $sourceBuilders
     * @param Builder[] $targetBuilders
     */
    public function __construct($sourceBuilders, $targetBuilders)
    {
        $this->sourceBuilders = $sourceBuilders;
        $this->targetBuilders = $targetBuilders;
    }

    /**
     * @return Builder[]
     */
    public function getSourceBuilders()
    {
        return $this->sourceBuilders;
    }

    /**
     * @return Builder[]
     */
    public function getTargetBuilders()
    {
        return $this->targetBuilders;
    }
}