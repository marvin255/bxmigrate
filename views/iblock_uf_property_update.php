<?php echo "<?php\r\n"; ?>

class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1); ?>');
        return $this->UFUpdate([
            'ENTITY_ID' => 'IBLOCK_' . $id . '_SECTION',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
        );
    }

    public function down()
    {
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1); ?>');
        return $this->UFUpdate([
            'ENTITY_ID' => 'IBLOCK_' . $id . '_SECTION',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
        );
    }
}
