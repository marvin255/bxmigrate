<?php echo "<?php\n"; ?>

/**
 * Миграция для создания свойства '<?php echo mb_strtoupper($smart_param_2); ?>' highload инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
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
        $id = $this->HLGetIdByCode('<?php echo ucfirst($smart_param_1); ?>');

        return $this->UFCreate([
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
            'ENTITY_ID' => 'HLBLOCK_' . $id,
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => '<?php echo ucfirst($smart_param_2); ?>',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $id = $this->HLGetIdByCode('<?php echo ucfirst($smart_param_1); ?>');
        $entity = 'HLBLOCK_' . $id;

        return $this->UFDelete($entity, 'UF_<?php echo mb_strtoupper($smart_param_2); ?>');
    }
}
