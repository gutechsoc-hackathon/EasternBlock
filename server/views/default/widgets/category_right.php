<!-- 
* categories list
-->
<?php
    $w_cats = System::getConfig ('categories');
    $w_selected = System::getConfig ('selectedCat');
    $w_canCreate = System::$user->checkAccess ('moderator');
?>
<ul id="cat_menu">
    <li <?php if (!$w_selected && (!isset (System::$url[1]) || System::$url[1] != 'adv')) echo 'class="active"'; ?> > <?php echo Html::getLink (array ('href' => Html::getUrl ('boards')), Html::getNonbreakable (T::get ('all_categories'))); ?></li>
    <?php 
    foreach ($w_cats as $cat): 
        ?><li <?php if ($w_selected && $cat->id == $w_selected->id) echo 'class="active"'; ?> > <?php echo Html::getLink (array ('href' => Html::getUrl ('boards/cat/'.$cat->id)), Html::getNonbreakable ($cat->label)); ?></li><?php 
    endforeach; 
    ?>
    <?php if ($w_canCreate) { ?>
    <li><a href="#" onclick="popup({r: 'boards/newcat'});"><?php echo Html::getNonbreakable (T::get ('Create new category')); ?></a></li>
    <?php } ?>
</ul>