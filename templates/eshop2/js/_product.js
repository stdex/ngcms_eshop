var productPhotoCZoom = window.productPhotoCZoom !== undefined,
        productPhotoDrop = window.productPhotoDrop !== undefined;

var hrefOptions = {
    next: '#photo .drop-content .next',
    prev: '#photo .drop-content .prev',
    gallery: '#photo .frame-fancy-gallery',
    cycle: false,
    //    'frame' and other in extend optionsPhoto and this object
    footerContent: '.frame-prices-buy-wish-compare',
    galleryContent: '.items-thumbs'
};
var optionsPhoto = {
    effectOn: 'fadeIn',
    drop: '#photo',
    position: 'absolute',
    before: 'Product.beforeShowHref',
    after: 'Product.onComplete',
    closed: 'Product.afterClosedPhoto'
};

Product = {
    changeVariant: function (el) {
        el = el === undefined ? body : el;
        /*Variants in Product*/
        el.find(genObj.parentBtnBuy).find(genObj.changeVariantProduct).on('change', function () {
            var productId = parseInt($(this).attr('value')),
                    liBlock = $(this).closest(genObj.parentBtnBuy),
                    btnInfo = liBlock.find(genObj.prefV + productId).find(genObj.infoBut),
                    vId = btnInfo.data('id'),
                    vName = $.trim(btnInfo.data('vname')),
                    vNumber = $.trim(btnInfo.data('number')),
                    vPrice = btnInfo.data('price'),
                    vAddPrice = btnInfo.data('addPrice'),
                    vOrigPrice = btnInfo.data('origPrice'),
                    vLargeImage = $.trim(btnInfo.data('largeImage')),
                    vMainImage = $.trim(btnInfo.data('mainImage')),
                    vStock = btnInfo.data('maxcount');

            $(genObj.photoProduct).add($(genObj.mainThumb)).attr('href', vLargeImage);
            $(genObj.imgVP).attr({
                'src': vMainImage,
                'alt': vName
            });
            $('.leftProduct .items-thumbs > li').removeClass('active').filter(':eq(0)').addClass('active');

            if (vOrigPrice !== '')
                liBlock.find(genObj.priceOrigVariant).html(vOrigPrice);
            liBlock.find(genObj.priceVariant).html(vPrice);
            liBlock.find(genObj.priceAddPrice).html(vAddPrice);
            ShopFront.Cart.existsVnumber(vNumber, liBlock);
            ShopFront.Cart.existsVnames(vName, liBlock);
            ShopFront.Cart.condProduct(vStock, liBlock, btnInfo);
            liBlock.find(genObj.selVariant).hide();
            liBlock.find(genObj.prefV + vId).show();

            if (productPhotoCZoom) {
                $('.mousetrap').remove();
                $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom({showTitle: false});
            }
        });
        /*/Variants in Product*/
    },
    initDrop: function (el) {//when click on <a>
        $this = el;
        $this.data($.extend({
            'frame': $('.item-product'),
            'mainPhoto': $this.attr('href'),
            'title': $this.attr('title')
        }, optionsPhoto));
        return true;
    },
    resizePhoto: function (drop, s, c) {
        var fancyFrame = drop.find('.drop-content'),
                img = fancyFrame.find('img');

        img.css({
            'width': img.actual('width'),
            'height': img.actual('height')
        });

        if (s !== undefined)
            s();
        img.css({
            'width': '',
            'height': ''
        });
        if (c !== undefined)
            c();
        $.drop.method('center')(drop);
    },
    changePhoto: function (arg, fancyFrameInPH, href) {
        var drop = arg[1];
        fancyFrameInPH.parent().addClass('p_r');
        fancyFrameInPH.append('<div class="preloader"></div>');
        if (href == hrefOptions.curHref)
            fancyFrameInPH.find('.preloader').remove();
        hrefOptions.curHref = href;

        var img = new Image();
        img.onload = function () {
            drop.find('.drop-content .inside-padd').empty().append($(this).css('visibility', 'visible')).prepend('<span class="helper"></span>');

            var carGal = drop.find('.content-carousel');

            $.drop.method('limitSize')(drop);
            Product.resizePhoto(drop, function () {
                drop.drop('heightContent');
                drop.drop('center');
            });
            carGal.find('.jcarousel-item').eq($.inArray(hrefOptions.curHref, hrefOptions.thumbs)).focusin();

        };
        img.src = href;
    },
    beforeShowHref: function (el, drop, isajax) {
        var arg = arguments,
                cycle = hrefOptions.cycle,
                obj = $.extend({}, el.data(), hrefOptions, el.closest(genObj.parentBtnBuy).find(genObj.infoBut).data()),
                frame = $('#photo');
        frame.html(_.template($('#framePhotoProduct').html(), obj));

        var next = $(hrefOptions.next),
                prev = $(hrefOptions.prev),
                content = drop.find('.drop-content'),
                img = content.find('img');
        hrefOptions.curHref = img.attr('src');

        ShopFront.Cart.changeCount(frame.find(genObj.plusMinus));

        var fancyFrameInPH = content.find('.inside-padd');

        function condBtn(acA) {
            if (!cycle) {
                if (acA === 0)
                    prev.attr('disabled', 'disabled');
                if (acA === itemGalL - 1)
                    next.attr('disabled', 'disabled');
            }
        }
        function btnSClick(btn) {
            itemGal.removeClass('active');
            if (btn.is(prev))
                next.removeAttr('disabled');
            else
                prev.removeAttr('disabled');
            acA = ($.inArray(hrefOptions.curHref, hrefOptions.thumbs));
            if (btn.is(prev)) {
                if (cycle) {
                    if (acA !== 0)
                        acA = acA - 1;
                    else
                        acA = itemGalL - 1;
                }
                else {
                    if (acA !== 0)
                        acA = acA - 1;
                    if (acA === 0)
                        prev.attr('disabled', 'disabled');
                }
            }
            else {
                if (cycle) {
                    if (acA !== itemGalL - 1)
                        acA = acA + 1;
                    else
                        acA = 0;
                }
                else {
                    if (acA !== itemGalL - 1)
                        acA = acA + 1;
                    if (acA === itemGalL - 1)
                        next.attr('disabled', 'disabled');
                }
            }
            thumbsA.eq(acA).parent().addClass('active');
            Product.changePhoto(arg, fancyFrameInPH, hrefOptions.thumbs[acA]);
        }

        var carGal = drop.find('.content-carousel');

        if ($.existsN(carGal.find('.items-thumbs').children())) {
            carGal.closest('.horizontal-carousel').show();
            var itemGal = carGal.find('.items-thumbs > li').removeClass('active'),
                    itemGalL = itemGal.length,
                    thumbsA = itemGal.find('a');
            hrefOptions.thumbs = new Array();

            thumbsA.each(function () {
                hrefOptions.thumbs.push($(this).attr('href'));
            });

            var btns = prev.add(next).removeAttr('disabled').fadeIn(),
                    acA = ($.inArray(img.attr('src'), hrefOptions.thumbs));
            itemGal.eq(acA).addClass('active');

            var itemGalUl = itemGal.parent();
            if ($.existsN(itemGalUl.parent('.jcarousel-clip')))
                itemGalUl.unwrap();

            thumbsA.off('click').on('click', function (e) {
                btns.removeAttr('disabled');
                itemGal.removeClass('active');
                $(this).parent().addClass('active');
                var href = $(this).attr('href'),
                        acA = ($.inArray(href, hrefOptions.thumbs));
                e.preventDefault();
                Product.changePhoto(arg, fancyFrameInPH, href);
                condBtn(acA);
            });
            btns.off('click').on('click', function (e) {
                btnSClick($(this));
            });
            condBtn(acA);
        }
        else
            carGal.closest('.horizontal-carousel').hide();

        wnd.unbind('resize.photo').bind('resize.photo', function () {
            Product.resizePhoto(drop);
        });
    },
    afterClosedPhoto: function (el, drop) {
        drop.find('.addingphoto').remove();
    },
    onComplete: function (el, drop, isajax) {
        var carGal = drop.find('.content-carousel');
        drop.find('.drop-content img').css('visibility', 'visible');

        Product.resizePhoto(drop, function () {
            carGal.parent().myCarousel($.extend({}, carousel, {
                'adding': {
                    'start': $.inArray(hrefOptions.curHref, hrefOptions.thumbs)
                },
                after: function (el) {
                    el.find('ul').css('visibility', 'visible');
                }
            }));
        });
    }
};
function initPhoto() {
    if (productPhotoCZoom) {
        function margZoomLens() {
            $(genObj.photoProduct).find('img').each(function () {
                var $this = $(this),
                        mT = Math.ceil(($this.parent().outerHeight() - $this.height()) / 2),
                        mL = Math.ceil(($this.parent().outerWidth() - $this.width()) / 2);
                $('#forCloudZomm').empty().append('.cloud-zoom-lens{margin:' + mT + 'px 0 0 ' + mL + 'px;}.mousetrap{top:' + mT + 'px !important;left:' + mL + 'px !important;}');
            });
            $('.leftProduct').off('mouseover', '.mousetrap').on('mouseover', '.mousetrap', function () {
                var cloudzoomlens = $('.cloud-zoom-lens');
                if (cloudzoomlens.width() > $(genObj.photoProduct).width()) {
                    $(this).remove();
                    cloudzoomlens.remove();
                    $('#xBlock').empty();
                }
            });
        }
    }

    if (productPhotoCZoom) {
        $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom({showTitle: false});
        body.append('<style id="forCloudZomm"></style>');
        margZoomLens();
        $(genObj.photoProduct).find('img').load(function () {
            margZoomLens();
        });
    }
    $('.item-product .items-thumbs > li > a').on('click.thumb', function (e) {
        e.preventDefault();
        var $this = $(this),
                href = $this.attr('href');

        $this.parent().siblings().removeClass('active').end().addClass('active');
        $(genObj.photoProduct).attr('href', href).find('img').attr('src', href);
    });
    if (productPhotoDrop && productPhotoCZoom) {
        $('.leftProduct').on('click.mousetrap', '.mousetrap', function () {
            var $this = $(this).prev();
            $(this).data($.extend({
                'frame': $('.item-product'),
                'mainPhoto': $this.attr('href'),
                'title': $this.attr('title')
            }, optionsPhoto)).drop({
                scrollContent: false
            }).trigger('click.drop');
        });
    }
}
function initPhotoTrEv() {
}

$(document).ready(function() {
    initPhoto();
    cuselInit(body, '#variantSwitcher');
    cuselInit(body, '#delivery_type');
    cuselInit(body, '#payment_type');
});