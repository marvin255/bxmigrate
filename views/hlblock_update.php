<?php echo "<?php\n"; ?>

/**
 * Миграция для обновления highload инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
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
        return $this->HLUpdate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->HLUpdate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
        ]);
    }
}
