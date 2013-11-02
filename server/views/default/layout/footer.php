<!-- footer of the site, copyrights, ToS...  -->
<section id="popup">
    <article id="popup_content">
        <div></div>
        <nav><ul><li id="popup_close_button">[<a href="#" onclick="$('#popup').fadeToggle(700)">close</a>]</li></ul></nav>
    </article>
</section>
<footer>
© Eastern Block, 2013
<?php echo '<p id="trace">'.Profiler::getTotalTime ().'s</p>'; ?>
</footer>
</body>
</html>
