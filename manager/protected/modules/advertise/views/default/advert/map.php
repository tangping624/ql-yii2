<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=NMbDcogfV2C46HQZXgLSvRgk"></script>
    <script type="text/javascript" src="/frontend/js/lib/jquery/jquery-1.11.2.min.js"></script>
    <style type="text/css">
        body, html {width: 100%;height: 100%;overflow: hidden;margin: 0;font-family:"Microsoft YaHei","Helvetica";font-size:14px;}
        #allmap {width: 100%;height: 100%;overflow: hidden;margin: 0;}
        .box{margin-bottom:5px;}
        #l-map {height: 100%;width: 100%;}
        /*.panel{border: 1px solid #C0C0C0; width: 150px; height: auto;}*/
        /*input{width:150px;}*/
        .inp{outline:none;background:transparent;vertical-align:top;-webkit-appearance:none;border:none;}
        .inp-pr{width:300px;border:1px solid #E7E7EB;margin-bottom:5px;padding:5px;margin-right:40px;}

        .btn-pr{padding: 6px 12px;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;border-radius: 4px;outline: none;border:1px solid #E7E6EB;background-color: #44B549;color:white;height:30px;}
        /*.btn-pr:hover{background-color:#E7E6EB;}*/
    </style>
</head>
<body>

<input type="hidden" id="longitude" value="0" />
<input type="hidden" id="latitude" value="0" />

<div class="box">
    <div id="r-result">
        请输入:
        <input type="text" id="suggestId" size="20" value="" class="inp inp-pr"/>
        <input type="button" id="ok" value="确定选择" class="btn-pr" />
    </div>
    <div id="searchResultPanel" class="panel"></div>
</div>

<div id="l-map"></div>

<script type="text/javascript">
(function() {
    var map = new BMap.Map("l-map");

    function configMap() {
        map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
        map.addControl(new BMap.NavigationControl({ anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN }));  //左下角，仅包含平移按钮
        //启用滚轮放大缩小，默认禁用
        map.enableScrollWheelZoom();

        var iniPoint = getPos();
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
            "input": "suggestId",
            "location": map
        });

        //鼠标点击下拉列表后的事件
        ac.addEventListener("onconfirm", function (e) {
            var _value = e.item.value;
            var address = _value.province + _value.city + _value.district + _value.street + _value.business;
            setPlace(address);
        });
    }

    function setPlace(address) {
        //清除地图上所有覆盖物
        map.clearOverlays();
        //智能搜索
        var local = new BMap.LocalSearch(map, {
            onSearchComplete: function () { markSearchResult(local); }
        });
        local.search(address);
    }

    function markSearchResult(search) {
        //获取第一个智能搜索的结果
        var pp = search.getResults().getPoi(0).point;
        addMarker(pp);
    }

    function setPos() {
        if(top && top.Proxy) {
            var pos = top.Proxy.getPos();
            if(pos) {
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
        if(top && top.Proxy) {
            top.Proxy.setPos(getPos());
            top.Proxy.setAddr(getAddr());
            top.Proxy.dialog.close().remove();
        }
    });
})();
</script>

</body>
</html>