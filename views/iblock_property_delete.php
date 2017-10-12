<?php echo "<?php\r\n"; ?>

/**
 * Миграция для удаления свойства '<?php echo ucfirst($smart_param_2); ?>' инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        return $this->IblockPropertyDelete(
            '<?php echo mb_strtolower($smart_param_1); ?>',
            '<?php echo mb_strtolower($smart_param_2); ?>'
        );
    }

    public function down()
    {
        return $this->IblockPropertyCreate(
            '<?php echo mb_strtolower($smart_param_1); ?>',
            [
                'NAME' => '<?php echo ucfirst($smart_param_2); ?>',
                'CODE' => '<?php echo mb_strtolower($smart_param_2); ?>',
                'SORT' => 500,
                'PROPERTY_TYPE' => 'S',

                'IS_REQUIRED' => 'N', //Обязательное
                'MULTIPLE' => 'N',  //Множественное
                'WITH_DESCRIPTION' => 'N', //Выводить поле для описания
                'DEFAULT_VALUE' => '', //Значение по умолчанию

                'SEARCHABLE' => 'N', //Значения свойства участвуют в поиске
                'FILTRABLE' => 'N', //Поле для фильтрации
                'MULTIPLE_CNT' => 5, //Количество полей для ввода новых множественных значений
                'HINT' => '', //Подсказка
                'DISPLAY_EXPANDED' => 'N', //Показать развёрнутым

                'USER_TYPE_SETTINGS' => [], //Настройки поля
            ]
        );
    }
}
