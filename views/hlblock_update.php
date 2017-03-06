<?php echo "<?php\r\n"; ?>

class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        $this->HLUpdate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
        ]);
    }

    public function down()
    {
        $this->HLUpdate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
        ]);
    }
}
