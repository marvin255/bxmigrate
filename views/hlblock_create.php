<?php echo "<?php\r\n"; ?>

class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        $this->HLCreate([
            'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
            'TABLE_NAME' => '', //insert data base table name
        ]);
    }

    public function down()
    {
        $this->HLDelete('<?php echo ucfirst($smart_param_1); ?>');
    }
}
