<section id="content">
    <header><h1>An error has occured!</h1></header>
    <p><?php echo $e->getMessage (); ?></p>
    <aside><a href="<?php $route = System::getConfig ('route'); echo Html::getUrl ($route['default']); ?>">To the main page</a></aside>
</section>