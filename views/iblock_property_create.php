<?php echo "<?php\r\n";?>

class <?php echo $name; if (!empty($parentClass)) echo " extends {$parentClass}"; echo "\r\n"; ?>
{
    public function up()
    {
        $this->IblockPropertyCreate(
            '<?php echo mb_strtolower($smart_param_1);?>',
            [
                'NAME' => '<?php echo ucfirst($smart_param_2);?>',
                'CODE' => '<?php echo mb_strtolower($smart_param_2);?>',
                'SORT' => 500,
                'PROPERTY_TYPE' => 'S',
                'MULTIPLE' => 'N',
                'WITH_DESCRIPTION' => 'N',
            ]
        );
    }

    public function down()
    {
        $this->IblockPropertyDelete(
            '<?php echo mb_strtolower($smart_param_1);?>',
            '<?php echo mb_strtolower($smart_param_2);?>'
        );
    }
}
