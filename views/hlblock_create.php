<?php echo "<?php\r\n"; ?>

/**
 * Миграция для создания highload инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        return $this->HLCreate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
            'TABLE_NAME' => '', //insert data base table name
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->HLDelete('<?php echo ucfirst($smart_param_1); ?>');
    }
}
