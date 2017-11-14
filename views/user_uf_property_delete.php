<?php echo "<?php\n"; ?>

/**
 * Миграция для удаления пользовательского поля '<?php echo mb_strtoupper($smart_param_1); ?>' для пользователя.
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
        return $this->UFDelete('USER', 'UF_<?php echo mb_strtoupper($smart_param_1); ?>');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->UFCreate([
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_1); ?>',
            'ENTITY_ID' => 'USER',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => '<?php echo ucfirst($smart_param_1); ?>',
            ],
        ]);
    }
}
