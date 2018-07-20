
define(function (require, exports, module) {
    var cityData = require('/modules/js/public/plugin/select-city-data.js');

    /**
     * 打包valueChange事件
     * @type {{teardown: Function, handler: Function, add: Function, triggerChanged: Function}}
     */
    $.event.special.valuechange = {
        teardown: function (namespaces) {
            $(this).unbind('.valuechange');
        },

        handler: function (e) {
            $.event.special.valuechange.triggerChanged($(this));
        },

        add: function (obj) {
            $(this).on('keyup.valuechange cut.valuechange paste.valuechange input.valuechange', obj.selector, $.event.special.valuechange.handler)
        },
        triggerChanged: function (element) {
            var current = element[0].contentEditable === 'true' ? element.html() : element.val()
                , previous = typeof element.data('previous') === 'undefined' ? element[0].defaultValue : element.data('previous')
            if (current !== previous) {
                element.trigger('valuechange', [element.data('previous')])
                element.data('previous', current)
            }
        }
    }

    jQuery.selectCity = function (options) {
        var defaults = {
            hotShowCount: 10,
            searchShowCount: 10,
            searchDelay: 300
        };
        options = $.extend({}, defaults, options);
        var $this = $('#' + options.inputId);
        if ($this) {
            var o = new selectCity($this, options);
            o.init()
            return o;
        } else {
            return null;
        }
    }

    function selectCity(o, options) {
        this.o = o;
        this.options = options;
    }

    selectCity.prototype = {
        selectPanel: null,
        hotCityPanel: null,
        searchCityPanel: null,
        searchTimeOut: null,
        classObj: {
            jCityItem: 'j_sc_city',
            selectPanel: 'select-city-control',
            hotCityPanel: 'hot-city',
            searchCityPanel: 'search-city',
            cityItem: 'city-item',
            citySelectedItem: 'curr',
            noneItem: 'no-data'
        },
        perSCKeyword: '',
        perHasSCResult: false,
        curHotCityCount: 0,
        curSearchCityCount: 0,
        curCityCount: 0,
        curCitySelectedIndex: -1,
        curCityPanel: null,
        allWindowArray: [],
        documentClickFunc: null,
        init: function () {
            if (!this.o) {
                return;
            }
            this.initDefaultCity();
            this.bindInputSearchEvents();
        },
        initCity: function () {
            this.destroy();
            this.initSelectPanel();
            this.bindCityPanelEvents(this.hotCityPanel);
            this.bindCityPanelEvents(this.searchCityPanel);
            this.allWindowArray = [];
            this.getAllParentWindows(window.self);
            var _this = this;
            this.documentClickFunc = function (e) {
                _this.documentClickDestroy(e);
            };
            for (var i = 0; i < this.allWindowArray.length; i++) {
                $(this.allWindowArray[i].document).on('click', this.documentClickFunc);
            }
        },
        /**
         * 销毁面板
         */
        destroy: function () {
            if (this.selectPanel) {
                clearTimeout(this.searchTimeOut);
                this.checkValue();
                this.selectPanel.remove();
                this.selectPanel = null;
                var _this = this;
                if (this.allWindowArray.length > 0) {
                    for (var i = 0; i < this.allWindowArray.length; i++) {
                        $(this.allWindowArray[i].document).off('click', this.documentClickFunc);
                    }
                }
            }
        },
        /**
         * 初始化选择面板
         */
        initSelectPanel: function () {
            this.hotCityPanel = new jQuery('<ul class="' + this.classObj.hotCityPanel + '" style="display: none;"></ul>');
            this.searchCityPanel = new jQuery('<ul class="' + this.classObj.searchCityPanel + '"  style="display: none;"></ul>');
            this.selectPanel = new jQuery('<div class="' + this.classObj.selectPanel + '"  style="display: none;position: absolute;z-index:9999"></div>');
            this.selectPanel.append(this.hotCityPanel).append(this.searchCityPanel);
            this.selectPanel.appendTo($(window.top.document.body));
            this.loadHotCity();
        },
        /**
         * 初始化默认值
         */
        initDefaultCity: function () {
            var cityName = this.o.val();
            if (cityName) {
                var idx = -1;
                for (var i = 0; i < cityData.cityArray.length; i++) {
                    if (cityData.cityArray[i].name.indexOf(cityName) != -1) {
                        this.o.data('cityIndex', i);
                        this.o.data('previous', cityName);
                        idx = 1;
                        break;
                    }
                }
                if (idx === -1) {
                    this.o.val('');
                    this.o.data('cityIndex', -1);
                }
            }
        },
        /**
         * 加载热门城市
         */
        loadHotCity: function () {
            var tempElArray = [];
            for (var i = 0; i < cityData.hotCityIndex.length; i++) {
                if (tempElArray.length >= this.options.hotShowCount) {
                    break;
                }
                var city = cityData.cityArray[cityData.hotCityIndex[i]];
                tempElArray.push('<li class="' + this.classObj.jCityItem + ' ' + this.classObj.cityItem + '" c_idx="' + cityData.hotCityIndex[i] + '">' + city.name + '<span>' + city.pinyin + '</span></li>');
            }
            this.curHotCityCount = tempElArray.length;
            this.hotCityPanel.html('').append(tempElArray.join(''));
        },
        /**
         * 搜索城市
         * @param keyword
         */
        searchCity: function (keyword) {
            this.o.data('cityIndex', '-1');
            var searchKeyword = keyword.toLowerCase();
            var tempElArray = [];
            //如果没有上一结果没有搜索到，则直接显示没有结果
            if (!(this.perSCKeyword !== "" && searchKeyword.indexOf(this.perSCKeyword) === 0 && this.perHasSCResult === false)) {
                for (var i = 0; i < cityData.cityArray.length; i++) {
                    if (tempElArray.length >= this.options.searchShowCount) {
                        break;
                    }
                    var city = cityData.cityArray[i];
                    if (city.label.toLowerCase().indexOf(searchKeyword) !== -1) {
                        tempElArray.push('<li class="' + this.classObj.jCityItem + ' ' + this.classObj.cityItem + '" c_idx="' + i + '">' + city.name + '<span>' + city.pinyin + '</span></li>');
                    }
                }
            }
            this.perSCKeyword = searchKeyword;
            if (tempElArray.length === 0) {
                tempElArray.push('<li class="' + this.classObj.jCityItem + ' ' + this.classObj.noneItem + '">没找到相关城市</li>');
                this.perHasSCResult = false;
                this.curSearchCityCount = 0;
            } else {
                this.curSearchCityCount = tempElArray.length;
                this.perHasSCResult = true;
            }
            this.selectPanel.show();
            this.hotCityPanel.hide();
            this.searchCityPanel.html('').append(tempElArray.join('')).show();
            this.curCitySelectedIndex = -1;
            this.curCityCount = this.curSearchCityCount
            this.curCityPanel = this.searchCityPanel;
        },
        checkValue: function () {
            this.perSCKeyword = '';
            var tempData = $._data(this.o[0]).data;
            if (tempData && tempData.previous && parseInt(tempData.cityIndex) === -1) {
                tempData.previous = '';
            }
            var temp_c_idx = parseInt(this.o.data('cityIndex'));
            if (temp_c_idx === -1) {
                this.o.val('');
            }
        },
        bindInputSearchEvents: function () {
            var _this = this;
            _this.o.on('valuechange', function (e, previous) {
                var inputText = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
                clearTimeout(_this.searchTimeOut);
                if (inputText) {
                    _this.searchTimeOut = setTimeout(function () {
                        _this.searchCity(inputText)
                    }, _this.options.searchDelay);
                } else {
                    _this.curCityCount = _this.curHotCityCount;
                    _this.curCityPanel = _this.hotCityPanel;
                    _this.curCitySelectedIndex = -1;
                    //显示热门城市
                    _this.searchCityPanel.hide();
                    _this.hotCityPanel.find('.' + _this.classObj.cityItem).removeClass(_this.classObj.citySelectedItem);
                    _this.hotCityPanel.show();
                }
            });
            _this.o.on('keydown', function (e) {
                if ((e.keyCode !== 13 && e.keyCode !== 38 && e.keyCode !== 40)
                    || _this.curCityPanel.is(":visible") === false
                    || _this.curCityCount === 0) {
                    return;
                }
                if (e.keyCode === 13) {
                    _this.curCityPanel && _this.curCityPanel.find("li[class*='" + _this.classObj.citySelectedItem + "']").eq(0).trigger('click');
                    return;
                } else {
                    var curIndex = _this.getCurKeyMoveCityItemIndex(e.keyCode === 40 ? 'down' : 'up');
                    var curItem = _this.curCityPanel.find('li').eq(curIndex);
                    curItem.siblings().removeClass(_this.classObj.citySelectedItem);
                    curItem.removeClass(_this.classObj.citySelectedItem).addClass(_this.classObj.citySelectedItem);
                }
            });
            _this.o.on('focus', function () {
                _this.initCity();
                _this.resetSelectCityShowPosition();
                _this.selectPanel.show();
                _this.hotCityPanel.show();
                _this.curCityCount = _this.curHotCityCount;
                _this.curCityPanel = _this.hotCityPanel;
                _this.curCitySelectedIndex = -1;
            });
            $(window.top).resize(function () {
                _this.resetSelectCityShowPosition();
            });

            $(window.self).on('unload', function () {
                _this.destroy();
            });
        },
        documentClickDestroy: function (e) {
            if (e.target != this.o[0] && $(e.target).closest('.' + this.classObj.jCityItem).length === 0) {
                this.destroy();
            }
        },
        bindCityPanelEvents: function ($panel) {
            var _this = this;
            $panel.on('click', '.' + this.classObj.jCityItem, function () {
                var cItemEL = $(this);
                if (cItemEL.attr('c_idx')) {
                    var c_idx = parseInt(cItemEL.attr('c_idx'));
                    var city = cityData.cityArray[c_idx];
                    _this.o.val(city.name);
                    _this.o.data('cityIndex', c_idx);
                    _this.o.data('previous', city.name);
                    _this.o.blur();
                    _this.destroy();
                }
            });
        },
        getCurKeyMoveCityItemIndex: function (mode) {
            if (mode === 'up') {
                this.curCitySelectedIndex--;
                if (this.curCitySelectedIndex <= -1) {
                    this.curCitySelectedIndex = this.curCityCount - 1;
                }
            }
            else if (mode === 'down') {
                this.curCitySelectedIndex++;
                if (this.curCitySelectedIndex >= this.curCityCount) {
                    this.curCitySelectedIndex = 0;
                }
            }
            else {
                this.curCitySelectedIndex = this.curCitySelectedIndex <= -1 ? 0 : this.curCitySelectedIndex;
            }
            return this.curCitySelectedIndex;
        },
        getSelectCityOuterPosition: function () {
            var offset = this.o.offset();
            return {top: offset.top + this.o.outerHeight(), left: offset.left, width: this.o.outerWidth()};
        },
        /**
         * 重新设置选择城市显示位置
         */
        resetSelectCityShowPosition: function () {
            if (this.selectPanel === null) {
                return;
            }
            var position = null;
            if (window.top != window.self && this.allWindowArray.length > 0) {
                position = this.getSelectCityOuterPosition();
                for (var i = 0; i < this.allWindowArray.length; i++) {
                    if (this.allWindowArray[i].frameElement) {
                        var tempOffset = $(this.allWindowArray[i].frameElement).offset();
                        position.top += tempOffset.top;
                        position.left += tempOffset.left;
                    }
                }
            } else {
                position = this.getSelectCityOuterPosition();
            }
            this.selectPanel.css({
                'top': position.top + 'px',
                'left': position.left + 'px',
                'width': position.width < 150 ? 150 : position.width
            });
        },
        /**
         * 获取所有父级窗体
         * @param winArray
         * @param curWindow
         * @returns {*}
         */
        getAllParentWindows: function (curWindow) {

            if (curWindow == window.top) {
                this.allWindowArray.push(curWindow);
                return;
            }
            this.allWindowArray.push(curWindow);
            this.getAllParentWindows(curWindow.parent.window);
        }
    };
});
