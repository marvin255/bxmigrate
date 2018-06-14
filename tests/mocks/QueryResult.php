<?php

/**
 * Мок для результата поиска по запросу.
 */
class QueryResult
{
    /**
     * @var array
     */
    protected $result;
    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @param array $result
     */
    public function __construct(array $result = [])
    {
        $this->result = array_values($result);
    }

    /**
     * Возвращает одну запись.
     *
     * @return mixed
     */
    public function fetch()
    {
        $return = isset($this->result[$this->counter])
            ? $this->result[$this->counter]
            : false;

        ++$this->counter;

        return $return;
    }

    /**
     * Возвращает одну запись.
     *
     * @return mixed
     */
    public function getNext()
    {
        $return = isset($this->result[$this->counter])
            ? $this->result[$this->counter]
            : false;

        ++$this->counter;

        return $return;
    }

    /**
     * Возвращает все записи.
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->result;
    }
}
