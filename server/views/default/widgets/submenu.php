<!-- 
 * submenu of the site
-->
<section id="submenu">
    <div><header><h1><?php echo T::get ('submenu'); ?></h1></header></div>
    <nav>
        <ul>
        <?php 
            foreach (System::getConfig ('submenu') as $item): 
                ?><li <?php 
                if ($item['href'][0] == System::$url[0]  
                    && (isset (System::$url[1]) && System::$url[1] && $item['href'][1] == System::$url[1]
                    || (!isset (System::$url[1]) || !System::$url[1]) && $this->defaultAction == $item['href'][1])
                    ) {
                    echo 'class="active"'; 
                }
                ?> > <?php echo Html::getParsedLink ($item); ?></li><?php 
            endforeach; 
        ?>
        </ul>
    </nav>
</section>