<?php echo "<?php\r\n"; ?>

/**
 * Миграция для удаления модуля '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        if (($module = \CModule::CreateModuleObject('<?php echo $smart_param_1; ?>')) && $module->IsInstalled()) {
            $module->DoUninstall();
        }
    }

    public function down()
    {
        if (($module = \CModule::CreateModuleObject('<?php echo $smart_param_1; ?>')) && !$module->IsInstalled()) {
            $module->DoInstall();
        }
    }
}
