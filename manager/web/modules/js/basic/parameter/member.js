/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define,plupload*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    /*require('../../../../frontend/js/plugin/form');
    require('../../../../frontend/js/lib/validate');*/
    window.DataValid = require('/frontend/js/lib/validate');
    require('../../../../frontend/js/plugin/colorpicker.js');
    require('../../../../frontend/js/lib/jquery.ui/jquery.sortable');


    //tpl
    var addTpl = $('#add_tpl').html();
    var _validate = new DataValid('<p style="color:red;">{errorHtml}</p>');
    var imgTpl = '<div class="img-wrap"><div class="img-box"><div class="box-inner"><p class="wait">等待中...</p><div class="per"><span class="pct"></span></div></div></div></div>';
    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    //初始化上传
    var initUpload = function () {
        var imgArea = $('.uploadimg-wrap'),
            imgWrap = imgArea.find('.img-wraps');
        var _uploader = utils.upload({
            runtimes: 'html5,flash,silverlight,html4',
            browse_button: 'upload_btn',
            container: imgArea.get(0),
            url: O.path('/basic/upload/upload-image'),
            flash_swf_url: '/frontend/3rd/plupload/Moxie.swf',
            silverlight_xap_url: '/frontend/3rd/plupload/Moxie.xap',
            filters: [{title: '图片文件', extensions: 'jpg,png,gif'}],
            init: {
                FilesAdded: function (up, files) {
                    var flag = false;
                    plupload.each(files, function (file) {
                        if (file.size / 1024 > 512) {
                            flag = 'size';
                            return false;
                        }
                        if (['image/jpeg', 'image/png', 'image/gif'].indexOf(file.type) < 0) {
                            flag = 'type';
                            return false;
                        }
                    });
                    if (flag) {
                        plupload.each(files, function (file) {
                            up.removeFile(file);
                        });
                    }
                    if (flag === 'size') {
                        tips('每张最大支持512k', 'tips');
                        return;
                    }
                    if (flag === 'type') {
                        tips('只支持jpg/gif/png格式的图片', 'tips');
                        return;
                    }
                    plupload.each(files, function (file) {
                        imgWrap.html(imgTpl);
                    });
                    _uploader.start();
                },
                FileUploaded: function (up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    if (data.status - 0 === 0 || !data.original) {
                        tips('上传失败', 'tips');
                    } else {
                        imgWrap.html('<img src="' + data.original + '" />');
                        $('<div class="card-no" id="card_no">NO.888888888888</div>')
                            .css('color',$('#card_no_color').val())
                            .addClass($('.form-radio.selected').data('position')).appendTo(imgWrap);
                        $('#img_url').attr('value', data.original).val(data.original).keyup();
                    }
                    _uploader.refresh();
                },
                UploadProgress: function (up, file) {
                    imgArea.find('.wait').text(file.percent + '%');
                    imgArea.find('.pct').css('width', file.percent + '%');
                },
                Error: function (up, err) {
                    if (err.code === -601) {
                        tips('只支持jpg/gif/png格式的图片', 'tips');
                    } else {
                        tips(err.message, 'tips');
                    }
                }
            }
        });
        _uploader.init();
    };

    var location_map = {
        '左上': 'left-top',
        '左下': 'left-bottom',
        '右上': 'right-top',
        '右下': 'right-bottom'
    };

    var add = function (data) {
        var newSort = 0;
        if (data) {
            data.card_no_location = location_map[data.card_no_location || '左上'] || data.card_no_location;
        } else {
            var sortList = $('.sort-num'),
                num = [];
            sortList.each(function (i) {
                num.push(sortList.eq(i).text() - 0);
            });
            newSort = num.length ? Math.max.apply(this, num) + 1 : 1;
        }

        var box = $.box({
            content: template(addTpl, data || {
                    id: '',
                    sort: newSort || '',
                    name: '',
                    score:'',
                    is_default: '',
                    is_default_landlord: '',
                    img_url: '',
                    privilege: '',
                    card_no_color: '#000000',
                    card_no_location: location_map['左上']
                }),
            title: data ? '修改会员等级' : '新增会员等级',
            height: 'auto',
            width: 800
        });

        var wrap = $(box.node);

        var addForm = wrap.find('#add_form'),
            $is_default = wrap.find('#is_default'),
            $is_default_landlord = wrap.find('#is_default_landlord'),
            $card_no_color = wrap.find('#card_no_color');
            
        //验证配置、规则
        var _checkCfg = {
            config: function() {
                return [{
                            id: 'name',
                            rules: 'required',
                            ruleMsg: {'required': '请填写级别名称'}
                        },{
                            id: 'score',
                            rules: 'required',
                            ruleMsg: {'required': '请填写会员级别对应积分'}
                        },{
                            id: 'img_url',
                            rules: 'required',
                            ruleMsg: {'required': '请上传会员卡图片'}
                        },{
                            id: 'privilege',
                            rules: 'required',
                            ruleMsg: {'required': '请填写级别权益'}
                        }]
                }
        };
        var _doCheck = function() {
            // if(_upNum < $('.img-wrap').length) {
            //     _showTips('还有未上传成功的图片，不能立即保存');
            //     return false;
            // }
            if (_validate.fieldList.length === 0) {
                _validate.addFields(_checkCfg.config());
            }

            if (!_validate.process(false)) {
                var id = _validate.errorField.split(',')[1];
                $('#' + id)[0].scrollIntoView();//之后添加效果
                return false;
            }
            return true;
        };
        $('#submit_btn').on('click', function() {
            if(_doCheck()) {
               var data = _getData();
               O.ajaxEx({
                    url: O.path('basic/member-level/check-level-name'),
                    type: 'get',
                    data: {
                        name: data.name,
                        id: data.id
                    }
                }).then(function (res) {
                    if (res.result) {
                        return O.ajaxEx({
                            url: O.path('basic/member-level/edit'),
                            type: 'post',
                            data: data
                        });
                    } else {
                        $('#name').after('<p class="form-error" id="datavalid_null_name_error">' + res.msg + '</p>');
                        return res;
                    }
                }).then(function (res) {
                    if (res.result && res.data) {
                        box.close();
                        box.remove();
                        tips(data ? '修改成功！' : '添加成功！');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                       
                        tips(res.msg, 'tips');
                    }
                });
            }else{
                return false;
            }
        }); 
        var _getData = function() {
            var seri = '';//图片地址的上传 
            seri = O.serialize(addForm, 2);
            seri.is_default = $is_default.hasClass('selected') ? 1 : 0;
            return seri;
        };

    // }
        addForm.on('click', '.form-radio', function () {
            $(box.node).find('#card_no').removeClass().addClass('card-no').addClass($(this).data('position'));
        });

        addForm.on('click','.form-checkbox',function(e){
            e.preventDefault();// 防止事件冒泡，触发2次
            var $this = $(this);
            if($this.hasClass('readonly') || $this.hasClass('disabled')) return;

            var $checkbox = $this.find('input[type="checkbox"]'); 
            var flag = false;
            if(!$this.hasClass('selected')) {
                flag = true;
                $this.addClass('selected');
                $checkbox.prop('checked', flag).attr('checked', flag);
                tips('设定为默认等级后不允许删除', 'tips');
            } else {
                flag = false;
                $this.removeClass('selected');
                $checkbox.prop('checked', flag).attr('checked', flag);
            }
            O.emit('checkbox', $checkbox, $this, flag);
        }); 

        $(box.node).find('#color_picker').colorPicker({
            renderDom: '>div',
            onSelect: function (color) {
                $card_no_color.val(color);
                $(box.node).find('#card_no').css('color', color);
            }
        });

        $(box.node).off('click').on('click', '.art-box-footer .btn-secondary', function () {
            box.close().remove();
        });

        initUpload();
    };


    $('.member-form').off('click').on('click', '.opt-btn', function () {
        var $this = $(this);
        if ($this.hasClass('opt-del')) {
            $this.tipsLayer('确定删除？', function () {
                O.ajaxEx({
                    url: O.path('basic/member-level/delete'),
                    type: 'post',
                    data: {
                        id: $this.data('id')
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips('删除成功');
                        var row = $this.closest('tr');
                        row.fadeOut(function () {
                            row.remove();
                        });
                    } else {
                        tips(res.msg, 'tips');
                    }
                });
            });
        } else if ($this.hasClass('add-member-level')) {
            add();
        } else if ($this.hasClass('opt-dosort')) {
            $this.hide();
            $('.opt-sort').show();
            $('.opt-finish').show();
            $('.opt-docancle').show();
            $('.opt-del').hide();
            $('.opt-modify').hide();
            $('.sort_list').sortable({item: 'tr', placeholder: 'sortable-placeholder'}).sortable('enable');
        } else if ($this.hasClass('opt-docancle')) {
            $('.sort_list').sortable('disable');
            $('.opt-hide').css('display', 'none');
            $('.opt-dosort').show();
            $('.opt-del').show();
            $('.opt-modify').show();
        } else if ($this.hasClass('opt-finish')) {
            var sortList = [];
            $.each($('.sort_list tr'), function () {
                sortList.push({
                    id: $(this).find('a.opt-sort').data('id'),
                    sort: $(this).index() + 1
                });
                
            });
            O.ajaxEx({
                url: O.path('basic/member-level/sort'),
                data: {ids: JSON.stringify(sortList)},
                type: 'post',
               
            }).then(function (res) {
                if (res.result) {
                    tips(res.msg);
                    window.location.reload();
                } else {
                    tips(res.msg, 'tips');
                    $('.opt-hide').css('display', 'none');
                    $('.opt-dosort').show();
                    $('.opt-del').show();
                    $('.opt-modify').show();
                    $('.sort_list').sortable('disable');
                }
            });
        } else {
            add($this.data());
        }
    });
});