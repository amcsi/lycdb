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
    var tr = $(card).closest('tr');

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
    var height = attachment.outerHeight(true);

    current_attachment = attachment;
    attachment.position({
        at: 'left+10 bottom+10',
        my: 'left+10 top+10',
        of: tr,
        collition: 'flip'
    });
}

function hide_card(card, event) {
    attachment = get_card_attachment(card);
    attachment.hide();
}

function get_card_attachment(card) {
    var id = $(card).closest('tr').attr('id').substr(12);
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

$(function () {
    var cardResults = $('table.card_results');
    if (cardResults.length) {
        var keepMode = false;
        var displayingCard = false;
        var trs = cardResults.find('tbody tr');
        var lastOpenedHtmlEl = null;
        var overlayOpenSelector = 'td.cardId,td.cardName';
        $('html').click(function (evt) {
            if (keepMode) {
                var target = $(evt.target);
                if (!target.closest('.card_js').length) {
                    hide_card(lastOpenedHtmlEl, evt);
                    keepMode = false;
                    if ($(evt.target).closest(overlayOpenSelector).length) {
                        onMouseOver.call(this, evt);
                    }
                }
            }
            else {
                if ($(evt.target).closest(overlayOpenSelector).length) {
                    keepMode = true;
                }
            }
        });
        var onMouseOver = function (evt) {
            if (!keepMode) {
                display_card(evt.target, evt);
                displayingCard = true;
                lastOpenedHtmlEl = evt.target;
            }
        };
        trs.on('mouseover', overlayOpenSelector, onMouseOver);
        trs.on('mouseout', overlayOpenSelector, function (evt) {
            if (!keepMode) {
                hide_card(evt.target, evt);
                displayingCard = false;
            }
        });
    }

    $('span.tooltip').tooltip({
        content: function (cb) {
            cb($(this).prop('title').replace('\n', '<br>'));
        }
    });
});
