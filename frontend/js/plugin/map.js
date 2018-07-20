define(function (require, exports, module) {
    require('../lib/dialog');

    jQuery.fn.bmap = function (options) {
        options = options || {};
        var el = $(this);
        el.on('click', options.button || '.openmap', function () {
            var $proLon = el.find('.map-longitude'),
                $prlLat = el.find('.map-dimensionality'),
                $proAddr = el.find('.map-address'),
                $proLon_placeholder = $proLon.attr('placeholder'),
                $prlLat_placeholder = $prlLat.attr('placeholder'),
                $proAddr_placeholder = $proAddr.attr('placeholder');

            var _proxy = window.Proxy = {
                dialog: null,
                getPos: function () {
                    return [
                        $.trim($proLon.val() == $proLon_placeholder ? 0 : $proLon.val()) || 0,
                        $.trim($prlLat.val() == $prlLat_placeholder ? 0 : $prlLat.val()) || 0
                    ];
                },
                setPos: function (pos) {
                    if (pos[0]) {
                        $proLon.val(pos[0]);
                        $prlLat.val(pos[1]);
                    }
                },
                getAddr: function () {
                    return $proAddr.val() == $proAddr_placeholder ? '' : $proAddr.val();
                },
                setAddr: function (addr) {
                    addr && $proAddr.val(addr);
                },
                change: function () {
                    options.change && options.change.call(el);
                }
            };
            _proxy.dialog = $.dialog({
                url: '/frontend/inc/map.php',
                title: '设置坐标',
                id: 'js_map',
                skin: 'art-box',
                width: 750,
                height: 450,
                onshow: function () {
                }
            }).showModal();
        });
    };
});
