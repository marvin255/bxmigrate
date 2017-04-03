<?php echo "<?php\r\n"; ?>

/**
 * Миграция для обновления пользовательского поля '<?php echo mb_strtoupper($smart_param_1); ?>' для пользователя.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        return $this->UFUpdate([
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_1); ?>',
        );
    }

    public function down()
    {
        return $this->UFUpdate([
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_1); ?>',
        );
    }
}
