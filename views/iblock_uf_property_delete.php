<?php echo "<?php\n"; ?>

/**
 * Миграция для удаления пользовательского свойства раздела '<?php echo mb_strtoupper($smart_param_2); ?>' инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
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
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1); ?>');
        $entity = 'IBLOCK_' . $id . '_SECTION';

        return $this->UFDelete($entity, 'UF_<?php echo mb_strtoupper($smart_param_2); ?>');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1); ?>');

        return $this->UFCreate([
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
            'ENTITY_ID' => 'IBLOCK_' . $id . '_SECTION',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => '<?php echo ucfirst($smart_param_2); ?>',
            ],
        ]);
    }
}
