<?php echo "<?php\r\n";?>

class <?php echo $name; if (!empty($parentClass)) echo " extends {$parentClass}"; echo "\r\n"; ?>
{
    public function up()
    {
        $this->IblockPropertyUpdate(
            '<?php echo mb_strtolower($smart_param_1);?>',
            [
                'CODE' => '<?php echo mb_strtolower($smart_param_2);?>',
            ]
        );
    }

    public function down()
    {
        $this->IblockPropertyUpdate(
            '<?php echo mb_strtolower($smart_param_1);?>',
            [
                'CODE' => '<?php echo mb_strtolower($smart_param_2);?>',
            ]
        );
    }
}
