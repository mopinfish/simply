<?php
class ClassLoader
{
    protected $dirs;

    public function register()
    {
        spl_auto_load_register(array($this, 'loadClass'));
    }

    public function registerDir($dir)
    {
    }

    public function loadClass($class)
    {
    }
}
echo 'hogehoge';
