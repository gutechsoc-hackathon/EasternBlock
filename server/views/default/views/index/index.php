<section id="content">
    <h1>Sample responses</h1>
    <ul>
        <li><strong>Dumb retrieval</strong></li>
        <li><a href="index.php?r=events/list&sess_id=614bfb0f86793c46338f758278fa7132">List of events</a></li>
        <li><a href="index.php?r=question/list&sess_id=614bfb0f86793c46338f758278fa7132">List of questions</a></li>
        <li><a href="index.php?r=location/list&sess_id=614bfb0f86793c46338f758278fa7132">List of locations</a></li>
        <li><a href="index.php?r=media/elist&event_id=8&sess_id=614bfb0f86793c46338f758278fa7132">Media for an event</a></li>
        <li><a href="index.php?r=media/ulist&user_id=3&sess_id=614bfb0f86793c46338f758278fa7132">Media for the user</a></li>
        <li><a href="index.php?r=user/online&sess_id=614bfb0f86793c46338f758278fa7132">Users online</a></li>
        <li><strong>Smart retrieval</strong></li>
        <li><a href="index.php?r=tag/list&sess_id=614bfb0f86793c46338f758278fa7132">List of tags</a></li>
        <li><a href="index.php?r=events/dlist&dist=1&lat=55.880661&long=-4.2735219&sess_id=614bfb0f86793c46338f758278fa7132">Events in an area</a></li>
        <li><a href="index.php?r=question/dlist&dist=1&lat=55.880661&long=-4.2735219&sess_id=614bfb0f86793c46338f758278fa7132">Questions in an area</a></li>
        <li><a href="index.php?r=location/dlist&dist=1&lat=55.880661&long=-4.2735219&sess_id=614bfb0f86793c46338f758278fa7132">Locations in an area</a></li>
        <li><a href="index.php?r=user/dlist&dist=1&lat=55.880661&long=-4.2735219&sess_id=614bfb0f86793c46338f758278fa7132">Users in an area</a></li>
        <li><strong>Image upload test</strong></li>
        <li>
            <form action="index.php" method="post"
enctype="multipart/form-data">
                <input type="file" id="file" name="file"/>
                <input type="hidden" name="sess_id" value="614bfb0f86793c46338f758278fa7132" />
                <input type="hidden" name="r" value="media/upload" />
                <input type="hidden" name="type_id" value="1" />
                <input type="hidden" name="event_id" value="8" />
                <input type="submit" value="upload" />
            </form>
        </li>
    </ul>
</section>
