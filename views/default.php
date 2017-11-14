<?php echo "<?php\n"; ?>

/**
 * Мирация '<?php echo $name; ?>'.
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
