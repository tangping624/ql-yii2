/**
 * Created by FUYL on 2015/3/19.
 */
jQuery(function ($) {
    var getQuery = function () {
        var str = location.search.replace("?", ""), tempArr,
            obj = {}, temp, arr = str.split("&"), len = arr.length;

        if (len > 0) {
            for (var i = 0; i < len; i++) {
                try {
                    if ((tempArr = arr[i].split('=')).length === 2) {
                        temp = decodeURIComponent(tempArr[1]);
                        obj[tempArr[0]] = temp;
                    }
                } catch (e) {
                }
            }
        }

        return obj;
    };

    var tranparams = getQuery();

    var init = function () {
        bindEvent();
        refreshProject();
    };
    var bindEvent = function () {
        $("#search").on("click", refreshProject);
        $("#projectList").on("change", onProjectSelected);
        $("#unitList").on("change", onUnitSelected);
        $("#roomList").on("change", onRoomSelected);
        $("#roomList").on('dblclick', 'option', {is_custom:1} ,confirmSelection);
        $('#btn_ok').on('click', {is_custom:1} , confirmSelection);
    };

    function onProjectSelected() {
        refreshUnits();
        showSelectionText();
    }

    function onUnitSelected() {
        refreshRooms();
        showSelectionText();
    }

    function onRoomSelected() {
        showSelectionText();
    }

    var $keyword = $("#keyword");

    function refreshProject() {
        $("#projectList,#unitList,#roomList").empty();
        if (tranparams.projectId != null && tranparams.projectId != '') {
            $('#room_search').hide();
        }

        var val = $.trim($keyword.val());
        val = val === $keyword.attr('placeholder') ? '' : val;

        ajax("/widgets/getprojects",
            {"keyword": val, 'projectId': tranparams.projectId},
            function (data) {
                refreshList("projectList", data, function (option, item) {
                    if (item.corp_id)
                    {
                        option.attr("corp_id", item.corp_id);
                    }

                    if (item.corp_name)
                    {
                        option.attr("corp_name", item.corp_name);
                    }

                });

                if ($('#projectList option').length > 0) {
                    $('#projectList option:first').attr("selected", "selected");
                    onProjectSelected();
                }
            });
    }

    function refreshUnits() {
        $("#unitList,#roomList").empty();
        var projectId = $("#projectList option:selected").val();
        ajax("/widgets/getbuildingunits", {"projectid": projectId}, function (data) {
            refreshList("unitList", data, function (option, item) {
                option.attr("unit", item.unit);
                option.attr("buildingId", item.buildingId);
                if (item.unit) {
                    option.text(item.name + "-" + item.unit);
                }

            });
        });
    }

    function getUrlParamValue(paramName){
            var url = window.location.href;
            if (url.indexOf('?') == -1) {
                return null;
            }
            var paramString = url.split('?')[1];
            var paramList = paramString.split('&');
            for (var i = 0; i < paramList.length; i++) {
                var paramPair = paramList[i].split('=');
                if (paramPair.length == 2 && paramPair[0] == paramName) {
                    return paramPair[1];
                }
            }

        return null;
    }

    function refreshRooms() {
        $("#roomList").empty();
        var selectedUnit = $("#unitList option:selected");
        var buildingid = selectedUnit.attr("buildingId");
        var unit = selectedUnit.attr("unit");

        var param = {"buildingid": buildingid, "unit": unit};
        var virtual = getUrlParamValue('virtual');
        if(virtual === '0'){
            param['is_virtual'] = '0';
        }
        ajax("/widgets/getrooms", param, function (data) {
            refreshList("roomList", data, function (option, item) {
                if (item.customer_names) {
                    option.text(item.name + " " + item.customer_names);
                }

                if (item.building_id)
                {
                    option.attr("building_id", item.building_id);
                }

                if (item.floorname)
                {
                    option.attr("floor_name", item.floor_name);
                }

                if (item.phones)
                {
                    option.attr("phones", item.phones);
                }

                if (item.customer_ids)
                {
                    option.attr("customer_ids", item.customer_ids);
                }

                if (item.customer_names)
                {
                    option.attr("customer_names", item.customer_names);
                }

            });
        });
    }

    function refreshList(listId, data, appendItemFunc) {
        var pl = $("#" + listId);
        pl.empty();
        for (var i = 0; i < data.length; i++) {
            var option = $("<option></option>");
            option.val(data[i].id);
            option.text(data[i].name);
            if (appendItemFunc) {
                appendItemFunc(option, data[i]);
            }
            pl.append(option);
        }
    }

    function ajaxError(errorInfo) {
        tip('请求服务数据出错！');
    }

    function ajax(url, data, success) {
        $.ajax({
            url: path(url),
            async: false,
            success: success,
            data: data,
            dataType: "json",
            error: ajaxError
        });
    }

    function hide(selection) {
        $(selection).addClass("hide");
    }

    function show(selection) {
        $(selection).removeClass("hide");
    }

    function showSelectionText() {
        $("#selectionText").text("");
        var project = $("#projectList option:selected");
        if (project.length == 0) {
            return;
        }

        var unit = $("#unitList option:selected");
        var room = $("#roomList option:selected");
        if (room.length > 0) {
            $("#selectionText").text(project.text() + "-" + unit.text() + "-" + room.text().split(" ")[0]);
        }
        else if (unit.length > 0) {
            $("#selectionText").text(project.text() + "-" + unit.text());
        }
        else {
            selectionLevel = "Project";
            selectionId = project.val();
            $("#selectionText").text(project.text());
        }
    }

    function tip(msg, isNormal) {
        if (window.top.topTips) {
            window.top.topTips({mode: isNormal ? 'normal' : 'warning', tip_text: msg});
        } else {
            alert(msg);
        }
    }

    function path(url) {
        var token = tranparams.___token;
        return '/' + token + (url.charAt(0) === '/' ? '' : '/') + url;
    }

    function confirmSelection(e) {
        if(e.data && !e.data.is_custom){
            return;
        }
        var project = $("#projectList option:selected");
        if (project.length == 0 || project.length > 1) {
            tip('请选择项目！');
            return;
        }

        var selectType = tranparams.selectType;

        var room = $("#roomList option:selected");
        if (selectType == "room" && (room.length == 0 || room.length > 1)) {
            tip('请选择房间！');
            return;
        }

        var unit = $("#unitList option:selected");
        if(unit.length>1){
            tip('请选择楼栋！');
            return;
        }

        var selectionLevel;
        var selectionId;
        var selectionName;

        var building_id = "";
        var unit_id = "";
        var room_id = "";
        var project_name = "";
        var unit_name = "";
        var room_name = "";
        var requester = "";

        if (room.length > 0) {
            selectionLevel = "Room";
            selectionId = room.val();
            selectionName = project.text() + "-" + unit.text() + "-" + room.text().split(" ")[0];
            building_id = room.attr("building_id");
            unit_id = unit.val();
            room_id = room.val();
            project_name = project.text();
            unit_name = unit.attr("unit");
            room_name = room.text().split(" ")[0];

            var roomuserarr = room.text().split(" ");
            if (roomuserarr.length > 0) {
                var roomuser = roomuserarr[1];
                requester = (roomuser || '').split(";")[0];
            }


        }
        else if (unit.length > 0) {
            selectionLevel = "Unit";
            selectionId = unit.val();
            selectionName = project.text() + "-" + unit.text();

            building_id = unit.attr("buildingId");
            unit_id = unit.val();
            project_name = project.text();
            unit_name = unit.attr("unit");
        }
        else {
            selectionLevel = "Project";
            selectionId = project.val();
            selectionName = project.text();
            project_name = project.text();
        }

        var data = {
            "id": selectionId, // 当前选项的id
            "name": selectionName, //当前选项的名称
            "level": selectionLevel, // 层级
            "corp_id": project.attr("corp_id"), //公司id
            "corp_name": project.attr("corp_name"), //公司名称
            "project_id": project.val(), //项目id
            "project_name": project_name, //项目名称
            "building_id": building_id, //楼栋ID
            "unit_id": unit_id, //单元ID
            "unit_name": unit_name, //单元名称
            "floor_name": room.attr("floor_name"), //楼层名称
            "room_id": room_id, //房间ID
            "room_name": room_name, //房间名称
            // "requester": requester, //服务请求人，默认选择第一个客户名称
            "phones": room.attr("phones"), //电话
            "first_owner_id": room.attr("customer_ids") ? room.attr("customer_ids").split(';')[0] : '',
            "first_owner_name": room.attr("customer_names") ? room.attr("customer_names").split(';')[0] : ''
        };

        data = $.extend(tranparams, data);
        parent && parent.ProxyRoom && parent.ProxyRoom.ok(data);
    }

    init();
});
