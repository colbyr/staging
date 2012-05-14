<?php

/**
 * View
 *
 * Abstraction for Smarty view templates
 */
class View extends Smarty
{

    /**
     * Preset Template
     *
     * preset template for use with make()
     *
     * @var string
     */
    public $preset_template = '';

    /**
     * Set Preset Template
     *
     * sets the preset template for use with make()
     *
     * @param  string $path
     * @return void
     */
    public function set_preset_template($path)
    {
        $this->preset_template= $path;
    }

    /**
     * Make
     *
     * generate a chainable smarty object
     *
     * @param  string $template
     * @param  string $suffix
     * @return View
     */
    public static function make($template, $suffix='.tpl')
    {
        $smarty = new self;
		// Apply debug footer as global if set in config		
        $smarty->preset_template = $template . $suffix;
        return $smarty;
    }

    /**
     * Construct
     *
     * Initializes the view object
     *
     * @return void
     */
    public function __construct()
    {
        // selfpointer needed by some other class methods
        $this->smarty = $this;
        if (is_callable('mb_internal_encoding')) {
            mb_internal_encoding(Smarty::$_CHARSET);
        }
        $this->start_time = microtime(true);
        // set default dirs
        $config = Config::get('smarty');

        $this->setTemplateDir($config['templates'])
             ->setCompileDir($config['templates_c'])
             ->setPluginsDir($config['plugins'])
             ->setCacheDir($config['cache'])
             ->setConfigDir($config['configs']);

        $this->debug_tpl = 'file:system/smarty/debug.tpl';
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $this->assignGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
        }
    }

    /**
     * Render
     *
     * display a view with a preset template
     *
     * @return View
     */
    public function render()
    {
        if (!isset($this->preset_template) || $this->preset_template==='.tpl')
        {
            throw new ViewException('View->render() requires a preset_template be defined.');
        }
        return $this->display($this->preset_template);
    }

}

class ViewException extends Exception {}