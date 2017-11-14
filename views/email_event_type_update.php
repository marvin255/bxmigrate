<?php echo "<?php\n"; ?>

/**
 * Миграция для обновления типа почтового события '<?php echo ucfirst($smart_param_1); ?>' для языка '<?php echo ucfirst($smart_param_2); ?>'.
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
        return $this->updateEmailEventType([
            'EVENT_NAME' => '<?php echo $smart_param_1; ?>',
            'LID' => '<?php echo $smart_param_2; ?>',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->updateEmailEventType([
            'EVENT_NAME' => '<?php echo $smart_param_1; ?>',
            'LID' => '<?php echo $smart_param_2; ?>',
        ]);
    }
}
