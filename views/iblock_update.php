<?php echo "<?php\r\n"; ?>

class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        return $this->IblockUpdate([
            'CODE' => '<?php echo mb_strtolower($smart_param_1); ?>',
        ]);
    }

    public function down()
    {
        return $this->IblockUpdate([
            'CODE' => '<?php echo mb_strtolower($smart_param_1); ?>',
        ]);
    }
}
