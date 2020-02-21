<?php

namespace Mageplaza\Reports\Model\Api\Data;

use Magento\Framework\DataObject;

/**
 * Class Card
 * @package Mageplaza\Reports\Model
 */
class Card extends DataObject implements \Mageplaza\Reports\Api\Data\CardInterface
{
    protected $name = '';
    protected $data;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return mixed|void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
