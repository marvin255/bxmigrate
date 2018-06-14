<?php

/**
 * Мок для результата запроса на обновление/создание/удаление сущности.
 */
class ExecuteResult
{
    /**
     * @var int|null
     */
    protected $id;
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param int   $id
     * @param array $errors
     */
    public function __construct($id, array $errors = [])
    {
        $this->id = $id === null ? null : (int) $id;
        $this->errors = $errors;
    }

    /**
     * Возвращает прошел запрос успешно или нет.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->id !== null;
    }

    /**
     * Возвращает идентификатор созданной записи.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id ?: 0;
    }

    /**
     * Возвращает список ошибок, произошедших во время запроса.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errors;
    }
}
