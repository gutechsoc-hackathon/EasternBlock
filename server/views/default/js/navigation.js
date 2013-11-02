/**
 * closes popup
 */
function closePopup ()
{
    $('#popup').css ('display', 'none');
}

/**
 * opens popup
 */
function openPopup ()
{
    $('#popup').css ('display', 'block');
}

/**
 * adds html code inside the popup
 */
function setPopupHtml (code)
{
    $('#popup_content > div').html (code);
}

/**
 * inserts a page into a popup, via ajax request
 */
function popup (params)
{
    $('#popup').fadeToggle (700);
    ajax (params, function (code) {
        if (code.substr (0, 10) == 'PLAIN_HTML')
            setPopupHtml (code.substr (10));
        else
            alert (code);
    });
}

/**
 * showing varliukas
 */
function runVarliukas () 
{
}

/**
 * disabling varliukas
 */
function stopVarliukas ()
{
}

/** ------------------------------------------------------------------------------------------------
 * ajax
 */
function ajax (params, success)
{
    runVarliukas ();
    $.ajax ({
        type: 'POST',
        url: 'index.php',
        data: params,
    }).done (function (msg) {
        success (msg);
        stopVarliukas ();
    });
}

/** ------------------------------------------------------------------------------------------------
 * confirmation
 */
function confirmLink (text, address)
{
    if (confirm (text))
        window.location = address;
}

/** ------------------------------------------------------------------------------------------------
 * on load actions
 */
function doOnLoad ()
{
    // clicking tiles result in going to the first link
    $('.boards article').click (function (e) {
        if (e.target.nodeName == 'IMG')
            return;
        $link = $(this).find ('a').first ();
        if ($link.attr ('href'))
            window.location = $link.attr ('href');
    });
}