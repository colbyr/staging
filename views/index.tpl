{extends file="layouts/main.tpl"}

{block name=body}
<header class="grid-12">
    <h1>Staging</h1>
    <p>no-nonsesne MVC for PHP 5.2... it's so simple there aren't even models</p>
</header>

<section class="grid-12">
    <div class="media warning">
        <div class="img">
            <h2>Note:</h2>
        </div>
        <div class="body">
            <p>If you have access to PHP 5.3 don't bother with Staging! Use <strong><a href="http://laravel.com">Laravel</a></strong> instead!!!</p>
        </div>
</section>

<section>
    <h2>Dig In:</h2>

    <p>some files for your consideration...</p>

    <ul>
        <li><code>config/routes.php</code> - Map urls to behaviors (i.e. "controllers")</li>
        <li><code>controllers.php</code> - Define behavior to associate with routes</li>
        <li><code>libraries/</code> - Custom classes added to the libraries directory are automatically autoloaded</li>
        <li><code>system/</code> - this folder contains the classes that make up Staging</li>
        <li><code>views/</code> - staging uses the <a href="http://www.smarty.net/">smarty templating engine</a></li>
    </ul>
</section>

<section>

    <h2>A Basic App:</h2>

    <h4>controllers.php</h4>

    <pre class="warning"><code>class Controllers
{

    public static function before() {}

    public static function after() {}

    /**
     * Index
     *
     * controller actions are prepended with "action_"
     *
     * @return void
     */
    public static function action_index()
    {
        return View::make('index')
                    ->assign('title', 'Staging')
                    ->render();
    }

}</code></pre>

    <h4>config/routes.php</h4>

    <pre class="warning"><code>/**
 * Routes
 */
return array(
    'GET /' => 'index',
    'GET /home' => 'index'
);</code></pre>

    <h4>views/index.tpl</h4>

    <pre class="warning"><code>&lt;h1&gt;{literal}{$title}{/literal}&lt;/h1&gt;</code></pre>

</section>
{/block}