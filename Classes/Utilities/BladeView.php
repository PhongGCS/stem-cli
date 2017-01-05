<?php
/**
 * Created by PhpStorm.
 * User: jong
 * Date: 12/25/16
 * Time: 5:16 PM.
 */

namespace ILab\Stem\CommandLine\Utilities;

use duncan3dc\Laravel\BladeInstance;

final class BladeView
{
    public static function renderViewToFile($view, $targetFile, $data = [])
    {
        $blade = new BladeInstance(STEM_CLI_DIR.'Templates'.DIRECTORY_SEPARATOR, sys_get_temp_dir());
        file_put_contents($targetFile, $blade->render($view, $data));
    }
}
