<?php echo "<?php\n"; ?>

/**
 * Миграция для удаления модуля '<?php echo ucfirst($smart_param_1); ?>'.
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
        return $this->uninstallModule('<?php echo $smart_param_1; ?>');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->installModule('<?php echo $smart_param_1; ?>');
    }
}
