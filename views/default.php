<?php echo "<?php\r\n"; ?>

class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        //set migration
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        //unset migration
    }
}
