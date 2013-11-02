<!-- 
 * people, that are available for hiring instantly
-->
<?php
    $w_link = array (
        'href' => array( 'rush', 'available' ),
        'title' => 'available_people',
        'label' => 'available_people',
    );
    $w_cats = System::getConfig ('categories');
?>
<section id="available_people">
    <div>
        <header>
            <h1><?php echo Html::getParsedLink ($w_link); ?></h1>
        </header>
        <nav>
            <h1 onclick="$('#cat4avp').slideToggle();"><?php echo T::get ('categories'); ?></h1>
        </nav>
    </div>
    <nav>
        <ul id="cat4avp">
            <li onclick="selectAvpCat(this);"><?php echo T::get ('all_categories'); ?></li>
            <?php foreach ($w_cats as $cat): ?><li onclick="selectAvpCat(this);"><?php echo $cat->label; ?></li><?php endforeach; ?>
        </ul>
    </nav>
</section>
<script type="text/javascript">
    function selectAvpCat (el) 
    {
        $('#available_people nav h1').text ($(el).text());
        $('#cat4avp').slideToggle();
    }
</script>