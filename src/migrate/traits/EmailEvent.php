<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CEventType;

/**
 * Трэйт с функциями для типов почтовых сообщений.
 */
trait EmailEvent
{
    /**
     * Создает новый тип почтовых событий.
     *
     * @param array $eventData
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function createEmailEventType(array $eventData)
    {
        if ($this->findEmailEventType($eventData)) {
            throw new Exception("Email event type {$eventData['EVENT_NAME']} already exists");
        }
        $et = new CEventType;
        $res = $et->add($eventData);
        if (!$res) {
            throw new Exception("Can't create email event type {$eventData['EVENT_NAME']}: {$et->LAST_ERROR}");
        }

        return ["Email event type {$eventData['EVENT_NAME']}({$res}) created"];
    }

    /**
     * Обновляет тип почтовых событий.
     *
     * @param array $eventData
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function updateEmailEventType(array $eventData)
    {
        if (!($event = $this->findEmailEventType($eventData))) {
            throw new Exception("Can't find {$eventData['EVENT_NAME']} email event type");
        }
        $et = new CEventType;
        unset($eventData['EVENT_NAME'], $eventData['LID']);
        $res = $et->update(['ID' => $event['ID']], $eventData);
        if (!$res) {
            throw new Exception("Can't update email event type {$eventData['EVENT_NAME']}: {$et->LAST_ERROR}");
        }

        return ["Email event type {$event['EVENT_NAME']}({$event['ID']}) updated"];
    }

    /**
     * Удаляет тип почтового события по его идентификатору (EVENT_NAME).
     *
     * @param array $eventData
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function deleteEmailEventType(array $eventData)
    {
        if (!($event = $this->findEmailEventType($eventData))) {
            throw new Exception("Can't find {$eventData['EVENT_NAME']} email event type");
        }
        $et = new CEventType;
        $et->delete(['ID' => $event['ID']]);

        return ["Email event type {$eventData['EVENT_NAME']}({$event['ID']}) deleted"];
    }

    /**
     * Ищет тип почтового события по массиву параметров.
     *
     * @param array $eventData
     *
     * @return array|null
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function findEmailEventType(array $eventData)
    {
        if (empty($eventData['EVENT_NAME'])) {
            throw new Exception('Empty email event type name');
        }
        $filter = ['TYPE_ID' => $eventData['EVENT_NAME']];
        if (!empty($eventData['LID'])) {
            $filter['LID'] = $eventData['LID'];
        }

        return CEventType::getList($filter)->fetch();
    }
}
