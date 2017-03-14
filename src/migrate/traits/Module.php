<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CModule;

trait Module
{
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function installModule($name)
    {
        $return = [];
        if (!($module = CModule::CreateModuleObject($name))) {
            throw new Exception("Module {$name} not found");
        } elseif ($module->IsInstalled()) {
            throw new Exception("Module {$name} already installed");
        } else {
            $module->DoInstall();
            $return[] = "Module {$name} installed";
        }

        return $return;
    }

    /**
     * @param string $name
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    public function uninstallModule($name)
    {
        $return = [];
        if (!($module = CModule::CreateModuleObject($name))) {
            throw new Exception("Module {$name} not found");
        } elseif (!$module->IsInstalled()) {
            throw new Exception("Module {$name} already uninstalled");
        } else {
            $module->DoUninstall();
            $return[] = "Module {$name} uninstalled";
        }

        return $return;
    }
}
