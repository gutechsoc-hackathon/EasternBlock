<!-- 
* top menu of the site
-->
<nav id="top_menu">
    <ul>
    <?php foreach (System::getConfig ('top_menu') as $item): ?><li <?php if ($item['href'][0] == System::$url[0]) echo 'class="active"'; ?>><?php echo Html::getParsedLink ($item); ?></li><?php endforeach; ?>
    </ul>
</nav>