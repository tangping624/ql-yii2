<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="../css/bootstrap/dist/css/bootstrap.min.css">
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=NMbDcogfV2C46HQZXgLSvRgk"></script>
    <script type="text/javascript" src="../js/lib/jquery/jquery-1.11.2.min.js"></script>
    <style type="text/css">
        html,body { background: #FFF;}
        .art-box * {box-sizing: border-box;}
        .art-box,
        .art-box-content { height: 100%; width: 100%;}
        .content-wrap { height: 100%; width: 100%; padding-top: 42px;}
    </style>
</head>
<body>

<input type="hidden" id="longitude" value="0"/>
<input type="hidden" id="latitude" value="0"/>

<div class="art-box">
    <div class="art-box-content">
        <label for="suggestId" class="pull-left" style="padding-top: 5px; margin-right: 10px;">请输入</label>
        <input type="text" id="suggestId" size="20" value="" class="form-control width-long pull-left" style=" margin-right: 10px;"/>
        <button class="btn btn-primary pull-left" id="ok">确定选择</button>
        <div id="searchResultPanel"></div>

        <div class="content-wrap">
            <div id="b_map" style="height: 100%;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function () {
        var map = window.map = new BMap.Map("b_map");

        function configMap() {
            map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
            map.addControl(new BMap.NavigationControl({
                anchor: BMAP_ANCHOR_BOTTOM_LEFT,
                type: BMAP_NAVIGATION_CONTROL_PAN
            }));  //左下角，仅包含平移按钮
            //启用滚轮放大缩小，默认禁用
            map.enableScrollWheelZoom();

            var iniPoint = getPos(),
                geo = new BMap.Geocoder();

            if (iniPoint[0]) {
                var point = new BMap.Point(iniPoint[0], iniPoint[1]);
                addMarker(point);

            } else {
                var myCity = new BMap.LocalCity();
                myCity.get(function (result) {
                    setPlace(result.name);
                });

            }

            //监听鼠标点击事件
            map.addEventListener("click", function (e) {
                map.clearOverlays();
                var point = new BMap.Point(e.point.lng, e.point.lat);
                addMarker(point);
                geo.getLocation(point, function(e){
                    $('#suggestId').val(e.address || '');
                })
            });
        }

        //定位并创建标注
        function addMarker(point) {
            var marker = new BMap.Marker(point);
            map.centerAndZoom(point, 15);
            map.addOverlay(marker);
            $("#longitude").val(point.lng);
            $("#latitude").val(point.lat);
        }

        function setAutoComplete() {
            //建立一个自动完成的对象
            var ac = new BMap.Autocomplete({
                input: "suggestId",
                location: map
            });

            if (top && top.Proxy) {
                ac.setInputValue(top.Proxy.getAddr());
            }

            //鼠标点击下拉列表后的事件
            ac.addEventListener("onconfirm", function (e) {
                var _value = e.item.value;
                var address = _value.province + _value.city + _value.district + _value.street + _value.business;
                //移动光标
                $("#suggestId").val(address);
                setPlace(address);
            });
        }

        function setPlace(address) {
            //清除地图上所有覆盖物
            map.clearOverlays();
            //智能搜索
            var local = new BMap.LocalSearch(map, {
                onSearchComplete: function () {
                    markSearchResult(local);
                }
            });
            local.search(address);
        }

        function markSearchResult(search) {
            //获取第一个智能搜索的结果
            var pp = search.getResults().getPoi(0).point;
            addMarker(pp);
        }

        function setPos() {
            if (top && top.Proxy) {
                var pos = top.Proxy.getPos();
                if (pos) {
                    $("#longitude").val(pos[0]);
                    $("#latitude").val(pos[1]);
                }
            }
        }

        function getPos() {
            return [
                $.trim($("#longitude").val()),
                $.trim($("#latitude").val())
            ]
        }

        function getAddr() {
            return $.trim($("#suggestId").val());
        }

        function init() {
            setPos();
            configMap();
            setAutoComplete();
        }

        init();

        $("#ok").on("click", function () {
            if (top && top.Proxy) {
                top.Proxy.setPos(getPos());
                top.Proxy.setAddr(getAddr());
                top.Proxy.change();
                top.Proxy.dialog.close().remove();
            }
        });
    })();
</script>

</body>
</html>
