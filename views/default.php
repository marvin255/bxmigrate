<?php echo "<?php\r\n";?>

class <?php echo $name; if (!empty($parentClass)) echo " extends {$parentClass}"; echo "\r\n"; ?>
{
	public function up()
	{
		//set migration
	}

	public function down()
	{
		//unset migration
	}
}