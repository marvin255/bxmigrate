<?php echo "<?php\r\n"; ?>

/**
 * Миграция для обновления свойства '<?php echo ucfirst($smart_param_2); ?>' инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        return $this->IblockPropertyUpdate(
            '<?php echo mb_strtolower($smart_param_1); ?>',
            [
                'CODE' => '<?php echo mb_strtolower($smart_param_2); ?>',
            ]
        );
    }

    public function down()
    {
        return $this->IblockPropertyUpdate(
            '<?php echo mb_strtolower($smart_param_1); ?>',
            [
                'CODE' => '<?php echo mb_strtolower($smart_param_2); ?>',
            ]
        );
    }
}
