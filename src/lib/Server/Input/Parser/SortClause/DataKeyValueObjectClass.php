<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformRest\Server\Input\Parser\SortClause;

use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformRest\Input\BaseParser;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use EzSystems\EzPlatformRest\Exceptions;

class DataKeyValueObjectClass extends BaseParser
{
    /**
     * Data key, corresponding to the $valueObjectClass class.
     * Example: 'DatePublished'.
     *
     * @var string
     */
    protected $dataKey;

    /**
     * Value object class, corresponding to the $dataKey.
     * Example: 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished'.
     *
     * @var string
     */
    protected $valueObjectClass;

    /**
     * DataKeyValueObjectClass constructor.
     *
     * @param string $dataKey
     * @param string $valueObjectClass
     */
    public function __construct($dataKey, $valueObjectClass)
    {
        $this->dataKey = $dataKey;
        $this->valueObjectClass = $valueObjectClass;
    }

    /**
     * Parse input structure.
     *
     * @param array $data
     * @param \EzSystems\EzPlatformRest\Input\ParsingDispatcher $parsingDispatcher
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        if (!class_exists($this->valueObjectClass)) {
            throw new Exceptions\Parser("Value object class <{$this->valueObjectClass}> is not defined");
        }

        if (!array_key_exists($this->dataKey, $data)) {
            throw new Exceptions\Parser("The <{$this->dataKey}> sort clause doesn't exist in the input structure");
        }

        $direction = $data[$this->dataKey];

        if (!in_array($direction, [Query::SORT_ASC, Query::SORT_DESC])) {
            throw new Exceptions\Parser("Invalid direction format in <{$this->dataKey}> sort clause");
        }

        return new $this->valueObjectClass($direction);
    }
}
