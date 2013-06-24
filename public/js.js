function show_hide(tag_id) {
    element = document.getElementById(tag_id);
    if (element.style.display != 'none') {
        element.style.display = 'none';
    }
    else {
        element.style.display = 'block';
    }
}

function display_card(card, e) {
    
    var attachment = get_card_attachment(card);
    var img = attachment.find('img.card-image');
    if (!img.length) {
        var cont = attachment.find('div.card_js_image');
        var targetSrc = cont.data('src');
        var width = cont.data('width');
        img = $('<img />');
        img.addClass('card-image');
        img.attr('width', width);
        img.attr('src', targetSrc);
        cont.append(img);
    }
    attachment.show();
    current_attachment = attachment;
    update_card_attachment_position(e);

}

function hide_card(card, event) {
    attachment = get_card_attachment(card);
    attachment.hide();
}

function update_card_attachment_position(e) {
    var cursor = getPosition(e);
    current_attachment.css('left', cursor.x + 4);
    current_attachment.css('top', cursor.y + 4);
}

function get_card_attachment(card) {
    var id = $(card).parent().attr('id').substr(12);
    return $("#card_js_" + id);
}

function getPosition(e) {
    e = e || window.event;
    var cursor = {x:0, y:0};
    if (e.pageX || e.pageY) {
        cursor.x = e.pageX;
        cursor.y = e.pageY;
    } 
    else {
        var de = document.documentElement;
        var b = document.body;
        cursor.x = e.clientX + 
            (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
        cursor.y = e.clientY + 
            (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
    }
    return cursor;
}
