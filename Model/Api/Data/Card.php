<?php

namespace Mageplaza\Reports\Model\Api\Data;

/**
 * Class Card
 * @package Mageplaza\Reports\Model
 */
class Card implements \Mageplaza\Reports\Api\Data\CardInterface
{
    protected $name = '';
    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setData($data)
    {
        $this->data = $data;
        return $data;
    }

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
