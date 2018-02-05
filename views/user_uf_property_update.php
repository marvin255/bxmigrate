<?php echo "<?php\n"; ?>

/**
 * Миграция для обновления пользовательского поля '<?php echo mb_strtoupper($smart_param_1); ?>' для пользователя.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\n"; ?>
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        return $this->UFUpdate([
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_1); ?>',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->UFUpdate([
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_1); ?>',
        ]);
    }
}
