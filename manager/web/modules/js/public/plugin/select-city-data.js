
define(function (require, exports, module) {
    return {
        hotCityIndex: [0, 1, 2, 3, 5, 9, 10, 12, 36, 43],
        cityArray: [
            {
                "label": "北京|Beijing|BJ|010",
                "name": "北京",
                "pinyin": "Beijing",
                "zip": "010",
                "szm": "BJ"
            },
            {
                "label": "重庆|Chongqing|CQ|023",
                "name": "重庆",
                "pinyin": "Chongqing",
                "zip": "023",
                "szm": "CQ"
            },
            {
                "label": "上海|Shanghai|SH|021",
                "name": "上海",
                "pinyin": "Shanghai",
                "zip": "021",
                "szm": "SH"
            },
            {
                "label": "天津|Tianjin|TJ|022",
                "name": "天津",
                "pinyin": "Tianjin",
                "zip": "022",
                "szm": "TJ"
            },
            {
                "label": "长春|Changchun|CC|0431",
                "name": "长春",
                "pinyin": "Changchun",
                "zip": "0431",
                "szm": "CC"
            },
            {
                "label": "长沙|Changsha|CS|0731",
                "name": "长沙",
                "pinyin": "Changsha",
                "zip": "0731",
                "szm": "CS"
            },
            {
                "label": "常州|Changzhou|CZ|0519",
                "name": "常州",
                "pinyin": "Changzhou",
                "zip": "0519",
                "szm": "CZ"
            },
            {
                "label": "成都|Chengdu|CD|028",
                "name": "成都",
                "pinyin": "Chengdu",
                "zip": "028",
                "szm": "CD"
            },
            {
                "label": "大连|Dalian|DL|0411",
                "name": "大连",
                "pinyin": "Dalian",
                "zip": "0411",
                "szm": "DL"
            },
            {
                "label": "东莞|Dongguan|DG|0769",
                "name": "东莞",
                "pinyin": "Dongguan",
                "zip": "0769",
                "szm": "DG"
            },
            {
                "label": "佛山|Foshan|BS|0757",
                "name": "佛山",
                "pinyin": "Foshan",
                "zip": "0757",
                "szm": "BS"
            },
            {
                "label": "福州|Fuzhou|FZ|0591",
                "name": "福州",
                "pinyin": "Fuzhou",
                "zip": "0591",
                "szm": "FZ"
            },
            {
                "label": "广州|Guangzhou|GZ|020",
                "name": "广州",
                "pinyin": "Guangzhou",
                "zip": "020",
                "szm": "GZ"
            },
            {
                "label": "贵阳|Guiyang|GY|0851",
                "name": "贵阳",
                "pinyin": "Guiyang",
                "zip": "0851",
                "szm": "GY"
            },
            {
                "label": "哈尔滨|Haerbin|HEB|0451",
                "name": "哈尔滨",
                "pinyin": "Haerbin",
                "zip": "0451",
                "szm": "HEB"
            },
            {
                "label": "海口|Haikou|HK|0898",
                "name": "海口",
                "pinyin": "Haikou",
                "zip": "0898",
                "szm": "HK"
            },
            {
                "label": "邯郸|Handan|HD|0310",
                "name": "邯郸",
                "pinyin": "Handan",
                "zip": "0310",
                "szm": "HD"
            },
            {
                "label": "杭州|Hangzhou|HZ|0571",
                "name": "杭州",
                "pinyin": "Hangzhou",
                "zip": "0571",
                "szm": "HZ"
            },
            {
                "label": "合肥|Hefei|HF|0551",
                "name": "合肥",
                "pinyin": "Hefei",
                "zip": "0551",
                "szm": "HF"
            },
            {
                "label": "惠州|Huizhou|HZ|0752",
                "name": "惠州",
                "pinyin": "Huizhou",
                "zip": "0752",
                "szm": "HZ"
            },
            {
                "label": "焦作|Jiaozuo|JZ|0391",
                "name": "焦作",
                "pinyin": "Jiaozuo",
                "zip": "0391",
                "szm": "JZ"
            },
            {
                "label": "嘉兴|Jiaxing|JX|0573",
                "name": "嘉兴",
                "pinyin": "Jiaxing",
                "zip": "0573",
                "szm": "JX"
            },
            {
                "label": "吉林|Jilin|JL|0423",
                "name": "吉林",
                "pinyin": "Jilin",
                "zip": "0423",
                "szm": "JL"
            },
            {
                "label": "济南|Jinan|JN|0531",
                "name": "济南",
                "pinyin": "Jinan",
                "zip": "0531",
                "szm": "JN"
            },
            {
                "label": "昆明|Kunming|KM|0871",
                "name": "昆明",
                "pinyin": "Kunming",
                "zip": "0871",
                "szm": "KM"
            },
            {
                "label": "兰州|Lanzhou|LZ|0931",
                "name": "兰州",
                "pinyin": "Lanzhou",
                "zip": "0931",
                "szm": "LZ"
            },
            {
                "label": "柳州|Liuzhou|LZ|0772",
                "name": "柳州",
                "pinyin": "Liuzhou",
                "zip": "0772",
                "szm": "LZ"
            },
            {
                "label": "洛阳|Luoyang|LY|0379",
                "name": "洛阳",
                "pinyin": "Luoyang",
                "zip": "0379",
                "szm": "LY"
            },
            {
                "label": "南昌|Nanchang|NC|0791",
                "name": "南昌",
                "pinyin": "Nanchang",
                "zip": "0791",
                "szm": "NC"
            },
            {
                "label": "南京|Nanjing|NJ|025",
                "name": "南京",
                "pinyin": "Nanjing",
                "zip": "025",
                "szm": "NJ"
            },
            {
                "label": "南宁|Nanning|NN|0771",
                "name": "南宁",
                "pinyin": "Nanning",
                "zip": "0771",
                "szm": "NN"
            },
            {
                "label": "南通|Nantong|NT|0513",
                "name": "南通",
                "pinyin": "Nantong",
                "zip": "0513",
                "szm": "NT"
            },
            {
                "label": "宁波|Ningbo|NB|0574",
                "name": "宁波",
                "pinyin": "Ningbo",
                "zip": "0574",
                "szm": "NB"
            },
            {
                "label": "青岛|Qingdao|QD|0532",
                "name": "青岛",
                "pinyin": "Qingdao",
                "zip": "0532",
                "szm": "QD"
            },
            {
                "label": "泉州|Quanzhou|QZ|0595",
                "name": "泉州",
                "pinyin": "Quanzhou",
                "zip": "0595",
                "szm": "QZ"
            },
            {
                "label": "沈阳|Shenyang|SY|024",
                "name": "沈阳",
                "pinyin": "Shenyang",
                "zip": "024",
                "szm": "SY"
            },
            {
                "label": "深圳|Shenzhen|SZ|0755",
                "name": "深圳",
                "pinyin": "Shenzhen",
                "zip": "0755",
                "szm": "SZ"
            },
            {
                "label": "石家庄|Shijiazhuang|SJZ|0311",
                "name": "石家庄",
                "pinyin": "Shijiazhuang",
                "zip": "0311",
                "szm": "SJZ"
            },
            {
                "label": "苏州|Suzhou|SZ|0512",
                "name": "苏州",
                "pinyin": "Suzhou",
                "zip": "0512",
                "szm": "SZ"
            },
            {
                "label": "台州|Taizhou|TZ|0576",
                "name": "台州",
                "pinyin": "Taizhou",
                "zip": "0576",
                "szm": "TZ"
            },
            {
                "label": "唐山|Tangshan|TS|0315",
                "name": "唐山",
                "pinyin": "Tangshan",
                "zip": "0315",
                "szm": "TS"
            },
            {
                "label": "潍坊|Weifang|WF|0536",
                "name": "潍坊",
                "pinyin": "Weifang",
                "zip": "0536",
                "szm": "WF"
            },
            {
                "label": "威海|Weihai|WH|0631",
                "name": "威海",
                "pinyin": "Weihai",
                "zip": "0631",
                "szm": "WH"
            },
            {
                "label": "武汉|Wuhan|WH|027",
                "name": "武汉",
                "pinyin": "Wuhan",
                "zip": "027",
                "szm": "WH"
            },
            {
                "label": "无锡|Wuxi|WX|0510",
                "name": "无锡",
                "pinyin": "Wuxi",
                "zip": "0510",
                "szm": "WX"
            },
            {
                "label": "厦门|Xiamen|XM|0592",
                "name": "厦门",
                "pinyin": "Xiamen",
                "zip": "0592",
                "szm": "XM"
            },
            {
                "label": "西安|Xian|XA|029",
                "name": "西安",
                "pinyin": "Xian",
                "zip": "029",
                "szm": "XA"
            },
            {
                "label": "许昌|Xuchang|XC|0374",
                "name": "许昌",
                "pinyin": "Xuchang",
                "zip": "0374",
                "szm": "XC"
            },
            {
                "label": "徐州|Xuzhou|XZ|0516",
                "name": "徐州",
                "pinyin": "Xuzhou",
                "zip": "0516",
                "szm": "XZ"
            },
            {
                "label": "扬州|Yangzhou|YZ|0514",
                "name": "扬州",
                "pinyin": "Yangzhou",
                "zip": "0514",
                "szm": "YZ"
            },
            {
                "label": "烟台|Yantai|YT|0535",
                "name": "烟台",
                "pinyin": "Yantai",
                "zip": "0535",
                "szm": "YT"
            },
            {
                "label": "漳州|Zhangzhou|ZZ|0596",
                "name": "漳州",
                "pinyin": "Zhangzhou",
                "zip": "0596",
                "szm": "ZZ"
            },
            {
                "label": "郑州|Zhengzhou|ZZ|0371",
                "name": "郑州",
                "pinyin": "Zhengzhou",
                "zip": "0371",
                "szm": "ZZ"
            },
            {
                "label": "中山|Zhongshan|ZS|0760",
                "name": "中山",
                "pinyin": "Zhongshan",
                "zip": "0760",
                "szm": "ZS"
            },
            {
                "label": "珠海|Zhuhai|ZH|0756",
                "name": "珠海",
                "pinyin": "Zhuhai",
                "zip": "0756",
                "szm": "ZH"
            },
            {
                "label": "阿坝|Aba|AB|0837",
                "name": "阿坝",
                "pinyin": "Aba",
                "zip": "0837",
                "szm": "AB"
            },
            {
                "label": "阿克苏|Akesu|AKS|0997",
                "name": "阿克苏",
                "pinyin": "Akesu",
                "zip": "0997",
                "szm": "AKS"
            },
            {
                "label": "阿拉善盟|Alashanmeng|ALSM|0483",
                "name": "阿拉善盟",
                "pinyin": "Alashanmeng",
                "zip": "0483",
                "szm": "ALSM"
            },
            {
                "label": "阿勒泰|Aletai|ALT|0906",
                "name": "阿勒泰",
                "pinyin": "Aletai",
                "zip": "0906",
                "szm": "ALT"
            },
            {
                "label": "阿里|Ali|AL|0897",
                "name": "阿里",
                "pinyin": "Ali",
                "zip": "0897",
                "szm": "AL"
            },
            {
                "label": "安康|Ankang|AK|0915",
                "name": "安康",
                "pinyin": "Ankang",
                "zip": "0915",
                "szm": "AK"
            },
            {
                "label": "安庆|Anqing|AQ|0556",
                "name": "安庆",
                "pinyin": "Anqing",
                "zip": "0556",
                "szm": "AQ"
            },
            {
                "label": "鞍山|Anshan|AS|0412",
                "name": "鞍山",
                "pinyin": "Anshan",
                "zip": "0412",
                "szm": "AS"
            },
            {
                "label": "安顺|Anshun|AS|0853",
                "name": "安顺",
                "pinyin": "Anshun",
                "zip": "0853",
                "szm": "AS"
            },
            {
                "label": "安阳|Anyang|AY|0372",
                "name": "安阳",
                "pinyin": "Anyang",
                "zip": "0372",
                "szm": "AY"
            },
            {
                "label": "白城|Baicheng|BC|0436",
                "name": "白城",
                "pinyin": "Baicheng",
                "zip": "0436",
                "szm": "BC"
            },
            {
                "label": "百色|Baise|BS|0776",
                "name": "百色",
                "pinyin": "Baise",
                "zip": "0776",
                "szm": "BS"
            },
            {
                "label": "白山|Baishan|BS|0439",
                "name": "白山",
                "pinyin": "Baishan",
                "zip": "0439",
                "szm": "BS"
            },
            {
                "label": "白银|Baiyin|BY|0943",
                "name": "白银",
                "pinyin": "Baiyin",
                "zip": "0943",
                "szm": "BY"
            },
            {
                "label": "蚌埠|Bangbu|BB|0552",
                "name": "蚌埠",
                "pinyin": "Bangbu",
                "zip": "0552",
                "szm": "BB"
            },
            {
                "label": "保定|Baoding|BD|0312",
                "name": "保定",
                "pinyin": "Baoding",
                "zip": "0312",
                "szm": "BD"
            },
            {
                "label": "宝鸡|Baoji|BJ|0917",
                "name": "宝鸡",
                "pinyin": "Baoji",
                "zip": "0917",
                "szm": "BJ"
            },
            {
                "label": "保山|Baoshan|BS|0875",
                "name": "保山",
                "pinyin": "Baoshan",
                "zip": "0875",
                "szm": "BS"
            },
            {
                "label": "包头|Baotou|BT|0472",
                "name": "包头",
                "pinyin": "Baotou",
                "zip": "0472",
                "szm": "BT"
            },
            {
                "label": "巴彦淖尔|Bayannaoer|BYNE|0478",
                "name": "巴彦淖尔",
                "pinyin": "Bayannaoer",
                "zip": "0478",
                "szm": "BYNE"
            },
            {
                "label": "巴音郭楞|Bayinguoleng|BYGL|0996",
                "name": "巴音郭楞",
                "pinyin": "Bayinguoleng",
                "zip": "0996",
                "szm": "BYGL"
            },
            {
                "label": "巴中|Bazhong|BZ|0827",
                "name": "巴中",
                "pinyin": "Bazhong",
                "zip": "0827",
                "szm": "BZ"
            },
            {
                "label": "北海|Beihai|BH|0779",
                "name": "北海",
                "pinyin": "Beihai",
                "zip": "0779",
                "szm": "BH"
            },
            {
                "label": "本溪|Benxi|BX|0414",
                "name": "本溪",
                "pinyin": "Benxi",
                "zip": "0414",
                "szm": "BX"
            },
            {
                "label": "毕节|Bijie|BJ|0857",
                "name": "毕节",
                "pinyin": "Bijie",
                "zip": "0857",
                "szm": "BJ"
            },
            {
                "label": "滨州|Binzhou|BZ|0543",
                "name": "滨州",
                "pinyin": "Binzhou",
                "zip": "0543",
                "szm": "BZ"
            },
            {
                "label": "博尔塔拉|Boertala|BETL|0909",
                "name": "博尔塔拉",
                "pinyin": "Boertala",
                "zip": "0909",
                "szm": "BETL"
            },
            {
                "label": "亳州|Bozhou|BZ|0558",
                "name": "亳州",
                "pinyin": "Bozhou",
                "zip": "0558",
                "szm": "BZ"
            },
            {
                "label": "沧州|Cangzhou|CZ|0317",
                "name": "沧州",
                "pinyin": "Cangzhou",
                "zip": "0317",
                "szm": "CZ"
            },
            {
                "label": "常德|Changde|CD|0736",
                "name": "常德",
                "pinyin": "Changde",
                "zip": "0736",
                "szm": "CD"
            },
            {
                "label": "昌都|Changdu|CD|0895",
                "name": "昌都",
                "pinyin": "Changdu",
                "zip": "0895",
                "szm": "CD"
            },
            {
                "label": "昌吉|Changji|CJ|0997",
                "name": "昌吉",
                "pinyin": "Changji",
                "zip": "0997",
                "szm": "CJ"
            },
            {
                "label": "长治|Changzhi|CZ|0355",
                "name": "长治",
                "pinyin": "Changzhi",
                "zip": "0355",
                "szm": "CZ"
            },
            {
                "label": "巢湖|Chaohu|CH|0565",
                "name": "巢湖",
                "pinyin": "Chaohu",
                "zip": "0565",
                "szm": "CH"
            },
            {
                "label": "朝阳|Chaoyang|CY|0421",
                "name": "朝阳",
                "pinyin": "Chaoyang",
                "zip": "0421",
                "szm": "CY"
            },
            {
                "label": "潮州|Chaozhou|CZ|0768",
                "name": "潮州",
                "pinyin": "Chaozhou",
                "zip": "0768",
                "szm": "CZ"
            },
            {
                "label": "承德|Chengde|CD|0314",
                "name": "承德",
                "pinyin": "Chengde",
                "zip": "0314",
                "szm": "CD"
            },
            {
                "label": "郴州|Chenzhou|CZ|0735",
                "name": "郴州",
                "pinyin": "Chenzhou",
                "zip": "0735",
                "szm": "CZ"
            },
            {
                "label": "赤峰|Chifeng|CF|0476",
                "name": "赤峰",
                "pinyin": "Chifeng",
                "zip": "0476",
                "szm": "CF"
            },
            {
                "label": "池州|Chizhou|CZ|0566",
                "name": "池州",
                "pinyin": "Chizhou",
                "zip": "0566",
                "szm": "CZ"
            },
            {
                "label": "崇左|Chongzuo|CZ|0771",
                "name": "崇左",
                "pinyin": "Chongzuo",
                "zip": "0771",
                "szm": "CZ"
            },
            {
                "label": "楚雄|Chuxiong|CX|0875",
                "name": "楚雄",
                "pinyin": "Chuxiong",
                "zip": "0875",
                "szm": "CX"
            },
            {
                "label": "滁州|Chuzhou|CZ|0550",
                "name": "滁州",
                "pinyin": "Chuzhou",
                "zip": "0550",
                "szm": "CZ"
            },
            {
                "label": "大理|Dali|DL|0872",
                "name": "大理",
                "pinyin": "Dali",
                "zip": "0872",
                "szm": "DL"
            },
            {
                "label": "丹东|Dandong|DD|0415",
                "name": "丹东",
                "pinyin": "Dandong",
                "zip": "0415",
                "szm": "DD"
            },
            {
                "label": "大庆|Daqing|DQ|0459",
                "name": "大庆",
                "pinyin": "Daqing",
                "zip": "0459",
                "szm": "DQ"
            },
            {
                "label": "大同|Datong|DT|0352",
                "name": "大同",
                "pinyin": "Datong",
                "zip": "0352",
                "szm": "DT"
            },
            {
                "label": "大兴安岭|Daxinganling|DXAL|0457",
                "name": "大兴安岭",
                "pinyin": "Daxinganling",
                "zip": "0457",
                "szm": "DXAL"
            },
            {
                "label": "达州|Dazhou|DZ|0818",
                "name": "达州",
                "pinyin": "Dazhou",
                "zip": "0818",
                "szm": "DZ"
            },
            {
                "label": "德宏|Dehong|DH|0692",
                "name": "德宏",
                "pinyin": "Dehong",
                "zip": "0692",
                "szm": "DH"
            },
            {
                "label": "德阳|Deyang|DY|0838",
                "name": "德阳",
                "pinyin": "Deyang",
                "zip": "0838",
                "szm": "DY"
            },
            {
                "label": "德州|Dezhou|DZ|0534",
                "name": "德州",
                "pinyin": "Dezhou",
                "zip": "0534",
                "szm": "DZ"
            },
            {
                "label": "定西|Dingxi|DX|0932",
                "name": "定西",
                "pinyin": "Dingxi",
                "zip": "0932",
                "szm": "DX"
            },
            {
                "label": "迪庆|Diqing|DQ|0887",
                "name": "迪庆",
                "pinyin": "Diqing",
                "zip": "0887",
                "szm": "DQ"
            },
            {
                "label": "东营|Dongying|DY|0546",
                "name": "东营",
                "pinyin": "Dongying",
                "zip": "0546",
                "szm": "DY"
            },
            {
                "label": "鄂尔多斯|Eerduosi|EEDS|0477",
                "name": "鄂尔多斯",
                "pinyin": "Eerduosi",
                "zip": "0477",
                "szm": "EEDS"
            },
            {
                "label": "恩施|Enshi|ES|0718",
                "name": "恩施",
                "pinyin": "Enshi",
                "zip": "0718",
                "szm": "ES"
            },
            {
                "label": "鄂州|Ezhou|EZ|0711",
                "name": "鄂州",
                "pinyin": "Ezhou",
                "zip": "0711",
                "szm": "EZ"
            },
            {
                "label": "防城港|Fangchenggang|FCG|0770",
                "name": "防城港",
                "pinyin": "Fangchenggang",
                "zip": "0770",
                "szm": "FCG"
            },
            {
                "label": "抚顺|Fushun|FS|0413",
                "name": "抚顺",
                "pinyin": "Fushun",
                "zip": "0413",
                "szm": "FS"
            },
            {
                "label": "阜新|Fuxin|FX|0418",
                "name": "阜新",
                "pinyin": "Fuxin",
                "zip": "0418",
                "szm": "FX"
            },
            {
                "label": "阜阳|Fuyang|FY|0558",
                "name": "阜阳",
                "pinyin": "Fuyang",
                "zip": "0558",
                "szm": "FY"
            },
            {
                "label": "抚州|Fuzhou|FZ|0794",
                "name": "抚州",
                "pinyin": "Fuzhou",
                "zip": "0794",
                "szm": "FZ"
            },
            {
                "label": "甘南|Gannan|GN|0941",
                "name": "甘南",
                "pinyin": "Gannan",
                "zip": "0941",
                "szm": "GN"
            },
            {
                "label": "赣州|Ganzhou|GZ|0797",
                "name": "赣州",
                "pinyin": "Ganzhou",
                "zip": "0797",
                "szm": "GZ"
            },
            {
                "label": "甘孜|Ganzi|GZ|0836",
                "name": "甘孜",
                "pinyin": "Ganzi",
                "zip": "0836",
                "szm": "GZ"
            },
            {
                "label": "广安|Guangan|GA|0826",
                "name": "广安",
                "pinyin": "Guangan",
                "zip": "0826",
                "szm": "GA"
            },
            {
                "label": "广元|Guangyuan|GY|0839",
                "name": "广元",
                "pinyin": "Guangyuan",
                "zip": "0839",
                "szm": "GY"
            },
            {
                "label": "贵港|Guigang|GG|0775",
                "name": "贵港",
                "pinyin": "Guigang",
                "zip": "0775",
                "szm": "GG"
            },
            {
                "label": "桂林|Guilin|GL|0773",
                "name": "桂林",
                "pinyin": "Guilin",
                "zip": "0773",
                "szm": "GL"
            },
            {
                "label": "果洛|Guoluo|GL|0975",
                "name": "果洛",
                "pinyin": "Guoluo",
                "zip": "0975",
                "szm": "GL"
            },
            {
                "label": "固原|Guyuan|GY|0954",
                "name": "固原",
                "pinyin": "Guyuan",
                "zip": "0954",
                "szm": "GY"
            },
            {
                "label": "海北|Haibei|HB|0970",
                "name": "海北",
                "pinyin": "Haibei",
                "zip": "0970",
                "szm": "HB"
            },
            {
                "label": "海东|Haidong|HD|0972",
                "name": "海东",
                "pinyin": "Haidong",
                "zip": "0972",
                "szm": "HD"
            },
            {
                "label": "海南|Hainan|HN|0974",
                "name": "海南",
                "pinyin": "Hainan",
                "zip": "0974",
                "szm": "HN"
            },
            {
                "label": "海西|Haixi|HX|0977",
                "name": "海西",
                "pinyin": "Haixi",
                "zip": "0977",
                "szm": "HX"
            },
            {
                "label": "哈密|Hami|HM|0902",
                "name": "哈密",
                "pinyin": "Hami",
                "zip": "0902",
                "szm": "HM"
            },
            {
                "label": "汉中|Hanzhong|HZ|0916",
                "name": "汉中",
                "pinyin": "Hanzhong",
                "zip": "0916",
                "szm": "HZ"
            },
            {
                "label": "鹤壁|Hebi|HB|0392",
                "name": "鹤壁",
                "pinyin": "Hebi",
                "zip": "0392",
                "szm": "HB"
            },
            {
                "label": "河池|Hechi|HC|0778",
                "name": "河池",
                "pinyin": "Hechi",
                "zip": "0778",
                "szm": "HC"
            },
            {
                "label": "鹤岗|Hegang|HG|0468",
                "name": "鹤岗",
                "pinyin": "Hegang",
                "zip": "0468",
                "szm": "HG"
            },
            {
                "label": "黑河|Heihe|HH|0456",
                "name": "黑河",
                "pinyin": "Heihe",
                "zip": "0456",
                "szm": "HH"
            },
            {
                "label": "衡水|Hengshui|HS|0318",
                "name": "衡水",
                "pinyin": "Hengshui",
                "zip": "0318",
                "szm": "HS"
            },
            {
                "label": "衡阳|Hengyang|HY|0734",
                "name": "衡阳",
                "pinyin": "Hengyang",
                "zip": "0734",
                "szm": "HY"
            },
            {
                "label": "和田地|Hetiandi|HTD|0903",
                "name": "和田地",
                "pinyin": "Hetiandi",
                "zip": "0903",
                "szm": "HTD"
            },
            {
                "label": "河源|Heyuan|HY|0762",
                "name": "河源",
                "pinyin": "Heyuan",
                "zip": "0762",
                "szm": "HY"
            },
            {
                "label": "菏泽|Heze|HZ|0530",
                "name": "菏泽",
                "pinyin": "Heze",
                "zip": "0530",
                "szm": "HZ"
            },
            {
                "label": "贺州|Hezhou|HZ|0774",
                "name": "贺州",
                "pinyin": "Hezhou",
                "zip": "0774",
                "szm": "HZ"
            },
            {
                "label": "红河|Honghe|HH|0873",
                "name": "红河",
                "pinyin": "Honghe",
                "zip": "0873",
                "szm": "HH"
            },
            {
                "label": "淮安|Huaian|HA|0517",
                "name": "淮安",
                "pinyin": "Huaian",
                "zip": "0517",
                "szm": "HA"
            },
            {
                "label": "淮北|Huaibei|HB|0561",
                "name": "淮北",
                "pinyin": "Huaibei",
                "zip": "0561",
                "szm": "HB"
            },
            {
                "label": "怀化|Huaihua|HH|0745",
                "name": "怀化",
                "pinyin": "Huaihua",
                "zip": "0745",
                "szm": "HH"
            },
            {
                "label": "淮南|Huainan|HN|0554",
                "name": "淮南",
                "pinyin": "Huainan",
                "zip": "0554",
                "szm": "HN"
            },
            {
                "label": "黄冈|Huanggang|HG|0713",
                "name": "黄冈",
                "pinyin": "Huanggang",
                "zip": "0713",
                "szm": "HG"
            },
            {
                "label": "黄南|Huangnan|HN|0973",
                "name": "黄南",
                "pinyin": "Huangnan",
                "zip": "0973",
                "szm": "HN"
            },
            {
                "label": "黄山|Huangshan|HS|0559",
                "name": "黄山",
                "pinyin": "Huangshan",
                "zip": "0559",
                "szm": "HS"
            },
            {
                "label": "黄石|Huangshi|HS|0714",
                "name": "黄石",
                "pinyin": "Huangshi",
                "zip": "0714",
                "szm": "HS"
            },
            {
                "label": "呼和浩特|Huhehaote|HHHT|0471",
                "name": "呼和浩特",
                "pinyin": "Huhehaote",
                "zip": "0471",
                "szm": "HHHT"
            },
            {
                "label": "葫芦岛|Huludao|HLD|0429",
                "name": "葫芦岛",
                "pinyin": "Huludao",
                "zip": "0429",
                "szm": "HLD"
            },
            {
                "label": "呼伦贝尔|Hulunbeier|HLBE|0470",
                "name": "呼伦贝尔",
                "pinyin": "Hulunbeier",
                "zip": "0470",
                "szm": "HLBE"
            },
            {
                "label": "湖州|Huzhou|HZ|0572",
                "name": "湖州",
                "pinyin": "Huzhou",
                "zip": "0572",
                "szm": "HZ"
            },
            {
                "label": "佳木斯|Jiamusi|JMS|0454",
                "name": "佳木斯",
                "pinyin": "Jiamusi",
                "zip": "0454",
                "szm": "JMS"
            },
            {
                "label": "江门|Jiangmen|JM|0750",
                "name": "江门",
                "pinyin": "Jiangmen",
                "zip": "0750",
                "szm": "JM"
            },
            {
                "label": "吉安|Jian|JA|0796",
                "name": "吉安",
                "pinyin": "Jian",
                "zip": "0796",
                "szm": "JA"
            },
            {
                "label": "嘉峪关|Jiayuguan|JYG|0937",
                "name": "嘉峪关",
                "pinyin": "Jiayuguan",
                "zip": "0937",
                "szm": "JYG"
            },
            {
                "label": "揭阳|Jieyang|JY|0663",
                "name": "揭阳",
                "pinyin": "Jieyang",
                "zip": "0663",
                "szm": "JY"
            },
            {
                "label": "金昌|Jinchang|JC|0935",
                "name": "金昌",
                "pinyin": "Jinchang",
                "zip": "0935",
                "szm": "JC"
            },
            {
                "label": "晋城|Jincheng|JC|0356",
                "name": "晋城",
                "pinyin": "Jincheng",
                "zip": "0356",
                "szm": "JC"
            },
            {
                "label": "景德镇|Jingdezhen|JDZ|0798",
                "name": "景德镇",
                "pinyin": "Jingdezhen",
                "zip": "0798",
                "szm": "JDZ"
            },
            {
                "label": "荆门|Jingmen|JM|0724",
                "name": "荆门",
                "pinyin": "Jingmen",
                "zip": "0724",
                "szm": "JM"
            },
            {
                "label": "荆州|Jingzhou|JZ|0716",
                "name": "荆州",
                "pinyin": "Jingzhou",
                "zip": "0716",
                "szm": "JZ"
            },
            {
                "label": "金华|Jinhua|JH|0579",
                "name": "金华",
                "pinyin": "Jinhua",
                "zip": "0579",
                "szm": "JH"
            },
            {
                "label": "济宁|Jining|JN|0537",
                "name": "济宁",
                "pinyin": "Jining",
                "zip": "0537",
                "szm": "JN"
            },
            {
                "label": "晋中|Jinzhong|JZ|0354",
                "name": "晋中",
                "pinyin": "Jinzhong",
                "zip": "0354",
                "szm": "JZ"
            },
            {
                "label": "锦州|Jinzhou|JZ|0416",
                "name": "锦州",
                "pinyin": "Jinzhou",
                "zip": "0416",
                "szm": "JZ"
            },
            {
                "label": "九江|Jiujiang|JJ|0792",
                "name": "九江",
                "pinyin": "Jiujiang",
                "zip": "0792",
                "szm": "JJ"
            },
            {
                "label": "酒泉|Jiuquan|JQ|0937",
                "name": "酒泉",
                "pinyin": "Jiuquan",
                "zip": "0937",
                "szm": "JQ"
            },
            {
                "label": "鸡西|Jixi|JX|0467",
                "name": "鸡西",
                "pinyin": "Jixi",
                "zip": "0467",
                "szm": "JX"
            },
            {
                "label": "开封|Kaifeng|KF|0378",
                "name": "开封",
                "pinyin": "Kaifeng",
                "zip": "0378",
                "szm": "KF"
            },
            {
                "label": "喀什地|Kashidi|KSD|0998",
                "name": "喀什地",
                "pinyin": "Kashidi",
                "zip": "0998",
                "szm": "KSD"
            },
            {
                "label": "克拉玛依|Kelamayi|KLMY|0990",
                "name": "克拉玛依",
                "pinyin": "Kelamayi",
                "zip": "0990",
                "szm": "KLMY"
            },
            {
                "label": "克孜勒|Kezile|KZL|0908",
                "name": "克孜勒",
                "pinyin": "Kezile",
                "zip": "0908",
                "szm": "KZL"
            },
            {
                "label": "来宾|Laibin|LB|0772",
                "name": "来宾",
                "pinyin": "Laibin",
                "zip": "0772",
                "szm": "LB"
            },
            {
                "label": "莱芜|Laiwu|LW|0634",
                "name": "莱芜",
                "pinyin": "Laiwu",
                "zip": "0634",
                "szm": "LW"
            },
            {
                "label": "廊坊|Langfang|LF|0316",
                "name": "廊坊",
                "pinyin": "Langfang",
                "zip": "0316",
                "szm": "LF"
            },
            {
                "label": "拉萨|Lasa|LS|0891",
                "name": "拉萨",
                "pinyin": "Lasa",
                "zip": "0891",
                "szm": "LS"
            },
            {
                "label": "乐山|Leshan|YS|0833",
                "name": "乐山",
                "pinyin": "Leshan",
                "zip": "0833",
                "szm": "YS"
            },
            {
                "label": "凉山|Liangshan|LS|0834",
                "name": "凉山",
                "pinyin": "Liangshan",
                "zip": "0834",
                "szm": "LS"
            },
            {
                "label": "连云港|Lianyungang|LYG|0518",
                "name": "连云港",
                "pinyin": "Lianyungang",
                "zip": "0518",
                "szm": "LYG"
            },
            {
                "label": "聊城|Liaocheng|LC|0635",
                "name": "聊城",
                "pinyin": "Liaocheng",
                "zip": "0635",
                "szm": "LC"
            },
            {
                "label": "辽阳|Liaoyang|LY|0419",
                "name": "辽阳",
                "pinyin": "Liaoyang",
                "zip": "0419",
                "szm": "LY"
            },
            {
                "label": "辽源|Liaoyuan|LY|0437",
                "name": "辽源",
                "pinyin": "Liaoyuan",
                "zip": "0437",
                "szm": "LY"
            },
            {
                "label": "丽江|Lijiang|LJ|0888",
                "name": "丽江",
                "pinyin": "Lijiang",
                "zip": "0888",
                "szm": "LJ"
            },
            {
                "label": "临沧|Lincang|LC|0883",
                "name": "临沧",
                "pinyin": "Lincang",
                "zip": "0883",
                "szm": "LC"
            },
            {
                "label": "临汾|Linfen|LF|0357",
                "name": "临汾",
                "pinyin": "Linfen",
                "zip": "0357",
                "szm": "LF"
            },
            {
                "label": "临夏|Linxia|LX|0930",
                "name": "临夏",
                "pinyin": "Linxia",
                "zip": "0930",
                "szm": "LX"
            },
            {
                "label": "临沂|Linyi|LY|0539",
                "name": "临沂",
                "pinyin": "Linyi",
                "zip": "0539",
                "szm": "LY"
            },
            {
                "label": "林芝|Linzhi|LZ|0894",
                "name": "林芝",
                "pinyin": "Linzhi",
                "zip": "0894",
                "szm": "LZ"
            },
            {
                "label": "丽水|Lishui|LS|0578",
                "name": "丽水",
                "pinyin": "Lishui",
                "zip": "0578",
                "szm": "LS"
            },
            {
                "label": "六安|Luan|LA|0564",
                "name": "六安",
                "pinyin": "Luan",
                "zip": "0564",
                "szm": "LA"
            },
            {
                "label": "六盘水|Liupanshui|LPS|0858",
                "name": "六盘水",
                "pinyin": "Liupanshui",
                "zip": "0858",
                "szm": "LPS"
            },
            {
                "label": "陇南|Longnan|LN|0939",
                "name": "陇南",
                "pinyin": "Longnan",
                "zip": "0939",
                "szm": "LN"
            },
            {
                "label": "龙岩|Longyan|LY|0597",
                "name": "龙岩",
                "pinyin": "Longyan",
                "zip": "0597",
                "szm": "LY"
            },
            {
                "label": "娄底|Loudi|LD|0738",
                "name": "娄底",
                "pinyin": "Loudi",
                "zip": "0738",
                "szm": "LD"
            },
            {
                "label": "漯河|Luohe|TH|0395",
                "name": "漯河",
                "pinyin": "Luohe",
                "zip": "0395",
                "szm": "TH"
            },
            {
                "label": "泸州|Luzhou|LZ|0830",
                "name": "泸州",
                "pinyin": "Luzhou",
                "zip": "0830",
                "szm": "LZ"
            },
            {
                "label": "吕梁|Lvliang|LL|0358",
                "name": "吕梁",
                "pinyin": "Lvliang",
                "zip": "0358",
                "szm": "LL"
            },
            {
                "label": "马鞍山|Maanshan|MAS|0555",
                "name": "马鞍山",
                "pinyin": "Maanshan",
                "zip": "0555",
                "szm": "MAS"
            },
            {
                "label": "茂名|Maoming|MM|0668",
                "name": "茂名",
                "pinyin": "Maoming",
                "zip": "0668",
                "szm": "MM"
            },
            {
                "label": "眉山|Meishan|MS|028",
                "name": "眉山",
                "pinyin": "Meishan",
                "zip": "028",
                "szm": "MS"
            },
            {
                "label": "梅州|Meizhou|MZ|0753",
                "name": "梅州",
                "pinyin": "Meizhou",
                "zip": "0753",
                "szm": "MZ"
            },
            {
                "label": "绵阳|Mianyang|MY|0816",
                "name": "绵阳",
                "pinyin": "Mianyang",
                "zip": "0816",
                "szm": "MY"
            },
            {
                "label": "牡丹江|Mudanjiang|MDJ|0453",
                "name": "牡丹江",
                "pinyin": "Mudanjiang",
                "zip": "0453",
                "szm": "MDJ"
            },
            {
                "label": "南充|Nanchong|NC|0817",
                "name": "南充",
                "pinyin": "Nanchong",
                "zip": "0817",
                "szm": "NC"
            },
            {
                "label": "南平|Nanping|NP|0599",
                "name": "南平",
                "pinyin": "Nanping",
                "zip": "0599",
                "szm": "NP"
            },
            {
                "label": "南阳|Nanyang|NY|0377",
                "name": "南阳",
                "pinyin": "Nanyang",
                "zip": "0377",
                "szm": "NY"
            },
            {
                "label": "那曲|Naqu|NQ|0896",
                "name": "那曲",
                "pinyin": "Naqu",
                "zip": "0896",
                "szm": "NQ"
            },
            {
                "label": "内江|Neijiang|NJ|0832",
                "name": "内江",
                "pinyin": "Neijiang",
                "zip": "0832",
                "szm": "NJ"
            },
            {
                "label": "宁德|Ningde|ND|0593",
                "name": "宁德",
                "pinyin": "Ningde",
                "zip": "0593",
                "szm": "ND"
            },
            {
                "label": "怒江|Nujiang|NJ|0886",
                "name": "怒江",
                "pinyin": "Nujiang",
                "zip": "0886",
                "szm": "NJ"
            },
            {
                "label": "盘锦|Panjin|PJ|0427",
                "name": "盘锦",
                "pinyin": "Panjin",
                "zip": "0427",
                "szm": "PJ"
            },
            {
                "label": "攀枝花|Panzhihua|PZH|0812",
                "name": "攀枝花",
                "pinyin": "Panzhihua",
                "zip": "0812",
                "szm": "PZH"
            },
            {
                "label": "平顶山|Pingdingshan|PDS|0375",
                "name": "平顶山",
                "pinyin": "Pingdingshan",
                "zip": "0375",
                "szm": "PDS"
            },
            {
                "label": "平凉|Pingliang|PL|0933",
                "name": "平凉",
                "pinyin": "Pingliang",
                "zip": "0933",
                "szm": "PL"
            },
            {
                "label": "萍乡|Pingxiang|PX|0799",
                "name": "萍乡",
                "pinyin": "Pingxiang",
                "zip": "0799",
                "szm": "PX"
            },
            {
                "label": "普洱|Puer|PE|0879",
                "name": "普洱",
                "pinyin": "Puer",
                "zip": "0879",
                "szm": "PE"
            },
            {
                "label": "莆田|Putian|PT|0594",
                "name": "莆田",
                "pinyin": "Putian",
                "zip": "0594",
                "szm": "PT"
            },
            {
                "label": "濮阳|Puyang|PY|0393",
                "name": "濮阳",
                "pinyin": "Puyang",
                "zip": "0393",
                "szm": "PY"
            },
            {
                "label": "黔东|Qiandong|QD|0855",
                "name": "黔东",
                "pinyin": "Qiandong",
                "zip": "0855",
                "szm": "QD"
            },
            {
                "label": "黔南|Qiannan|QN|0854",
                "name": "黔南",
                "pinyin": "Qiannan",
                "zip": "0854",
                "szm": "QN"
            },
            {
                "label": "黔西南|Qianxinan|QXN|0859",
                "name": "黔西南",
                "pinyin": "Qianxinan",
                "zip": "0859",
                "szm": "QXN"
            },
            {
                "label": "庆阳|Qingyang|QY|0934",
                "name": "庆阳",
                "pinyin": "Qingyang",
                "zip": "0934",
                "szm": "QY"
            },
            {
                "label": "清远|Qingyuan|QY|0763",
                "name": "清远",
                "pinyin": "Qingyuan",
                "zip": "0763",
                "szm": "QY"
            },
            {
                "label": "秦皇岛|Qinhuangdao|QHD|0335",
                "name": "秦皇岛",
                "pinyin": "Qinhuangdao",
                "zip": "0335",
                "szm": "QHD"
            },
            {
                "label": "钦州|Qinzhou|QZ|0777",
                "name": "钦州",
                "pinyin": "Qinzhou",
                "zip": "0777",
                "szm": "QZ"
            },
            {
                "label": "齐齐哈尔|Qiqihaer|QQHE|0452",
                "name": "齐齐哈尔",
                "pinyin": "Qiqihaer",
                "zip": "0452",
                "szm": "QQHE"
            },
            {
                "label": "七台河|Qitaihe|QTH|0464",
                "name": "七台河",
                "pinyin": "Qitaihe",
                "zip": "0464",
                "szm": "QTH"
            },
            {
                "label": "曲靖|Qujing|QJ|0874",
                "name": "曲靖",
                "pinyin": "Qujing",
                "zip": "0874",
                "szm": "QJ"
            },
            {
                "label": "衢州|Quzhou|QZ|0570",
                "name": "衢州",
                "pinyin": "Quzhou",
                "zip": "0570",
                "szm": "QZ"
            },
            {
                "label": "日喀则|Rikaze|RKZ|0892",
                "name": "日喀则",
                "pinyin": "Rikaze",
                "zip": "0892",
                "szm": "RKZ"
            },
            {
                "label": "日照|Rizhao|RZ|0633",
                "name": "日照",
                "pinyin": "Rizhao",
                "zip": "0633",
                "szm": "RZ"
            },
            {
                "label": "三门峡|Sanmenxia|SMX|0398",
                "name": "三门峡",
                "pinyin": "Sanmenxia",
                "zip": "0398",
                "szm": "SMX"
            },
            {
                "label": "三明|Sanming|SM|0598",
                "name": "三明",
                "pinyin": "Sanming",
                "zip": "0598",
                "szm": "SM"
            },
            {
                "label": "三亚|Sanya|SY|0899",
                "name": "三亚",
                "pinyin": "Sanya",
                "zip": "0899",
                "szm": "SY"
            },
            {
                "label": "商洛|Shangluo|SL|0914",
                "name": "商洛",
                "pinyin": "Shangluo",
                "zip": "0914",
                "szm": "SL"
            },
            {
                "label": "商丘|Shangqiu|SQ|0370",
                "name": "商丘",
                "pinyin": "Shangqiu",
                "zip": "0370",
                "szm": "SQ"
            },
            {
                "label": "上饶|Shangrao|SR|0793",
                "name": "上饶",
                "pinyin": "Shangrao",
                "zip": "0793",
                "szm": "SR"
            },
            {
                "label": "山南|Shannan|SN|0893",
                "name": "山南",
                "pinyin": "Shannan",
                "zip": "0893",
                "szm": "SN"
            },
            {
                "label": "汕头|Shantou|ST|0754",
                "name": "汕头",
                "pinyin": "Shantou",
                "zip": "0754",
                "szm": "ST"
            },
            {
                "label": "汕尾|Shanwei|SY|0660",
                "name": "汕尾",
                "pinyin": "Shanwei",
                "zip": "0660",
                "szm": "SY"
            },
            {
                "label": "韶关|Shaoguan|SG|0751",
                "name": "韶关",
                "pinyin": "Shaoguan",
                "zip": "0751",
                "szm": "SG"
            },
            {
                "label": "绍兴|Shaoxing|SX|0575",
                "name": "绍兴",
                "pinyin": "Shaoxing",
                "zip": "0575",
                "szm": "SX"
            },
            {
                "label": "邵阳|Shaoyang|SY|0739",
                "name": "邵阳",
                "pinyin": "Shaoyang",
                "zip": "0739",
                "szm": "SY"
            },
            {
                "label": "十堰|Shiyan|SY|0719",
                "name": "十堰",
                "pinyin": "Shiyan",
                "zip": "0719",
                "szm": "SY"
            },
            {
                "label": "石嘴山|Shizuishan|SZS|0952",
                "name": "石嘴山",
                "pinyin": "Shizuishan",
                "zip": "0952",
                "szm": "SZS"
            },
            {
                "label": "双鸭山|Shuangyashan|SYS|0469",
                "name": "双鸭山",
                "pinyin": "Shuangyashan",
                "zip": "0469",
                "szm": "SYS"
            },
            {
                "label": "朔州|Shuozhou|SZ|0349",
                "name": "朔州",
                "pinyin": "Shuozhou",
                "zip": "0349",
                "szm": "SZ"
            },
            {
                "label": "四平|Siping|SP|0434",
                "name": "四平",
                "pinyin": "Siping",
                "zip": "0434",
                "szm": "SP"
            },
            {
                "label": "松原|Songyuan|SY|0438",
                "name": "松原",
                "pinyin": "Songyuan",
                "zip": "0438",
                "szm": "SY"
            },
            {
                "label": "绥化|Suihua|SH|0455",
                "name": "绥化",
                "pinyin": "Suihua",
                "zip": "0455",
                "szm": "SH"
            },
            {
                "label": "遂宁|Suining|SN|0825",
                "name": "遂宁",
                "pinyin": "Suining",
                "zip": "0825",
                "szm": "SN"
            },
            {
                "label": "随州|Suizhou|SZ|0722",
                "name": "随州",
                "pinyin": "Suizhou",
                "zip": "0722",
                "szm": "SZ"
            },
            {
                "label": "宿迁|Suqian|SQ|0527",
                "name": "宿迁",
                "pinyin": "Suqian",
                "zip": "0527",
                "szm": "SQ"
            },
            {
                "label": "宿州|Suzhou|SZ|0557",
                "name": "宿州",
                "pinyin": "Suzhou",
                "zip": "0557",
                "szm": "SZ"
            },
            {
                "label": "塔城地|Tachengdi|TCD|0901",
                "name": "塔城地",
                "pinyin": "Tachengdi",
                "zip": "0901",
                "szm": "TCD"
            },
            {
                "label": "泰安|Taian|TA|0538",
                "name": "泰安",
                "pinyin": "Taian",
                "zip": "0538",
                "szm": "TA"
            },
            {
                "label": "太原|Taiyuan|TY|0351",
                "name": "太原",
                "pinyin": "Taiyuan",
                "zip": "0351",
                "szm": "TY"
            },
            {
                "label": "泰州|Taizhou|TZ|0523",
                "name": "泰州",
                "pinyin": "Taizhou",
                "zip": "0523",
                "szm": "TZ"
            },
            {
                "label": "天水|Tianshui|TS|0938",
                "name": "天水",
                "pinyin": "Tianshui",
                "zip": "0938",
                "szm": "TS"
            },
            {
                "label": "铁岭|Tieling|TL|0410",
                "name": "铁岭",
                "pinyin": "Tieling",
                "zip": "0410",
                "szm": "TL"
            },
            {
                "label": "铜川|Tongchuan|TC|0919",
                "name": "铜川",
                "pinyin": "Tongchuan",
                "zip": "0919",
                "szm": "TC"
            },
            {
                "label": "通化|Tonghua|TH|0435",
                "name": "通化",
                "pinyin": "Tonghua",
                "zip": "0435",
                "szm": "TH"
            },
            {
                "label": "通辽|Tongliao|TL|0475",
                "name": "通辽",
                "pinyin": "Tongliao",
                "zip": "0475",
                "szm": "TL"
            },
            {
                "label": "铜陵|Tongling|TL|0562",
                "name": "铜陵",
                "pinyin": "Tongling",
                "zip": "0562",
                "szm": "TL"
            },
            {
                "label": "铜仁|Tongren|TR|0856",
                "name": "铜仁",
                "pinyin": "Tongren",
                "zip": "0856",
                "szm": "TR"
            },
            {
                "label": "吐鲁番|Tulufan|TLP|0995",
                "name": "吐鲁番",
                "pinyin": "Tulufan",
                "zip": "0995",
                "szm": "TLP"
            },
            {
                "label": "渭南|Weinan|WN|0913",
                "name": "渭南",
                "pinyin": "Weinan",
                "zip": "0913",
                "szm": "WN"
            },
            {
                "label": "文山|Wenshan|WS|0876",
                "name": "文山",
                "pinyin": "Wenshan",
                "zip": "0876",
                "szm": "WS"
            },
            {
                "label": "温州|Wenzhou|WZ|0577",
                "name": "温州",
                "pinyin": "Wenzhou",
                "zip": "0577",
                "szm": "WZ"
            },
            {
                "label": "乌海|Wuhai|WH|0473",
                "name": "乌海",
                "pinyin": "Wuhai",
                "zip": "0473",
                "szm": "WH"
            },
            {
                "label": "芜湖|Wuhu|WH|0553",
                "name": "芜湖",
                "pinyin": "Wuhu",
                "zip": "0553",
                "szm": "WH"
            },
            {
                "label": "乌兰察布|Wulanchabu|WLCB|0474",
                "name": "乌兰察布",
                "pinyin": "Wulanchabu",
                "zip": "0474",
                "szm": "WLCB"
            },
            {
                "label": "乌鲁木齐|Wulumuqi|WLMQ|0991",
                "name": "乌鲁木齐",
                "pinyin": "Wulumuqi",
                "zip": "0991",
                "szm": "WLMQ"
            },
            {
                "label": "武威|Wuwei|WW|0935",
                "name": "武威",
                "pinyin": "Wuwei",
                "zip": "0935",
                "szm": "WW"
            },
            {
                "label": "吴忠|Wuzhong|WZ|0953",
                "name": "吴忠",
                "pinyin": "Wuzhong",
                "zip": "0953",
                "szm": "WZ"
            },
            {
                "label": "梧州|Wuzhou|WZ|0774",
                "name": "梧州",
                "pinyin": "Wuzhou",
                "zip": "0774",
                "szm": "WZ"
            },
            {
                "label": "襄樊|Xiangfan|XF|0710",
                "name": "襄樊",
                "pinyin": "Xiangfan",
                "zip": "0710",
                "szm": "XF"
            },
            {
                "label": "湘潭|Xiangtan|XT|0732",
                "name": "湘潭",
                "pinyin": "Xiangtan",
                "zip": "0732",
                "szm": "XT"
            },
            {
                "label": "湘西|Xiangxi|XX|0743",
                "name": "湘西",
                "pinyin": "Xiangxi",
                "zip": "0743",
                "szm": "XX"
            },
            {
                "label": "咸宁|Xianning|XN|0715",
                "name": "咸宁",
                "pinyin": "Xianning",
                "zip": "0715",
                "szm": "XN"
            },
            {
                "label": "咸阳|Xianyang|XY|029",
                "name": "咸阳",
                "pinyin": "Xianyang",
                "zip": "029",
                "szm": "XY"
            },
            {
                "label": "孝感|Xiaogan|XG|0712",
                "name": "孝感",
                "pinyin": "Xiaogan",
                "zip": "0712",
                "szm": "XG"
            },
            {
                "label": "锡林郭勒盟|Xilinguolemeng|XLGLM|0479",
                "name": "锡林郭勒盟",
                "pinyin": "Xilinguolemeng",
                "zip": "0479",
                "szm": "XLGLM"
            },
            {
                "label": "兴安盟|Xinganmeng|XAM|0482",
                "name": "兴安盟",
                "pinyin": "Xinganmeng",
                "zip": "0482",
                "szm": "XAM"
            },
            {
                "label": "邢台|Xingtai|XT|0319",
                "name": "邢台",
                "pinyin": "Xingtai",
                "zip": "0319",
                "szm": "XT"
            },
            {
                "label": "西宁|Xining|XN|0971",
                "name": "西宁",
                "pinyin": "Xining",
                "zip": "0971",
                "szm": "XN"
            },
            {
                "label": "新乡|Xinxiang|XX|0373",
                "name": "新乡",
                "pinyin": "Xinxiang",
                "zip": "0373",
                "szm": "XX"
            },
            {
                "label": "信阳|Xinyang|XY|0376",
                "name": "信阳",
                "pinyin": "Xinyang",
                "zip": "0376",
                "szm": "XY"
            },
            {
                "label": "新余|Xinyu|XY|0790",
                "name": "新余",
                "pinyin": "Xinyu",
                "zip": "0790",
                "szm": "XY"
            },
            {
                "label": "忻州|Xinzhou|XZ|0350",
                "name": "忻州",
                "pinyin": "Xinzhou",
                "zip": "0350",
                "szm": "XZ"
            },
            {
                "label": "西双版纳|Xishuangbanna|XSBN|0691",
                "name": "西双版纳",
                "pinyin": "Xishuangbanna",
                "zip": "0691",
                "szm": "XSBN"
            },
            {
                "label": "宣城|Xuancheng|XC|0563",
                "name": "宣城",
                "pinyin": "Xuancheng",
                "zip": "0563",
                "szm": "XC"
            },
            {
                "label": "雅安|Yaan|YA|0835",
                "name": "雅安",
                "pinyin": "Yaan",
                "zip": "0835",
                "szm": "YA"
            },
            {
                "label": "延安|Yanan|YA|0911",
                "name": "延安",
                "pinyin": "Yanan",
                "zip": "0911",
                "szm": "YA"
            },
            {
                "label": "延边|Yanbian|YB|0433",
                "name": "延边",
                "pinyin": "Yanbian",
                "zip": "0433",
                "szm": "YB"
            },
            {
                "label": "盐城|Yancheng|YC|0515",
                "name": "盐城",
                "pinyin": "Yancheng",
                "zip": "0515",
                "szm": "YC"
            },
            {
                "label": "阳江|Yangjiang|YJ|0662",
                "name": "阳江",
                "pinyin": "Yangjiang",
                "zip": "0662",
                "szm": "YJ"
            },
            {
                "label": "阳泉|Yangquan|YQ|0353",
                "name": "阳泉",
                "pinyin": "Yangquan",
                "zip": "0353",
                "szm": "YQ"
            },
            {
                "label": "宜宾|Yibin|YB|0831",
                "name": "宜宾",
                "pinyin": "Yibin",
                "zip": "0831",
                "szm": "YB"
            },
            {
                "label": "宜昌|Yichang|YC|0717",
                "name": "宜昌",
                "pinyin": "Yichang",
                "zip": "0717",
                "szm": "YC"
            },
            {
                "label": "伊春|Yichun|YC|0458",
                "name": "伊春",
                "pinyin": "Yichun",
                "zip": "0458",
                "szm": "YC"
            },
            {
                "label": "宜春|Yichun|YC|0795",
                "name": "宜春",
                "pinyin": "Yichun",
                "zip": "0795",
                "szm": "YC"
            },
            {
                "label": "伊犁哈萨克|Yilihasake|YLHSK|0999",
                "name": "伊犁哈萨克",
                "pinyin": "Yilihasake",
                "zip": "0999",
                "szm": "YLHSK"
            },
            {
                "label": "银川|Yinchuan|YC|0951",
                "name": "银川",
                "pinyin": "Yinchuan",
                "zip": "0951",
                "szm": "YC"
            },
            {
                "label": "营口|Yingkou|YK|0417",
                "name": "营口",
                "pinyin": "Yingkou",
                "zip": "0417",
                "szm": "YK"
            },
            {
                "label": "鹰潭|Yingtan|YT|0701",
                "name": "鹰潭",
                "pinyin": "Yingtan",
                "zip": "0701",
                "szm": "YT"
            },
            {
                "label": "益阳|Yiyang|YY|0737",
                "name": "益阳",
                "pinyin": "Yiyang",
                "zip": "0737",
                "szm": "YY"
            },
            {
                "label": "永州|Yongzhou|YZ|0746",
                "name": "永州",
                "pinyin": "Yongzhou",
                "zip": "0746",
                "szm": "YZ"
            },
            {
                "label": "岳阳|Yueyang|YY|0730",
                "name": "岳阳",
                "pinyin": "Yueyang",
                "zip": "0730",
                "szm": "YY"
            },
            {
                "label": "玉林|Yulin|YL|0775",
                "name": "玉林",
                "pinyin": "Yulin",
                "zip": "0775",
                "szm": "YL"
            },
            {
                "label": "榆林|Yulin|YL|0912",
                "name": "榆林",
                "pinyin": "Yulin",
                "zip": "0912",
                "szm": "YL"
            },
            {
                "label": "运城|Yuncheng|YC|0359",
                "name": "运城",
                "pinyin": "Yuncheng",
                "zip": "0359",
                "szm": "YC"
            },
            {
                "label": "云浮|Yunfu|YF|0766",
                "name": "云浮",
                "pinyin": "Yunfu",
                "zip": "0766",
                "szm": "YF"
            },
            {
                "label": "玉树|Yushu|YS|0976",
                "name": "玉树",
                "pinyin": "Yushu",
                "zip": "0976",
                "szm": "YS"
            },
            {
                "label": "玉溪|Yuxi|YX|0877",
                "name": "玉溪",
                "pinyin": "Yuxi",
                "zip": "0877",
                "szm": "YX"
            },
            {
                "label": "枣庄|Zaozhuang|ZZ|0623",
                "name": "枣庄",
                "pinyin": "Zaozhuang",
                "zip": "0623",
                "szm": "ZZ"
            },
            {
                "label": "张家界|Zhangjiajie|ZJJ|0744",
                "name": "张家界",
                "pinyin": "Zhangjiajie",
                "zip": "0744",
                "szm": "ZJJ"
            },
            {
                "label": "张家口|Zhangjiakou|ZJK|0313",
                "name": "张家口",
                "pinyin": "Zhangjiakou",
                "zip": "0313",
                "szm": "ZJK"
            },
            {
                "label": "张掖|Zhangye|ZY|0936",
                "name": "张掖",
                "pinyin": "Zhangye",
                "zip": "0936",
                "szm": "ZY"
            },
            {
                "label": "湛江|Zhanjiang|ZJ|0759",
                "name": "湛江",
                "pinyin": "Zhanjiang",
                "zip": "0759",
                "szm": "ZJ"
            },
            {
                "label": "肇庆|Zhaoqing|ZQ|0758",
                "name": "肇庆",
                "pinyin": "Zhaoqing",
                "zip": "0758",
                "szm": "ZQ"
            },
            {
                "label": "昭通|Zhaotong|ZT|0870",
                "name": "昭通",
                "pinyin": "Zhaotong",
                "zip": "0870",
                "szm": "ZT"
            },
            {
                "label": "镇江|Zhenjiang|ZJ|0511",
                "name": "镇江",
                "pinyin": "Zhenjiang",
                "zip": "0511",
                "szm": "ZJ"
            },
            {
                "label": "中卫|Zhongwei|ZW|0955",
                "name": "中卫",
                "pinyin": "Zhongwei",
                "zip": "0955",
                "szm": "ZW"
            },
            {
                "label": "周口|Zhoukou|ZK|0394",
                "name": "周口",
                "pinyin": "Zhoukou",
                "zip": "0394",
                "szm": "ZK"
            },
            {
                "label": "舟山|Zhoushan|ZS|0580",
                "name": "舟山",
                "pinyin": "Zhoushan",
                "zip": "0580",
                "szm": "ZS"
            },
            {
                "label": "驻马店|Zhumadian|ZMD|0396",
                "name": "驻马店",
                "pinyin": "Zhumadian",
                "zip": "0396",
                "szm": "ZMD"
            },
            {
                "label": "株洲|Zhuzhou|ZZ|0731",
                "name": "株洲",
                "pinyin": "Zhuzhou",
                "zip": "0731",
                "szm": "ZZ"
            },
            {
                "label": "淄博|Zibo|ZB|0533",
                "name": "淄博",
                "pinyin": "Zibo",
                "zip": "0533",
                "szm": "ZB"
            },
            {
                "label": "自贡|Zigong|ZG|0813",
                "name": "自贡",
                "pinyin": "Zigong",
                "zip": "0813",
                "szm": "ZG"
            },
            {
                "label": "资阳|Ziyang|ZY|028",
                "name": "资阳",
                "pinyin": "Ziyang",
                "zip": "028",
                "szm": "ZY"
            },
            {
                "label": "遵义|Zunyi|ZY|0852",
                "name": "遵义",
                "pinyin": "Zunyi",
                "zip": "0852",
                "szm": "ZY"
            },
            {
                "label": "阿城|Acheng|AC|0451",
                "name": "阿城",
                "pinyin": "Acheng",
                "zip": "0451",
                "szm": "AC"
            },
            {
                "label": "安福|Anfu|AF|0796",
                "name": "安福",
                "pinyin": "Anfu",
                "zip": "0796",
                "szm": "AF"
            },
            {
                "label": "安吉|Anji|AJ|0572",
                "name": "安吉",
                "pinyin": "Anji",
                "zip": "0572",
                "szm": "AJ"
            },
            {
                "label": "安宁|Anning|AN|0871",
                "name": "安宁",
                "pinyin": "Anning",
                "zip": "0871",
                "szm": "AN"
            },
            {
                "label": "安丘|Anqiu|AQ|0536",
                "name": "安丘",
                "pinyin": "Anqiu",
                "zip": "0536",
                "szm": "AQ"
            },
            {
                "label": "安溪|Anxi|AX|0595",
                "name": "安溪",
                "pinyin": "Anxi",
                "zip": "0595",
                "szm": "AX"
            },
            {
                "label": "安义|Anyi|AY|0791",
                "name": "安义",
                "pinyin": "Anyi",
                "zip": "0791",
                "szm": "AY"
            },
            {
                "label": "安远|Anyuan|AY|0797",
                "name": "安远",
                "pinyin": "Anyuan",
                "zip": "0797",
                "szm": "AY"
            },
            {
                "label": "宝应|Baoying|BY|0514",
                "name": "宝应",
                "pinyin": "Baoying",
                "zip": "0514",
                "szm": "BY"
            },
            {
                "label": "巴彦|Bayan|BY|0451",
                "name": "巴彦",
                "pinyin": "Bayan",
                "zip": "0451",
                "szm": "BY"
            },
            {
                "label": "滨海|Binhai|BH|0515",
                "name": "滨海",
                "pinyin": "Binhai",
                "zip": "0515",
                "szm": "BH"
            },
            {
                "label": "宾县|Binxian|BX|0451",
                "name": "宾县",
                "pinyin": "Binxian",
                "zip": "0451",
                "szm": "BX"
            },
            {
                "label": "宾阳|Binyang|BY|0771",
                "name": "宾阳",
                "pinyin": "Binyang",
                "zip": "0771",
                "szm": "BY"
            },
            {
                "label": "璧山|Bishan|BS|023",
                "name": "璧山",
                "pinyin": "Bishan",
                "zip": "023",
                "szm": "BS"
            },
            {
                "label": "博爱|Boai|BA|0391",
                "name": "博爱",
                "pinyin": "Boai",
                "zip": "0391",
                "szm": "BA"
            },
            {
                "label": "博罗|Boluo|BL|0752",
                "name": "博罗",
                "pinyin": "Boluo",
                "zip": "0752",
                "szm": "BL"
            },
            {
                "label": "博兴|Boxing|BX|0543",
                "name": "博兴",
                "pinyin": "Boxing",
                "zip": "0543",
                "szm": "BX"
            },
            {
                "label": "苍南|Cangnan|CN|0577",
                "name": "苍南",
                "pinyin": "Cangnan",
                "zip": "0577",
                "szm": "CN"
            },
            {
                "label": "苍山|Cangshan|CS|0539",
                "name": "苍山",
                "pinyin": "Cangshan",
                "zip": "0539",
                "szm": "CS"
            },
            {
                "label": "曹县|Caoxian|CX|0530",
                "name": "曹县",
                "pinyin": "Caoxian",
                "zip": "0530",
                "szm": "CX"
            },
            {
                "label": "长岛|Changdao|CD|0535",
                "name": "长岛",
                "pinyin": "Changdao",
                "zip": "0535",
                "szm": "CD"
            },
            {
                "label": "长丰|Changfeng|CF|0551",
                "name": "长丰",
                "pinyin": "Changfeng",
                "zip": "0551",
                "szm": "CF"
            },
            {
                "label": "长海|Changhai|CH|0411",
                "name": "长海",
                "pinyin": "Changhai",
                "zip": "0411",
                "szm": "CH"
            },
            {
                "label": "长乐|Changle|CL|0591",
                "name": "长乐",
                "pinyin": "Changle",
                "zip": "0591",
                "szm": "CL"
            },
            {
                "label": "昌乐|Changle|CL|0536",
                "name": "昌乐",
                "pinyin": "Changle",
                "zip": "0536",
                "szm": "CL"
            },
            {
                "label": "常山|Changshan|CS|0570",
                "name": "常山",
                "pinyin": "Changshan",
                "zip": "0570",
                "szm": "CS"
            },
            {
                "label": "常熟|Changshu|CS|0512",
                "name": "常熟",
                "pinyin": "Changshu",
                "zip": "0512",
                "szm": "CS"
            },
            {
                "label": "长泰|Changtai|CT|0596",
                "name": "长泰",
                "pinyin": "Changtai",
                "zip": "0596",
                "szm": "CT"
            },
            {
                "label": "长汀|Changting|CT|0597",
                "name": "长汀",
                "pinyin": "Changting",
                "zip": "0597",
                "szm": "CT"
            },
            {
                "label": "长兴|Changxing|CX|0572",
                "name": "长兴",
                "pinyin": "Changxing",
                "zip": "0572",
                "szm": "CX"
            },
            {
                "label": "昌邑|Changyi|CY|0536",
                "name": "昌邑",
                "pinyin": "Changyi",
                "zip": "0536",
                "szm": "CY"
            },
            {
                "label": "潮安|Chaoan|CA|0768",
                "name": "潮安",
                "pinyin": "Chaoan",
                "zip": "0768",
                "szm": "CA"
            },
            {
                "label": "呈贡|Chenggong|CG|0871",
                "name": "呈贡",
                "pinyin": "Chenggong",
                "zip": "0871",
                "szm": "CG"
            },
            {
                "label": "城口|Chengkou|CK|023",
                "name": "城口",
                "pinyin": "Chengkou",
                "zip": "023",
                "szm": "CK"
            },
            {
                "label": "成武|Chengwu|CW|0530",
                "name": "成武",
                "pinyin": "Chengwu",
                "zip": "0530",
                "szm": "CW"
            },
            {
                "label": "茌平|Chiping|CP|0635",
                "name": "茌平",
                "pinyin": "Chiping",
                "zip": "0635",
                "szm": "CP"
            },
            {
                "label": "崇仁|Chongren|CR|0794",
                "name": "崇仁",
                "pinyin": "Chongren",
                "zip": "0794",
                "szm": "CR"
            },
            {
                "label": "崇义|Chongyi|CY|0797",
                "name": "崇义",
                "pinyin": "Chongyi",
                "zip": "0797",
                "szm": "CY"
            },
            {
                "label": "崇州|Chongzhou|CZ|028",
                "name": "崇州",
                "pinyin": "Chongzhou",
                "zip": "028",
                "szm": "CZ"
            },
            {
                "label": "淳安|Chunan|CA|0571",
                "name": "淳安",
                "pinyin": "Chunan",
                "zip": "0571",
                "szm": "CA"
            },
            {
                "label": "慈溪|Cixi|CX|0574",
                "name": "慈溪",
                "pinyin": "Cixi",
                "zip": "0574",
                "szm": "CX"
            },
            {
                "label": "从化|Conghua|CH|020",
                "name": "从化",
                "pinyin": "Conghua",
                "zip": "020",
                "szm": "CH"
            },
            {
                "label": "枞阳|Congyang|ZY|0556",
                "name": "枞阳",
                "pinyin": "Congyang",
                "zip": "0556",
                "szm": "ZY"
            },
            {
                "label": "大丰|Dafeng|DF|0515",
                "name": "大丰",
                "pinyin": "Dafeng",
                "zip": "0515",
                "szm": "DF"
            },
            {
                "label": "岱山|Daishan|DS|0580",
                "name": "岱山",
                "pinyin": "Daishan",
                "zip": "0580",
                "szm": "DS"
            },
            {
                "label": "砀山|Dangshan|DS|0557",
                "name": "砀山",
                "pinyin": "Dangshan",
                "zip": "0557",
                "szm": "DS"
            },
            {
                "label": "当涂|Dangtu|DT|0555",
                "name": "当涂",
                "pinyin": "Dangtu",
                "zip": "0555",
                "szm": "DT"
            },
            {
                "label": "单县|Danxian|SX|0530",
                "name": "单县",
                "pinyin": "Danxian",
                "zip": "0530",
                "szm": "SX"
            },
            {
                "label": "丹阳|Danyang|DY|0511",
                "name": "丹阳",
                "pinyin": "Danyang",
                "zip": "0511",
                "szm": "DY"
            },
            {
                "label": "大埔|Dapu|DP|0753",
                "name": "大埔",
                "pinyin": "Dapu",
                "zip": "0753",
                "szm": "DP"
            },
            {
                "label": "大田|Datian|DT|0598",
                "name": "大田",
                "pinyin": "Datian",
                "zip": "0598",
                "szm": "DT"
            },
            {
                "label": "大邑|Dayi|DY|028",
                "name": "大邑",
                "pinyin": "Dayi",
                "zip": "028",
                "szm": "DY"
            },
            {
                "label": "大余|Dayu|DY|0797",
                "name": "大余",
                "pinyin": "Dayu",
                "zip": "0797",
                "szm": "DY"
            },
            {
                "label": "大足|Dazu|DZ|023",
                "name": "大足",
                "pinyin": "Dazu",
                "zip": "023",
                "szm": "DZ"
            },
            {
                "label": "德安|Dean|DA|0792",
                "name": "德安",
                "pinyin": "Dean",
                "zip": "0792",
                "szm": "DA"
            },
            {
                "label": "德化|Dehua|DH|0595",
                "name": "德化",
                "pinyin": "Dehua",
                "zip": "0595",
                "szm": "DH"
            },
            {
                "label": "德惠|Dehui|DH|0431",
                "name": "德惠",
                "pinyin": "Dehui",
                "zip": "0431",
                "szm": "DH"
            },
            {
                "label": "登封|Dengfeng|DF|0371",
                "name": "登封",
                "pinyin": "Dengfeng",
                "zip": "0371",
                "szm": "DF"
            },
            {
                "label": "德清|Deqing|DQ|0572",
                "name": "德清",
                "pinyin": "Deqing",
                "zip": "0572",
                "szm": "DQ"
            },
            {
                "label": "德庆|Deqing|DQ|0758",
                "name": "德庆",
                "pinyin": "Deqing",
                "zip": "0758",
                "szm": "DQ"
            },
            {
                "label": "德兴|Dexing|DX|0793",
                "name": "德兴",
                "pinyin": "Dexing",
                "zip": "0793",
                "szm": "DX"
            },
            {
                "label": "电白|Dianbai|DB|0668",
                "name": "电白",
                "pinyin": "Dianbai",
                "zip": "0668",
                "szm": "DB"
            },
            {
                "label": "垫江|Dianjiang|DJ|023",
                "name": "垫江",
                "pinyin": "Dianjiang",
                "zip": "023",
                "szm": "DJ"
            },
            {
                "label": "定南|Dingnan|DN|0797",
                "name": "定南",
                "pinyin": "Dingnan",
                "zip": "0797",
                "szm": "DN"
            },
            {
                "label": "定陶|Dingtao|DY|0530",
                "name": "定陶",
                "pinyin": "Dingtao",
                "zip": "0530",
                "szm": "DY"
            },
            {
                "label": "定远|Dingyuan|DY|0550",
                "name": "定远",
                "pinyin": "Dingyuan",
                "zip": "0550",
                "szm": "DY"
            },
            {
                "label": "东阿|Donga|DA|0635",
                "name": "东阿",
                "pinyin": "Donga",
                "zip": "0635",
                "szm": "DA"
            },
            {
                "label": "东海|Donghai|DH|0518",
                "name": "东海",
                "pinyin": "Donghai",
                "zip": "0518",
                "szm": "DH"
            },
            {
                "label": "东明|Dongming|DM|0530",
                "name": "东明",
                "pinyin": "Dongming",
                "zip": "0530",
                "szm": "DM"
            },
            {
                "label": "东平|Dongping|DP|0538",
                "name": "东平",
                "pinyin": "Dongping",
                "zip": "0538",
                "szm": "DP"
            },
            {
                "label": "东山|Dongshan|DS|0596",
                "name": "东山",
                "pinyin": "Dongshan",
                "zip": "0596",
                "szm": "DS"
            },
            {
                "label": "东台|Dongtai|DT|0515",
                "name": "东台",
                "pinyin": "Dongtai",
                "zip": "0515",
                "szm": "DT"
            },
            {
                "label": "洞头|Dongtou|DT|0577",
                "name": "洞头",
                "pinyin": "Dongtou",
                "zip": "0577",
                "szm": "DT"
            },
            {
                "label": "东乡|Dongxiang|DX|0794",
                "name": "东乡",
                "pinyin": "Dongxiang",
                "zip": "0794",
                "szm": "DX"
            },
            {
                "label": "东阳|Dongyang|DY|0579",
                "name": "东阳",
                "pinyin": "Dongyang",
                "zip": "0579",
                "szm": "DY"
            },
            {
                "label": "东源|Dongyuan|DY|0762",
                "name": "东源",
                "pinyin": "Dongyuan",
                "zip": "0762",
                "szm": "DY"
            },
            {
                "label": "东至|Dongzhi|DZ|0566",
                "name": "东至",
                "pinyin": "Dongzhi",
                "zip": "0566",
                "szm": "DZ"
            },
            {
                "label": "都昌|Duchang|DC|0792",
                "name": "都昌",
                "pinyin": "Duchang",
                "zip": "0792",
                "szm": "DC"
            },
            {
                "label": "都江堰|Dujiangyan|DJY|028",
                "name": "都江堰",
                "pinyin": "Dujiangyan",
                "zip": "028",
                "szm": "DJY"
            },
            {
                "label": "恩平|Enping|EP|0750",
                "name": "恩平",
                "pinyin": "Enping",
                "zip": "0750",
                "szm": "EP"
            },
            {
                "label": "法库|Faku|FK|024",
                "name": "法库",
                "pinyin": "Faku",
                "zip": "024",
                "szm": "FK"
            },
            {
                "label": "繁昌|Fanchang|PC|0553",
                "name": "繁昌",
                "pinyin": "Fanchang",
                "zip": "0553",
                "szm": "PC"
            },
            {
                "label": "方正|Fangzheng|FZ|0451",
                "name": "方正",
                "pinyin": "Fangzheng",
                "zip": "0451",
                "szm": "FZ"
            },
            {
                "label": "肥城|Feicheng|FC|0538",
                "name": "肥城",
                "pinyin": "Feicheng",
                "zip": "0538",
                "szm": "FC"
            },
            {
                "label": "肥东|Feidong|FD|0551",
                "name": "肥东",
                "pinyin": "Feidong",
                "zip": "0551",
                "szm": "FD"
            },
            {
                "label": "肥西|Feixi|FX|0551",
                "name": "肥西",
                "pinyin": "Feixi",
                "zip": "0551",
                "szm": "FX"
            },
            {
                "label": "费县|Feixian|FX|0539",
                "name": "费县",
                "pinyin": "Feixian",
                "zip": "0539",
                "szm": "FX"
            },
            {
                "label": "丰城|Fengcheng|FC|0795",
                "name": "丰城",
                "pinyin": "Fengcheng",
                "zip": "0795",
                "szm": "FC"
            },
            {
                "label": "丰都|Fengdu|FD|023",
                "name": "丰都",
                "pinyin": "Fengdu",
                "zip": "023",
                "szm": "FD"
            },
            {
                "label": "奉化|Fenghua|FH|0574",
                "name": "奉化",
                "pinyin": "Fenghua",
                "zip": "0574",
                "szm": "FH"
            },
            {
                "label": "奉节|Fengjie|FJ|023",
                "name": "奉节",
                "pinyin": "Fengjie",
                "zip": "023",
                "szm": "FJ"
            },
            {
                "label": "封开|Fengkai|FK|0758",
                "name": "封开",
                "pinyin": "Fengkai",
                "zip": "0758",
                "szm": "FK"
            },
            {
                "label": "丰顺|Fengshun|FS|0753",
                "name": "丰顺",
                "pinyin": "Fengshun",
                "zip": "0753",
                "szm": "FS"
            },
            {
                "label": "凤台|Fengtai|FT|0554",
                "name": "凤台",
                "pinyin": "Fengtai",
                "zip": "0554",
                "szm": "FT"
            },
            {
                "label": "丰县|Fengxian|FX|0516",
                "name": "丰县",
                "pinyin": "Fengxian",
                "zip": "0516",
                "szm": "FX"
            },
            {
                "label": "奉新|Fengxin|FX|0795",
                "name": "奉新",
                "pinyin": "Fengxin",
                "zip": "0795",
                "szm": "FX"
            },
            {
                "label": "凤阳|Fengyang|FY|0550",
                "name": "凤阳",
                "pinyin": "Fengyang",
                "zip": "0550",
                "szm": "FY"
            },
            {
                "label": "分宜|Fenyi|FY|0790",
                "name": "分宜",
                "pinyin": "Fenyi",
                "zip": "0790",
                "szm": "FY"
            },
            {
                "label": "佛冈|Fogang|BG|0763",
                "name": "佛冈",
                "pinyin": "Fogang",
                "zip": "0763",
                "szm": "BG"
            },
            {
                "label": "福安|Fuan|FA|0593",
                "name": "福安",
                "pinyin": "Fuan",
                "zip": "0593",
                "szm": "FA"
            },
            {
                "label": "福鼎|Fuding|FD|0593",
                "name": "福鼎",
                "pinyin": "Fuding",
                "zip": "0593",
                "szm": "FD"
            },
            {
                "label": "浮梁|Fuliang|FL|0798",
                "name": "浮梁",
                "pinyin": "Fuliang",
                "zip": "0798",
                "szm": "FL"
            },
            {
                "label": "富民|Fumin|FM|0871",
                "name": "富民",
                "pinyin": "Fumin",
                "zip": "0871",
                "szm": "FM"
            },
            {
                "label": "阜南|Funan|FN|0558",
                "name": "阜南",
                "pinyin": "Funan",
                "zip": "0558",
                "szm": "FN"
            },
            {
                "label": "阜宁|Funing|FN|0515",
                "name": "阜宁",
                "pinyin": "Funing",
                "zip": "0515",
                "szm": "FN"
            },
            {
                "label": "福清|Fuqing|FQ|0591",
                "name": "福清",
                "pinyin": "Fuqing",
                "zip": "0591",
                "szm": "FQ"
            },
            {
                "label": "富阳|Fuyang|FY|0571",
                "name": "富阳",
                "pinyin": "Fuyang",
                "zip": "0571",
                "szm": "FY"
            },
            {
                "label": "赣县|Ganxian|GX|0797",
                "name": "赣县",
                "pinyin": "Ganxian",
                "zip": "0797",
                "szm": "GX"
            },
            {
                "label": "赣榆|Ganyu|GY|0518",
                "name": "赣榆",
                "pinyin": "Ganyu",
                "zip": "0518",
                "szm": "GY"
            },
            {
                "label": "高安|Gaoan|GA|0795",
                "name": "高安",
                "pinyin": "Gaoan",
                "zip": "0795",
                "szm": "GA"
            },
            {
                "label": "藁城|Gaocheng|GC|0311",
                "name": "藁城",
                "pinyin": "Gaocheng",
                "zip": "0311",
                "szm": "GC"
            },
            {
                "label": "高淳|Gaochun|GC|025",
                "name": "高淳",
                "pinyin": "Gaochun",
                "zip": "025",
                "szm": "GC"
            },
            {
                "label": "皋兰|Gaolan|GL|0931",
                "name": "皋兰",
                "pinyin": "Gaolan",
                "zip": "0931",
                "szm": "GL"
            },
            {
                "label": "高陵|Gaoling|GL|029",
                "name": "高陵",
                "pinyin": "Gaoling",
                "zip": "029",
                "szm": "GL"
            },
            {
                "label": "高密|Gaomi|GM|0536",
                "name": "高密",
                "pinyin": "Gaomi",
                "zip": "0536",
                "szm": "GM"
            },
            {
                "label": "高青|Gaoqing|GQ|0533",
                "name": "高青",
                "pinyin": "Gaoqing",
                "zip": "0533",
                "szm": "GQ"
            },
            {
                "label": "高唐|Gaotang|GT|0635",
                "name": "高唐",
                "pinyin": "Gaotang",
                "zip": "0635",
                "szm": "GT"
            },
            {
                "label": "高要|Gaoyao|GY|0758",
                "name": "高要",
                "pinyin": "Gaoyao",
                "zip": "0758",
                "szm": "GY"
            },
            {
                "label": "高邑|Gaoyi|GY|0311",
                "name": "高邑",
                "pinyin": "Gaoyi",
                "zip": "0311",
                "szm": "GY"
            },
            {
                "label": "高邮|Gaoyou|GY|0514",
                "name": "高邮",
                "pinyin": "Gaoyou",
                "zip": "0514",
                "szm": "GY"
            },
            {
                "label": "高州|Gaozhou|GZ|0668",
                "name": "高州",
                "pinyin": "Gaozhou",
                "zip": "0668",
                "szm": "GZ"
            },
            {
                "label": "巩义|Gongyi|GY|0371",
                "name": "巩义",
                "pinyin": "Gongyi",
                "zip": "0371",
                "szm": "GY"
            },
            {
                "label": "广昌|Guangchang|GC|0794",
                "name": "广昌",
                "pinyin": "Guangchang",
                "zip": "0794",
                "szm": "GC"
            },
            {
                "label": "广德|Guangde|GD|0563",
                "name": "广德",
                "pinyin": "Guangde",
                "zip": "0563",
                "szm": "GD"
            },
            {
                "label": "广丰|Guangfeng|GF|0793",
                "name": "广丰",
                "pinyin": "Guangfeng",
                "zip": "0793",
                "szm": "GF"
            },
            {
                "label": "广宁|Guangning|GN|0758",
                "name": "广宁",
                "pinyin": "Guangning",
                "zip": "0758",
                "szm": "GN"
            },
            {
                "label": "广饶|Guangrao|GR|0546",
                "name": "广饶",
                "pinyin": "Guangrao",
                "zip": "0546",
                "szm": "GR"
            },
            {
                "label": "光泽|Guangze|GZ|0599",
                "name": "光泽",
                "pinyin": "Guangze",
                "zip": "0599",
                "szm": "GZ"
            },
            {
                "label": "灌南|Guannan|GN|0518",
                "name": "灌南",
                "pinyin": "Guannan",
                "zip": "0518",
                "szm": "GN"
            },
            {
                "label": "冠县|Guanxian|GX|0635",
                "name": "冠县",
                "pinyin": "Guanxian",
                "zip": "0635",
                "szm": "GX"
            },
            {
                "label": "灌云|Guanyun|GY|0518",
                "name": "灌云",
                "pinyin": "Guanyun",
                "zip": "0518",
                "szm": "GY"
            },
            {
                "label": "贵溪|Guixi|GX|0701",
                "name": "贵溪",
                "pinyin": "Guixi",
                "zip": "0701",
                "szm": "GX"
            },
            {
                "label": "古田|Gutian|GT|0593",
                "name": "古田",
                "pinyin": "Gutian",
                "zip": "0593",
                "szm": "GT"
            },
            {
                "label": "固镇|Guzhen|GZ|0552",
                "name": "固镇",
                "pinyin": "Guzhen",
                "zip": "0552",
                "szm": "GZ"
            },
            {
                "label": "海安|Haian|HA|0513",
                "name": "海安",
                "pinyin": "Haian",
                "zip": "0513",
                "szm": "HA"
            },
            {
                "label": "海丰|Haifeng|HF|0660",
                "name": "海丰",
                "pinyin": "Haifeng",
                "zip": "0660",
                "szm": "HF"
            },
            {
                "label": "海门|Haimen|HM|0513",
                "name": "海门",
                "pinyin": "Haimen",
                "zip": "0513",
                "szm": "HM"
            },
            {
                "label": "海宁|Haining|HN|0573",
                "name": "海宁",
                "pinyin": "Haining",
                "zip": "0573",
                "szm": "HN"
            },
            {
                "label": "海盐|Haiyan|HY|0573",
                "name": "海盐",
                "pinyin": "Haiyan",
                "zip": "0573",
                "szm": "HY"
            },
            {
                "label": "海阳|Haiyang|HY|0535",
                "name": "海阳",
                "pinyin": "Haiyang",
                "zip": "0535",
                "szm": "HY"
            },
            {
                "label": "含山|Hanshan|HS|0565",
                "name": "含山",
                "pinyin": "Hanshan",
                "zip": "0565",
                "szm": "HS"
            },
            {
                "label": "合川|Hechuan|HC|023",
                "name": "合川",
                "pinyin": "Hechuan",
                "zip": "023",
                "szm": "HC"
            },
            {
                "label": "横峰|Hengfeng|HF|0793",
                "name": "横峰",
                "pinyin": "Hengfeng",
                "zip": "0793",
                "szm": "HF"
            },
            {
                "label": "横县|Hengxian|HX|0771",
                "name": "横县",
                "pinyin": "Hengxian",
                "zip": "0771",
                "szm": "HX"
            },
            {
                "label": "和平|Heping|HP|0762",
                "name": "和平",
                "pinyin": "Heping",
                "zip": "0762",
                "szm": "HP"
            },
            {
                "label": "鹤山|Heshan|HS|0750",
                "name": "鹤山",
                "pinyin": "Heshan",
                "zip": "0750",
                "szm": "HS"
            },
            {
                "label": "和县|Hexian|HX|0565",
                "name": "和县",
                "pinyin": "Hexian",
                "zip": "0565",
                "szm": "HX"
            },
            {
                "label": "洪泽|Hongze|HZ|0517",
                "name": "洪泽",
                "pinyin": "Hongze",
                "zip": "0517",
                "szm": "HZ"
            },
            {
                "label": "华安|Huaan|HA|0596",
                "name": "华安",
                "pinyin": "Huaan",
                "zip": "0596",
                "szm": "HA"
            },
            {
                "label": "桦甸|Huadian|HD|0423",
                "name": "桦甸",
                "pinyin": "Huadian",
                "zip": "0423",
                "szm": "HD"
            },
            {
                "label": "怀集|Huaiji|HJ|0758",
                "name": "怀集",
                "pinyin": "Huaiji",
                "zip": "0758",
                "szm": "HJ"
            },
            {
                "label": "怀宁|Huaining|HN|0556",
                "name": "怀宁",
                "pinyin": "Huaining",
                "zip": "0556",
                "szm": "HN"
            },
            {
                "label": "怀远|Huaiyuan|HY|0552",
                "name": "怀远",
                "pinyin": "Huaiyuan",
                "zip": "0552",
                "szm": "HY"
            },
            {
                "label": "桓台|Huantai|HT|0533",
                "name": "桓台",
                "pinyin": "Huantai",
                "zip": "0533",
                "szm": "HT"
            },
            {
                "label": "化州|Huazhou|HZ|0668",
                "name": "化州",
                "pinyin": "Huazhou",
                "zip": "0668",
                "szm": "HZ"
            },
            {
                "label": "惠安|Huian|HA|0595",
                "name": "惠安",
                "pinyin": "Huian",
                "zip": "0595",
                "szm": "HA"
            },
            {
                "label": "会昌|Huichang|KC|0797",
                "name": "会昌",
                "pinyin": "Huichang",
                "zip": "0797",
                "szm": "KC"
            },
            {
                "label": "惠东|Huidong|HD|0752",
                "name": "惠东",
                "pinyin": "Huidong",
                "zip": "0752",
                "szm": "HD"
            },
            {
                "label": "惠来|Huilai|HL|0663",
                "name": "惠来",
                "pinyin": "Huilai",
                "zip": "0663",
                "szm": "HL"
            },
            {
                "label": "惠民|Huimin|HM|0543",
                "name": "惠民",
                "pinyin": "Huimin",
                "zip": "0543",
                "szm": "HM"
            },
            {
                "label": "湖口|Hukou|HK|0792",
                "name": "湖口",
                "pinyin": "Hukou",
                "zip": "0792",
                "szm": "HK"
            },
            {
                "label": "呼兰|Hulan|HL|0451",
                "name": "呼兰",
                "pinyin": "Hulan",
                "zip": "0451",
                "szm": "HL"
            },
            {
                "label": "霍邱|Huoqiu|HQ|0564",
                "name": "霍邱",
                "pinyin": "Huoqiu",
                "zip": "0564",
                "szm": "HQ"
            },
            {
                "label": "霍山|Huoshan|HS|0564",
                "name": "霍山",
                "pinyin": "Huoshan",
                "zip": "0564",
                "szm": "HS"
            },
            {
                "label": "户县|Huxian|HX|029",
                "name": "户县",
                "pinyin": "Huxian",
                "zip": "029",
                "szm": "HX"
            },
            {
                "label": "建德|Jiande|JD|0571",
                "name": "建德",
                "pinyin": "Jiande",
                "zip": "0571",
                "szm": "JD"
            },
            {
                "label": "江都|Jiangdu|JD|0514",
                "name": "江都",
                "pinyin": "Jiangdu",
                "zip": "0514",
                "szm": "JD"
            },
            {
                "label": "江津|Jiangjin|JJ|023",
                "name": "江津",
                "pinyin": "Jiangjin",
                "zip": "023",
                "szm": "JJ"
            },
            {
                "label": "将乐|Jiangle|QY|0598",
                "name": "将乐",
                "pinyin": "Jiangle",
                "zip": "0598",
                "szm": "QY"
            },
            {
                "label": "江山|Jiangshan|JS|0570",
                "name": "江山",
                "pinyin": "Jiangshan",
                "zip": "0570",
                "szm": "JS"
            },
            {
                "label": "姜堰|Jiangyan|JY|0523",
                "name": "姜堰",
                "pinyin": "Jiangyan",
                "zip": "0523",
                "szm": "JY"
            },
            {
                "label": "江阴|Jiangyin|JY|0510",
                "name": "江阴",
                "pinyin": "Jiangyin",
                "zip": "0510",
                "szm": "JY"
            },
            {
                "label": "建湖|Jianhu|JH|0515",
                "name": "建湖",
                "pinyin": "Jianhu",
                "zip": "0515",
                "szm": "JH"
            },
            {
                "label": "建宁|Jianning|JN|0598",
                "name": "建宁",
                "pinyin": "Jianning",
                "zip": "0598",
                "szm": "JN"
            },
            {
                "label": "建瓯|Jianou|JO|0599",
                "name": "建瓯",
                "pinyin": "Jianou",
                "zip": "0599",
                "szm": "JO"
            },
            {
                "label": "建阳|Jianyang|JY|0599",
                "name": "建阳",
                "pinyin": "Jianyang",
                "zip": "0599",
                "szm": "JY"
            },
            {
                "label": "吉安|Jian|JA|0796",
                "name": "吉安",
                "pinyin": "Jian",
                "zip": "0796",
                "szm": "JA"
            },
            {
                "label": "蛟河|Jiaohe|JH|0423",
                "name": "蛟河",
                "pinyin": "Jiaohe",
                "zip": "0423",
                "szm": "JH"
            },
            {
                "label": "蕉岭|Jiaoling|QL|0753",
                "name": "蕉岭",
                "pinyin": "Jiaoling",
                "zip": "0753",
                "szm": "QL"
            },
            {
                "label": "胶南|Jiaonan|JN|0532",
                "name": "胶南",
                "pinyin": "Jiaonan",
                "zip": "0532",
                "szm": "JN"
            },
            {
                "label": "胶州|Jiaozhou|JZ|0532",
                "name": "胶州",
                "pinyin": "Jiaozhou",
                "zip": "0532",
                "szm": "JZ"
            },
            {
                "label": "嘉善|Jiashan|JS|0573",
                "name": "嘉善",
                "pinyin": "Jiashan",
                "zip": "0573",
                "szm": "JS"
            },
            {
                "label": "嘉祥|Jiaxiang|JX|0537",
                "name": "嘉祥",
                "pinyin": "Jiaxiang",
                "zip": "0537",
                "szm": "JX"
            },
            {
                "label": "揭东|Jiedong|JD|0663",
                "name": "揭东",
                "pinyin": "Jiedong",
                "zip": "0663",
                "szm": "JD"
            },
            {
                "label": "界首|Jieshou|JS|0558",
                "name": "界首",
                "pinyin": "Jieshou",
                "zip": "0558",
                "szm": "JS"
            },
            {
                "label": "揭西|Jiexi|JX|0663",
                "name": "揭西",
                "pinyin": "Jiexi",
                "zip": "0663",
                "szm": "JX"
            },
            {
                "label": "即墨|Jimo|JM|0532",
                "name": "即墨",
                "pinyin": "Jimo",
                "zip": "0532",
                "szm": "JM"
            },
            {
                "label": "靖安|Jingan|JA|0795",
                "name": "靖安",
                "pinyin": "Jingan",
                "zip": "0795",
                "szm": "JA"
            },
            {
                "label": "旌德|Jingde|JD|0563",
                "name": "旌德",
                "pinyin": "Jingde",
                "zip": "0563",
                "szm": "JD"
            },
            {
                "label": "井冈山|Jinggangshan|JGS|0796",
                "name": "井冈山",
                "pinyin": "Jinggangshan",
                "zip": "0796",
                "szm": "JGS"
            },
            {
                "label": "靖江|Jingjiang|JJ|0523",
                "name": "靖江",
                "pinyin": "Jingjiang",
                "zip": "0523",
                "szm": "JJ"
            },
            {
                "label": "景宁|Jingning|JN|0578",
                "name": "景宁",
                "pinyin": "Jingning",
                "zip": "0578",
                "szm": "JN"
            },
            {
                "label": "泾县|Jingxian|JX|0563",
                "name": "泾县",
                "pinyin": "Jingxian",
                "zip": "0563",
                "szm": "JX"
            },
            {
                "label": "井陉|Jingxing|JX|0311",
                "name": "井陉",
                "pinyin": "Jingxing",
                "zip": "0311",
                "szm": "JX"
            },
            {
                "label": "金湖|Jinhu|JH|0517",
                "name": "金湖",
                "pinyin": "Jinhu",
                "zip": "0517",
                "szm": "JH"
            },
            {
                "label": "晋江|Jinjiang|JJ|0595",
                "name": "晋江",
                "pinyin": "Jinjiang",
                "zip": "0595",
                "szm": "JJ"
            },
            {
                "label": "金门|Jinmen|JM|0595",
                "name": "金门",
                "pinyin": "Jinmen",
                "zip": "0595",
                "szm": "JM"
            },
            {
                "label": "晋宁|Jinning|JN|0871",
                "name": "晋宁",
                "pinyin": "Jinning",
                "zip": "0871",
                "szm": "JN"
            },
            {
                "label": "金坛|Jintan|JT|0519",
                "name": "金坛",
                "pinyin": "Jintan",
                "zip": "0519",
                "szm": "JT"
            },
            {
                "label": "金堂|Jintang|JT|028",
                "name": "金堂",
                "pinyin": "Jintang",
                "zip": "028",
                "szm": "JT"
            },
            {
                "label": "进贤|Jinxian|JX|0791",
                "name": "进贤",
                "pinyin": "Jinxian",
                "zip": "0791",
                "szm": "JX"
            },
            {
                "label": "金溪|Jinxi|JX|0794",
                "name": "金溪",
                "pinyin": "Jinxi",
                "zip": "0794",
                "szm": "JX"
            },
            {
                "label": "金乡|Jinxiang|JX|0537",
                "name": "金乡",
                "pinyin": "Jinxiang",
                "zip": "0537",
                "szm": "JX"
            },
            {
                "label": "缙云|Jinyun|JY|0578",
                "name": "缙云",
                "pinyin": "Jinyun",
                "zip": "0578",
                "szm": "JY"
            },
            {
                "label": "金寨|Jinzhai|JZ|0564",
                "name": "金寨",
                "pinyin": "Jinzhai",
                "zip": "0564",
                "szm": "JZ"
            },
            {
                "label": "晋州|Jinzhou|JZ|0311",
                "name": "晋州",
                "pinyin": "Jinzhou",
                "zip": "0311",
                "szm": "JZ"
            },
            {
                "label": "吉水|Jishui|JS|0796",
                "name": "吉水",
                "pinyin": "Jishui",
                "zip": "0796",
                "szm": "JS"
            },
            {
                "label": "九江|Jiujiang|JJ|0792",
                "name": "九江",
                "pinyin": "Jiujiang",
                "zip": "0792",
                "szm": "JJ"
            },
            {
                "label": "九台|Jiutai|JT|0431",
                "name": "九台",
                "pinyin": "Jiutai",
                "zip": "0431",
                "szm": "JT"
            },
            {
                "label": "绩溪|Jixi|JX|0563",
                "name": "绩溪",
                "pinyin": "Jixi",
                "zip": "0563",
                "szm": "JX"
            },
            {
                "label": "济阳|Jiyang|JY|0531",
                "name": "济阳",
                "pinyin": "Jiyang",
                "zip": "0531",
                "szm": "JY"
            },
            {
                "label": "济源|Jiyuan|JY|0391",
                "name": "济源",
                "pinyin": "Jiyuan",
                "zip": "0391",
                "szm": "JY"
            },
            {
                "label": "鄄城|Juancheng|JC|0530",
                "name": "鄄城",
                "pinyin": "Juancheng",
                "zip": "0530",
                "szm": "JC"
            },
            {
                "label": "莒南|Junan|JN|0539",
                "name": "莒南",
                "pinyin": "Junan",
                "zip": "0539",
                "szm": "JN"
            },
            {
                "label": "句容|Jurong|JR|0511",
                "name": "句容",
                "pinyin": "Jurong",
                "zip": "0511",
                "szm": "JR"
            },
            {
                "label": "莒县|Juxian|JX|0633",
                "name": "莒县",
                "pinyin": "Juxian",
                "zip": "0633",
                "szm": "JX"
            },
            {
                "label": "巨野|Juye|JY|0530",
                "name": "巨野",
                "pinyin": "Juye",
                "zip": "0530",
                "szm": "JY"
            },
            {
                "label": "开化|Kaihua|KH|0570",
                "name": "开化",
                "pinyin": "Kaihua",
                "zip": "0570",
                "szm": "KH"
            },
            {
                "label": "开平|Kaiping|KP|0750",
                "name": "开平",
                "pinyin": "Kaiping",
                "zip": "0750",
                "szm": "KP"
            },
            {
                "label": "开县|Kaixian|KX|023",
                "name": "开县",
                "pinyin": "Kaixian",
                "zip": "023",
                "szm": "KX"
            },
            {
                "label": "开阳|Kaiyang|KY|0851",
                "name": "开阳",
                "pinyin": "Kaiyang",
                "zip": "0851",
                "szm": "KY"
            },
            {
                "label": "康平|Kangping|KP|024",
                "name": "康平",
                "pinyin": "Kangping",
                "zip": "024",
                "szm": "KP"
            },
            {
                "label": "垦利|Kenli|KL|0546",
                "name": "垦利",
                "pinyin": "Kenli",
                "zip": "0546",
                "szm": "KL"
            },
            {
                "label": "昆山|Kunshan|KS|0512",
                "name": "昆山",
                "pinyin": "Kunshan",
                "zip": "0512",
                "szm": "KS"
            },
            {
                "label": "来安|Laian|LA|0550",
                "name": "来安",
                "pinyin": "Laian",
                "zip": "0550",
                "szm": "LA"
            },
            {
                "label": "莱西|Laixi|LX|0532",
                "name": "莱西",
                "pinyin": "Laixi",
                "zip": "0532",
                "szm": "LX"
            },
            {
                "label": "莱阳|Laiyang|LY|0535",
                "name": "莱阳",
                "pinyin": "Laiyang",
                "zip": "0535",
                "szm": "LY"
            },
            {
                "label": "莱州|Laizhou|LZ|0535",
                "name": "莱州",
                "pinyin": "Laizhou",
                "zip": "0535",
                "szm": "LZ"
            },
            {
                "label": "郎溪|Langxi|LX|0563",
                "name": "郎溪",
                "pinyin": "Langxi",
                "zip": "0563",
                "szm": "LX"
            },
            {
                "label": "蓝田|Lantian|LT|029",
                "name": "蓝田",
                "pinyin": "Lantian",
                "zip": "029",
                "szm": "LT"
            },
            {
                "label": "兰溪|Lanxi|LX|0579",
                "name": "兰溪",
                "pinyin": "Lanxi",
                "zip": "0579",
                "szm": "LX"
            },
            {
                "label": "乐安|Lean|YA|0794",
                "name": "乐安",
                "pinyin": "Lean",
                "zip": "0794",
                "szm": "YA"
            },
            {
                "label": "乐昌|Lechang|YC|0751",
                "name": "乐昌",
                "pinyin": "Lechang",
                "zip": "0751",
                "szm": "YC"
            },
            {
                "label": "雷州|Leizhou|LZ|0759",
                "name": "雷州",
                "pinyin": "Leizhou",
                "zip": "0759",
                "szm": "LZ"
            },
            {
                "label": "乐陵|Leling|YL|0534",
                "name": "乐陵",
                "pinyin": "Leling",
                "zip": "0534",
                "szm": "YL"
            },
            {
                "label": "乐平|Leping|YP|0798",
                "name": "乐平",
                "pinyin": "Leping",
                "zip": "0798",
                "szm": "YP"
            },
            {
                "label": "乐清|Leqing|YQ|0577",
                "name": "乐清",
                "pinyin": "Leqing",
                "zip": "0577",
                "szm": "YQ"
            },
            {
                "label": "乐亭|Leting|YT|0315",
                "name": "乐亭",
                "pinyin": "Leting",
                "zip": "0315",
                "szm": "YT"
            },
            {
                "label": "连城|Liancheng|LC|0597",
                "name": "连城",
                "pinyin": "Liancheng",
                "zip": "0597",
                "szm": "LC"
            },
            {
                "label": "梁平|Liangping|LP|023",
                "name": "梁平",
                "pinyin": "Liangping",
                "zip": "023",
                "szm": "LP"
            },
            {
                "label": "梁山|Liangshan|LS|0537",
                "name": "梁山",
                "pinyin": "Liangshan",
                "zip": "0537",
                "szm": "LS"
            },
            {
                "label": "莲花|Lianhua|LH|0799",
                "name": "莲花",
                "pinyin": "Lianhua",
                "zip": "0799",
                "szm": "LH"
            },
            {
                "label": "连江|Lianjiang|LJ|0591",
                "name": "连江",
                "pinyin": "Lianjiang",
                "zip": "0591",
                "szm": "LJ"
            },
            {
                "label": "廉江|Lianjiang|LJ|0759",
                "name": "廉江",
                "pinyin": "Lianjiang",
                "zip": "0759",
                "szm": "LJ"
            },
            {
                "label": "连南|Liannan|LN|0763",
                "name": "连南",
                "pinyin": "Liannan",
                "zip": "0763",
                "szm": "LN"
            },
            {
                "label": "连平|Lianping|LP|0762",
                "name": "连平",
                "pinyin": "Lianping",
                "zip": "0762",
                "szm": "LP"
            },
            {
                "label": "连山|Lianshan|LS|0763",
                "name": "连山",
                "pinyin": "Lianshan",
                "zip": "0763",
                "szm": "LS"
            },
            {
                "label": "涟水|Lianshui|LS|0517",
                "name": "涟水",
                "pinyin": "Lianshui",
                "zip": "0517",
                "szm": "LS"
            },
            {
                "label": "连州|Lianzhou|LZ|0763",
                "name": "连州",
                "pinyin": "Lianzhou",
                "zip": "0763",
                "szm": "LZ"
            },
            {
                "label": "辽中|Liaozhong|LZ|024",
                "name": "辽中",
                "pinyin": "Liaozhong",
                "zip": "024",
                "szm": "LZ"
            },
            {
                "label": "黎川|Lichuan|LC|0794",
                "name": "黎川",
                "pinyin": "Lichuan",
                "zip": "0794",
                "szm": "LC"
            },
            {
                "label": "利津|Lijin|LJ|0546",
                "name": "利津",
                "pinyin": "Lijin",
                "zip": "0546",
                "szm": "LJ"
            },
            {
                "label": "临安|Linan|LA|0571",
                "name": "临安",
                "pinyin": "Linan",
                "zip": "0571",
                "szm": "LA"
            },
            {
                "label": "灵璧|Lingbi|LB|0557",
                "name": "灵璧",
                "pinyin": "Lingbi",
                "zip": "0557",
                "szm": "LB"
            },
            {
                "label": "灵寿|Lingshou|LS|0311",
                "name": "灵寿",
                "pinyin": "Lingshou",
                "zip": "0311",
                "szm": "LS"
            },
            {
                "label": "陵县|Lingxian|LX|0534",
                "name": "陵县",
                "pinyin": "Lingxian",
                "zip": "0534",
                "szm": "LX"
            },
            {
                "label": "临海|Linhai|LH|0576",
                "name": "临海",
                "pinyin": "Linhai",
                "zip": "0576",
                "szm": "LH"
            },
            {
                "label": "临清|Linqing|LQ|0635",
                "name": "临清",
                "pinyin": "Linqing",
                "zip": "0635",
                "szm": "LQ"
            },
            {
                "label": "临泉|Linquan|LQ|0558",
                "name": "临泉",
                "pinyin": "Linquan",
                "zip": "0558",
                "szm": "LQ"
            },
            {
                "label": "临朐|Linqu|LQ|0536",
                "name": "临朐",
                "pinyin": "Linqu",
                "zip": "0536",
                "szm": "LQ"
            },
            {
                "label": "临沭|Linshu|LS|0539",
                "name": "临沭",
                "pinyin": "Linshu",
                "zip": "0539",
                "szm": "LS"
            },
            {
                "label": "临邑|Linyi|LY|0534",
                "name": "临邑",
                "pinyin": "Linyi",
                "zip": "0534",
                "szm": "LY"
            },
            {
                "label": "溧水|Lishui|LS|025",
                "name": "溧水",
                "pinyin": "Lishui",
                "zip": "025",
                "szm": "LS"
            },
            {
                "label": "柳城|Liucheng|LC|0772",
                "name": "柳城",
                "pinyin": "Liucheng",
                "zip": "0772",
                "szm": "LC"
            },
            {
                "label": "柳江|Liujiang|LJ|0772",
                "name": "柳江",
                "pinyin": "Liujiang",
                "zip": "0772",
                "szm": "LJ"
            },
            {
                "label": "浏阳|Liuyang|LY|0731",
                "name": "浏阳",
                "pinyin": "Liuyang",
                "zip": "0731",
                "szm": "LY"
            },
            {
                "label": "利辛|Lixin|LX|0558",
                "name": "利辛",
                "pinyin": "Lixin",
                "zip": "0558",
                "szm": "LX"
            },
            {
                "label": "溧阳|Liyang|LY|0519",
                "name": "溧阳",
                "pinyin": "Liyang",
                "zip": "0519",
                "szm": "LY"
            },
            {
                "label": "隆安|Longan|LA|0771",
                "name": "隆安",
                "pinyin": "Longan",
                "zip": "0771",
                "szm": "LA"
            },
            {
                "label": "龙川|Longchuan|LC|0762",
                "name": "龙川",
                "pinyin": "Longchuan",
                "zip": "0762",
                "szm": "LC"
            },
            {
                "label": "龙海|Longhai|LH|0596",
                "name": "龙海",
                "pinyin": "Longhai",
                "zip": "0596",
                "szm": "LH"
            },
            {
                "label": "龙口|Longkou|LK|0535",
                "name": "龙口",
                "pinyin": "Longkou",
                "zip": "0535",
                "szm": "LK"
            },
            {
                "label": "龙门|Longmen|LM|0752",
                "name": "龙门",
                "pinyin": "Longmen",
                "zip": "0752",
                "szm": "LM"
            },
            {
                "label": "龙南|Longnan|LN|0797",
                "name": "龙南",
                "pinyin": "Longnan",
                "zip": "0797",
                "szm": "LN"
            },
            {
                "label": "龙泉|Longquan|LQ|0578",
                "name": "龙泉",
                "pinyin": "Longquan",
                "zip": "0578",
                "szm": "LQ"
            },
            {
                "label": "龙游|Longyou|LY|0570",
                "name": "龙游",
                "pinyin": "Longyou",
                "zip": "0570",
                "szm": "LY"
            },
            {
                "label": "栾城|Luancheng|LC|0311",
                "name": "栾城",
                "pinyin": "Luancheng",
                "zip": "0311",
                "szm": "LC"
            },
            {
                "label": "栾川|Luanchuan|LC|0379",
                "name": "栾川",
                "pinyin": "Luanchuan",
                "zip": "0379",
                "szm": "LC"
            },
            {
                "label": "滦南|Luannan|LN|0315",
                "name": "滦南",
                "pinyin": "Luannan",
                "zip": "0315",
                "szm": "LN"
            },
            {
                "label": "滦县|Luanxian|LX|0315",
                "name": "滦县",
                "pinyin": "Luanxian",
                "zip": "0315",
                "szm": "LX"
            },
            {
                "label": "陆丰|Lufeng|LF|0660",
                "name": "陆丰",
                "pinyin": "Lufeng",
                "zip": "0660",
                "szm": "LF"
            },
            {
                "label": "陆河|Luhe|LH|0660",
                "name": "陆河",
                "pinyin": "Luhe",
                "zip": "0660",
                "szm": "LH"
            },
            {
                "label": "庐江|Lujiang|LJ|0565",
                "name": "庐江",
                "pinyin": "Lujiang",
                "zip": "0565",
                "szm": "LJ"
            },
            {
                "label": "罗定|Luoding|LD|0766",
                "name": "罗定",
                "pinyin": "Luoding",
                "zip": "0766",
                "szm": "LD"
            },
            {
                "label": "洛宁|Luoning|LN|0379",
                "name": "洛宁",
                "pinyin": "Luoning",
                "zip": "0379",
                "szm": "LN"
            },
            {
                "label": "罗源|Luoyuan|LY|0591",
                "name": "罗源",
                "pinyin": "Luoyuan",
                "zip": "0591",
                "szm": "LY"
            },
            {
                "label": "鹿泉|Luquan|LQ|0311",
                "name": "鹿泉",
                "pinyin": "Luquan",
                "zip": "0311",
                "szm": "LQ"
            },
            {
                "label": "禄劝|Luquan|LQ|0871",
                "name": "禄劝",
                "pinyin": "Luquan",
                "zip": "0871",
                "szm": "LQ"
            },
            {
                "label": "芦溪|Luxi|LX|0799",
                "name": "芦溪",
                "pinyin": "Luxi",
                "zip": "0799",
                "szm": "LX"
            },
            {
                "label": "鹿寨|Luzhai|LZ|0772",
                "name": "鹿寨",
                "pinyin": "Luzhai",
                "zip": "0772",
                "szm": "LZ"
            },
            {
                "label": "马山|Mashan|MS|0771",
                "name": "马山",
                "pinyin": "Mashan",
                "zip": "0771",
                "szm": "MS"
            },
            {
                "label": "梅县|Meixian|MX|0753",
                "name": "梅县",
                "pinyin": "Meixian",
                "zip": "0753",
                "szm": "MX"
            },
            {
                "label": "蒙城|Mengcheng|MC|0558",
                "name": "蒙城",
                "pinyin": "Mengcheng",
                "zip": "0558",
                "szm": "MC"
            },
            {
                "label": "孟津|Mengjin|MJ|0379",
                "name": "孟津",
                "pinyin": "Mengjin",
                "zip": "0379",
                "szm": "MJ"
            },
            {
                "label": "蒙阴|Mengyin|MY|0539",
                "name": "蒙阴",
                "pinyin": "Mengyin",
                "zip": "0539",
                "szm": "MY"
            },
            {
                "label": "孟州|Mengzhou|MZ|0391",
                "name": "孟州",
                "pinyin": "Mengzhou",
                "zip": "0391",
                "szm": "MZ"
            },
            {
                "label": "明光|Mingguang|MG|0550",
                "name": "明光",
                "pinyin": "Mingguang",
                "zip": "0550",
                "szm": "MG"
            },
            {
                "label": "明溪|Mingxi|MX|0598",
                "name": "明溪",
                "pinyin": "Mingxi",
                "zip": "0598",
                "szm": "MX"
            },
            {
                "label": "闽侯|Minhou|MH|0591",
                "name": "闽侯",
                "pinyin": "Minhou",
                "zip": "0591",
                "szm": "MH"
            },
            {
                "label": "闽清|Minqing|MQ|0591",
                "name": "闽清",
                "pinyin": "Minqing",
                "zip": "0591",
                "szm": "MQ"
            },
            {
                "label": "木兰|Mulan|ML|0451",
                "name": "木兰",
                "pinyin": "Mulan",
                "zip": "0451",
                "szm": "ML"
            },
            {
                "label": "南安|Nanan|NA|0595",
                "name": "南安",
                "pinyin": "Nanan",
                "zip": "0595",
                "szm": "NA"
            },
            {
                "label": "南澳|Nanao|NA|0754",
                "name": "南澳",
                "pinyin": "Nanao",
                "zip": "0754",
                "szm": "NA"
            },
            {
                "label": "南城|Nancheng|NC|0794",
                "name": "南城",
                "pinyin": "Nancheng",
                "zip": "0794",
                "szm": "NC"
            },
            {
                "label": "南川|Nanchuan|NC|023",
                "name": "南川",
                "pinyin": "Nanchuan",
                "zip": "023",
                "szm": "NC"
            },
            {
                "label": "南丰|Nanfeng|NF|0794",
                "name": "南丰",
                "pinyin": "Nanfeng",
                "zip": "0794",
                "szm": "NF"
            },
            {
                "label": "南靖|Nanjing|NJ|0596",
                "name": "南靖",
                "pinyin": "Nanjing",
                "zip": "0596",
                "szm": "NJ"
            },
            {
                "label": "南康|Nankang|NK|0797",
                "name": "南康",
                "pinyin": "Nankang",
                "zip": "0797",
                "szm": "NK"
            },
            {
                "label": "南陵|Nanling|NL|0553",
                "name": "南陵",
                "pinyin": "Nanling",
                "zip": "0553",
                "szm": "NL"
            },
            {
                "label": "南雄|Nanxiong|NX|0751",
                "name": "南雄",
                "pinyin": "Nanxiong",
                "zip": "0751",
                "szm": "NX"
            },
            {
                "label": "宁都|Ningdu|ND|0797",
                "name": "宁都",
                "pinyin": "Ningdu",
                "zip": "0797",
                "szm": "ND"
            },
            {
                "label": "宁国|Ningguo|NG|0563",
                "name": "宁国",
                "pinyin": "Ningguo",
                "zip": "0563",
                "szm": "NG"
            },
            {
                "label": "宁海|Ninghai|NH|0574",
                "name": "宁海",
                "pinyin": "Ninghai",
                "zip": "0574",
                "szm": "NH"
            },
            {
                "label": "宁化|Ninghua|NH|0598",
                "name": "宁化",
                "pinyin": "Ninghua",
                "zip": "0598",
                "szm": "NH"
            },
            {
                "label": "宁津|Ningjin|NJ|0534",
                "name": "宁津",
                "pinyin": "Ningjin",
                "zip": "0534",
                "szm": "NJ"
            },
            {
                "label": "宁乡|Ningxiang|NX|0731",
                "name": "宁乡",
                "pinyin": "Ningxiang",
                "zip": "0731",
                "szm": "NX"
            },
            {
                "label": "宁阳|Ningyang|NY|0538",
                "name": "宁阳",
                "pinyin": "Ningyang",
                "zip": "0538",
                "szm": "NY"
            },
            {
                "label": "农安|Nongan|NA|0431",
                "name": "农安",
                "pinyin": "Nongan",
                "zip": "0431",
                "szm": "NA"
            },
            {
                "label": "磐安|Panan|PA|0579",
                "name": "磐安",
                "pinyin": "Panan",
                "zip": "0579",
                "szm": "PA"
            },
            {
                "label": "磐石|Panshi|PS|0423",
                "name": "磐石",
                "pinyin": "Panshi",
                "zip": "0423",
                "szm": "PS"
            },
            {
                "label": "沛县|Peixian|PX|0516",
                "name": "沛县",
                "pinyin": "Peixian",
                "zip": "0516",
                "szm": "PX"
            },
            {
                "label": "蓬莱|Penglai|PL|0535",
                "name": "蓬莱",
                "pinyin": "Penglai",
                "zip": "0535",
                "szm": "PL"
            },
            {
                "label": "彭水|Pengshui|PS|023",
                "name": "彭水",
                "pinyin": "Pengshui",
                "zip": "023",
                "szm": "PS"
            },
            {
                "label": "彭泽|Pengze|PZ|0792",
                "name": "彭泽",
                "pinyin": "Pengze",
                "zip": "0792",
                "szm": "PZ"
            },
            {
                "label": "彭州|Pengzhou|PZ|028",
                "name": "彭州",
                "pinyin": "Pengzhou",
                "zip": "028",
                "szm": "PZ"
            },
            {
                "label": "平度|Pingdu|PD|0532",
                "name": "平度",
                "pinyin": "Pingdu",
                "zip": "0532",
                "szm": "PD"
            },
            {
                "label": "平和|Pinghe|PH|0596",
                "name": "平和",
                "pinyin": "Pinghe",
                "zip": "0596",
                "szm": "PH"
            },
            {
                "label": "平湖|Pinghu|PH|0573",
                "name": "平湖",
                "pinyin": "Pinghu",
                "zip": "0573",
                "szm": "PH"
            },
            {
                "label": "屏南|Pingnan|PN|0593",
                "name": "屏南",
                "pinyin": "Pingnan",
                "zip": "0593",
                "szm": "PN"
            },
            {
                "label": "平山|Pingshan|PS|0311",
                "name": "平山",
                "pinyin": "Pingshan",
                "zip": "0311",
                "szm": "PS"
            },
            {
                "label": "平潭|Pingtan|PT|0591",
                "name": "平潭",
                "pinyin": "Pingtan",
                "zip": "0591",
                "szm": "PT"
            },
            {
                "label": "平阳|Pingyang|PY|0577",
                "name": "平阳",
                "pinyin": "Pingyang",
                "zip": "0577",
                "szm": "PY"
            },
            {
                "label": "平阴|Pingyin|PY|0531",
                "name": "平阴",
                "pinyin": "Pingyin",
                "zip": "0531",
                "szm": "PY"
            },
            {
                "label": "平邑|Pingyi|PY|0539",
                "name": "平邑",
                "pinyin": "Pingyi",
                "zip": "0539",
                "szm": "PY"
            },
            {
                "label": "平原|Pingyuan|PY|0534",
                "name": "平原",
                "pinyin": "Pingyuan",
                "zip": "0534",
                "szm": "PY"
            },
            {
                "label": "平远|Pingyuan|PY|0753",
                "name": "平远",
                "pinyin": "Pingyuan",
                "zip": "0753",
                "szm": "PY"
            },
            {
                "label": "郫县|Pixian|PX|028",
                "name": "郫县",
                "pinyin": "Pixian",
                "zip": "028",
                "szm": "PX"
            },
            {
                "label": "邳州|Pizhou|PZ|0516",
                "name": "邳州",
                "pinyin": "Pizhou",
                "zip": "0516",
                "szm": "PZ"
            },
            {
                "label": "鄱阳|Poyang|PY|0793",
                "name": "鄱阳",
                "pinyin": "Poyang",
                "zip": "0793",
                "szm": "PY"
            },
            {
                "label": "浦城|Pucheng|PC|0599",
                "name": "浦城",
                "pinyin": "Pucheng",
                "zip": "0599",
                "szm": "PC"
            },
            {
                "label": "浦江|Pujiang|PJ|0579",
                "name": "浦江",
                "pinyin": "Pujiang",
                "zip": "0579",
                "szm": "PJ"
            },
            {
                "label": "蒲江|Pujiang|PJ|028",
                "name": "蒲江",
                "pinyin": "Pujiang",
                "zip": "028",
                "szm": "PJ"
            },
            {
                "label": "普兰店|Pulandian|PLD|0411",
                "name": "普兰店",
                "pinyin": "Pulandian",
                "zip": "0411",
                "szm": "PLD"
            },
            {
                "label": "普宁|Puning|PN|0663",
                "name": "普宁",
                "pinyin": "Puning",
                "zip": "0663",
                "szm": "PN"
            },
            {
                "label": "迁安|Qianan|QA|0315",
                "name": "迁安",
                "pinyin": "Qianan",
                "zip": "0315",
                "szm": "QA"
            },
            {
                "label": "潜山|Qianshan|QS|0556",
                "name": "潜山",
                "pinyin": "Qianshan",
                "zip": "0556",
                "szm": "QS"
            },
            {
                "label": "铅山|Qianshan|QS|0793",
                "name": "铅山",
                "pinyin": "Qianshan",
                "zip": "0793",
                "szm": "QS"
            },
            {
                "label": "迁西|Qianxi|QX|0315",
                "name": "迁西",
                "pinyin": "Qianxi",
                "zip": "0315",
                "szm": "QX"
            },
            {
                "label": "启东|Qidong|QD|0513",
                "name": "启东",
                "pinyin": "Qidong",
                "zip": "0513",
                "szm": "QD"
            },
            {
                "label": "齐河|Qihe|QH|0534",
                "name": "齐河",
                "pinyin": "Qihe",
                "zip": "0534",
                "szm": "QH"
            },
            {
                "label": "綦江|Qijiang|QJ|023",
                "name": "綦江",
                "pinyin": "Qijiang",
                "zip": "023",
                "szm": "QJ"
            },
            {
                "label": "祁门|Qimen|QM|0559",
                "name": "祁门",
                "pinyin": "Qimen",
                "zip": "0559",
                "szm": "QM"
            },
            {
                "label": "清流|Qingliu|QL|0598",
                "name": "清流",
                "pinyin": "Qingliu",
                "zip": "0598",
                "szm": "QL"
            },
            {
                "label": "青田|Qingtian|QT|0578",
                "name": "青田",
                "pinyin": "Qingtian",
                "zip": "0578",
                "szm": "QT"
            },
            {
                "label": "清新|Qingxin|QX|0763",
                "name": "清新",
                "pinyin": "Qingxin",
                "zip": "0763",
                "szm": "QX"
            },
            {
                "label": "青阳|Qingyang|QY|0566",
                "name": "青阳",
                "pinyin": "Qingyang",
                "zip": "0566",
                "szm": "QY"
            },
            {
                "label": "庆元|Qingyuan|QY|0578",
                "name": "庆元",
                "pinyin": "Qingyuan",
                "zip": "0578",
                "szm": "QY"
            },
            {
                "label": "庆云|Qingyun|QY|0534",
                "name": "庆云",
                "pinyin": "Qingyun",
                "zip": "0534",
                "szm": "QY"
            },
            {
                "label": "清镇|Qingzhen|QZ|0851",
                "name": "清镇",
                "pinyin": "Qingzhen",
                "zip": "0851",
                "szm": "QZ"
            },
            {
                "label": "青州|Qingzhou|QZ|0536",
                "name": "青州",
                "pinyin": "Qingzhou",
                "zip": "0536",
                "szm": "QZ"
            },
            {
                "label": "沁阳|Qinyang|QY|0391",
                "name": "沁阳",
                "pinyin": "Qinyang",
                "zip": "0391",
                "szm": "QY"
            },
            {
                "label": "邛崃|Qionglai|QL|028",
                "name": "邛崃",
                "pinyin": "Qionglai",
                "zip": "028",
                "szm": "QL"
            },
            {
                "label": "栖霞|Qixia|XX|0535",
                "name": "栖霞",
                "pinyin": "Qixia",
                "zip": "0535",
                "szm": "XX"
            },
            {
                "label": "全椒|Quanjiao|QJ|0550",
                "name": "全椒",
                "pinyin": "Quanjiao",
                "zip": "0550",
                "szm": "QJ"
            },
            {
                "label": "全南|Quannan|QN|0797",
                "name": "全南",
                "pinyin": "Quannan",
                "zip": "0797",
                "szm": "QN"
            },
            {
                "label": "曲阜|Qufu|QF|0537",
                "name": "曲阜",
                "pinyin": "Qufu",
                "zip": "0537",
                "szm": "QF"
            },
            {
                "label": "曲江|Qujiang|QJ|0751",
                "name": "曲江",
                "pinyin": "Qujiang",
                "zip": "0751",
                "szm": "QJ"
            },
            {
                "label": "饶平|Raoping|RP|0768",
                "name": "饶平",
                "pinyin": "Raoping",
                "zip": "0768",
                "szm": "RP"
            },
            {
                "label": "仁化|Renhua|RH|0751",
                "name": "仁化",
                "pinyin": "Renhua",
                "zip": "0751",
                "szm": "RH"
            },
            {
                "label": "融安|Rongan|RA|0772",
                "name": "融安",
                "pinyin": "Rongan",
                "zip": "0772",
                "szm": "RA"
            },
            {
                "label": "荣昌|Rongchang|RC|023",
                "name": "荣昌",
                "pinyin": "Rongchang",
                "zip": "023",
                "szm": "RC"
            },
            {
                "label": "荣成|Rongcheng|RC|0631",
                "name": "荣成",
                "pinyin": "Rongcheng",
                "zip": "0631",
                "szm": "RC"
            },
            {
                "label": "融水|Rongshui|RS|0772",
                "name": "融水",
                "pinyin": "Rongshui",
                "zip": "0772",
                "szm": "RS"
            },
            {
                "label": "如东|Rudong|RD|0513",
                "name": "如东",
                "pinyin": "Rudong",
                "zip": "0513",
                "szm": "RD"
            },
            {
                "label": "如皋|Rugao|RG|0513",
                "name": "如皋",
                "pinyin": "Rugao",
                "zip": "0513",
                "szm": "RG"
            },
            {
                "label": "瑞安|Ruian|RA|0577",
                "name": "瑞安",
                "pinyin": "Ruian",
                "zip": "0577",
                "szm": "RA"
            },
            {
                "label": "瑞昌|Ruichang|RC|0792",
                "name": "瑞昌",
                "pinyin": "Ruichang",
                "zip": "0792",
                "szm": "RC"
            },
            {
                "label": "瑞金|Ruijin|RJ|0797",
                "name": "瑞金",
                "pinyin": "Ruijin",
                "zip": "0797",
                "szm": "RJ"
            },
            {
                "label": "乳山|Rushan|RS|0631",
                "name": "乳山",
                "pinyin": "Rushan",
                "zip": "0631",
                "szm": "RS"
            },
            {
                "label": "汝阳|Ruyang|RY|0379",
                "name": "汝阳",
                "pinyin": "Ruyang",
                "zip": "0379",
                "szm": "RY"
            },
            {
                "label": "乳源|Ruyuan|RY|0751",
                "name": "乳源",
                "pinyin": "Ruyuan",
                "zip": "0751",
                "szm": "RY"
            },
            {
                "label": "三江|Sanjiang|SJ|0772",
                "name": "三江",
                "pinyin": "Sanjiang",
                "zip": "0772",
                "szm": "SJ"
            },
            {
                "label": "三门|Sanmen|SM|0576",
                "name": "三门",
                "pinyin": "Sanmen",
                "zip": "0576",
                "szm": "SM"
            },
            {
                "label": "诏安|Saoan|ZA|0596",
                "name": "诏安",
                "pinyin": "Saoan",
                "zip": "0596",
                "szm": "ZA"
            },
            {
                "label": "上高|Shanggao|SG|0795",
                "name": "上高",
                "pinyin": "Shanggao",
                "zip": "0795",
                "szm": "SG"
            },
            {
                "label": "上杭|Shanghang|SH|0597",
                "name": "上杭",
                "pinyin": "Shanghang",
                "zip": "0597",
                "szm": "SH"
            },
            {
                "label": "商河|Shanghe|SH|0531",
                "name": "商河",
                "pinyin": "Shanghe",
                "zip": "0531",
                "szm": "SH"
            },
            {
                "label": "上栗|Shangli|SL|0799",
                "name": "上栗",
                "pinyin": "Shangli",
                "zip": "0799",
                "szm": "SL"
            },
            {
                "label": "上林|Shanglin|SL|0771",
                "name": "上林",
                "pinyin": "Shanglin",
                "zip": "0771",
                "szm": "SL"
            },
            {
                "label": "上饶|Shangrao|SR|0793",
                "name": "上饶",
                "pinyin": "Shangrao",
                "zip": "0793",
                "szm": "SR"
            },
            {
                "label": "上犹|Shangyou|SY|0797",
                "name": "上犹",
                "pinyin": "Shangyou",
                "zip": "0797",
                "szm": "SY"
            },
            {
                "label": "上虞|Shangyu|SY|0575",
                "name": "上虞",
                "pinyin": "Shangyu",
                "zip": "0575",
                "szm": "SY"
            },
            {
                "label": "尚志|Shangzhi|SZ|0451",
                "name": "尚志",
                "pinyin": "Shangzhi",
                "zip": "0451",
                "szm": "SZ"
            },
            {
                "label": "邵武|Shaowu|SW|0599",
                "name": "邵武",
                "pinyin": "Shaowu",
                "zip": "0599",
                "szm": "SW"
            },
            {
                "label": "绍兴|Shaoxing|SX|0575",
                "name": "绍兴",
                "pinyin": "Shaoxing",
                "zip": "0575",
                "szm": "SX"
            },
            {
                "label": "沙县|Shaxian|SX|0598",
                "name": "沙县",
                "pinyin": "Shaxian",
                "zip": "0598",
                "szm": "SX"
            },
            {
                "label": "嵊泗|Shengsi|SS|0580",
                "name": "嵊泗",
                "pinyin": "Shengsi",
                "zip": "0580",
                "szm": "SS"
            },
            {
                "label": "嵊州|Shengzhou|SZ|0575",
                "name": "嵊州",
                "pinyin": "Shengzhou",
                "zip": "0575",
                "szm": "SZ"
            },
            {
                "label": "莘县|Shenxian|SX|0635",
                "name": "莘县",
                "pinyin": "Shenxian",
                "zip": "0635",
                "szm": "SX"
            },
            {
                "label": "深泽|Shenze|SZ|0311",
                "name": "深泽",
                "pinyin": "Shenze",
                "zip": "0311",
                "szm": "SZ"
            },
            {
                "label": "歙县|Shexian|XX|0559",
                "name": "歙县",
                "pinyin": "Shexian",
                "zip": "0559",
                "szm": "XX"
            },
            {
                "label": "射阳|Sheyang|SY|0515",
                "name": "射阳",
                "pinyin": "Sheyang",
                "zip": "0515",
                "szm": "SY"
            },
            {
                "label": "石城|Shicheng|SC|0797",
                "name": "石城",
                "pinyin": "Shicheng",
                "zip": "0797",
                "szm": "SC"
            },
            {
                "label": "石林|Shilin|SL|0871",
                "name": "石林",
                "pinyin": "Shilin",
                "zip": "0871",
                "szm": "SL"
            },
            {
                "label": "石狮|Shishi|SS|0595",
                "name": "石狮",
                "pinyin": "Shishi",
                "zip": "0595",
                "szm": "SS"
            },
            {
                "label": "石台|Shitai|ST|0566",
                "name": "石台",
                "pinyin": "Shitai",
                "zip": "0566",
                "szm": "ST"
            },
            {
                "label": "始兴|Shixing|SX|0751",
                "name": "始兴",
                "pinyin": "Shixing",
                "zip": "0751",
                "szm": "SX"
            },
            {
                "label": "石柱|Shizhu|SZ|023",
                "name": "石柱",
                "pinyin": "Shizhu",
                "zip": "023",
                "szm": "SZ"
            },
            {
                "label": "寿光|Shouguang|SG|0536",
                "name": "寿光",
                "pinyin": "Shouguang",
                "zip": "0536",
                "szm": "SG"
            },
            {
                "label": "寿宁|Shouning|SN|0593",
                "name": "寿宁",
                "pinyin": "Shouning",
                "zip": "0593",
                "szm": "SN"
            },
            {
                "label": "寿县|Shouxian|SX|0564",
                "name": "寿县",
                "pinyin": "Shouxian",
                "zip": "0564",
                "szm": "SX"
            },
            {
                "label": "双城|Shuangcheng|SC|0451",
                "name": "双城",
                "pinyin": "Shuangcheng",
                "zip": "0451",
                "szm": "SC"
            },
            {
                "label": "双流|Shuangliu|SL|028",
                "name": "双流",
                "pinyin": "Shuangliu",
                "zip": "028",
                "szm": "SL"
            },
            {
                "label": "舒城|Shucheng|SC|0564",
                "name": "舒城",
                "pinyin": "Shucheng",
                "zip": "0564",
                "szm": "SC"
            },
            {
                "label": "舒兰|Shulan|SL|0423",
                "name": "舒兰",
                "pinyin": "Shulan",
                "zip": "0423",
                "szm": "SL"
            },
            {
                "label": "顺昌|Shunchang|SC|0599",
                "name": "顺昌",
                "pinyin": "Shunchang",
                "zip": "0599",
                "szm": "SC"
            },
            {
                "label": "沭阳|Shuyang|SY|0527",
                "name": "沭阳",
                "pinyin": "Shuyang",
                "zip": "0527",
                "szm": "SY"
            },
            {
                "label": "泗洪|Sihong|SH|0527",
                "name": "泗洪",
                "pinyin": "Sihong",
                "zip": "0527",
                "szm": "SH"
            },
            {
                "label": "四会|Sihui|SK|0758",
                "name": "四会",
                "pinyin": "Sihui",
                "zip": "0758",
                "szm": "SK"
            },
            {
                "label": "泗水|Sishui|SS|0537",
                "name": "泗水",
                "pinyin": "Sishui",
                "zip": "0537",
                "szm": "SS"
            },
            {
                "label": "泗县|Sixian|SX|0557",
                "name": "泗县",
                "pinyin": "Sixian",
                "zip": "0557",
                "szm": "SX"
            },
            {
                "label": "泗阳|Siyang|SY|0527",
                "name": "泗阳",
                "pinyin": "Siyang",
                "zip": "0527",
                "szm": "SY"
            },
            {
                "label": "嵩明|Songming|SM|0871",
                "name": "嵩明",
                "pinyin": "Songming",
                "zip": "0871",
                "szm": "SM"
            },
            {
                "label": "松溪|Songxi|SX|0599",
                "name": "松溪",
                "pinyin": "Songxi",
                "zip": "0599",
                "szm": "SX"
            },
            {
                "label": "嵩县|Songxian|SX|0379",
                "name": "嵩县",
                "pinyin": "Songxian",
                "zip": "0379",
                "szm": "SX"
            },
            {
                "label": "松阳|Songyang|SY|0578",
                "name": "松阳",
                "pinyin": "Songyang",
                "zip": "0578",
                "szm": "SY"
            },
            {
                "label": "遂昌|Suichang|SC|0578",
                "name": "遂昌",
                "pinyin": "Suichang",
                "zip": "0578",
                "szm": "SC"
            },
            {
                "label": "遂川|Suichuan|SC|0796",
                "name": "遂川",
                "pinyin": "Suichuan",
                "zip": "0796",
                "szm": "SC"
            },
            {
                "label": "睢宁|Suining|SN|0516",
                "name": "睢宁",
                "pinyin": "Suining",
                "zip": "0516",
                "szm": "SN"
            },
            {
                "label": "濉溪|Suixi|SX|0561",
                "name": "濉溪",
                "pinyin": "Suixi",
                "zip": "0561",
                "szm": "SX"
            },
            {
                "label": "遂溪|Suixi|SX|0759",
                "name": "遂溪",
                "pinyin": "Suixi",
                "zip": "0759",
                "szm": "SX"
            },
            {
                "label": "宿松|Susong|SS|0556",
                "name": "宿松",
                "pinyin": "Susong",
                "zip": "0556",
                "szm": "SS"
            },
            {
                "label": "宿豫|Suyu|SY|0527",
                "name": "宿豫",
                "pinyin": "Suyu",
                "zip": "0527",
                "szm": "SY"
            },
            {
                "label": "太仓|Taicang|TC|0512",
                "name": "太仓",
                "pinyin": "Taicang",
                "zip": "0512",
                "szm": "TC"
            },
            {
                "label": "太和|Taihe|TH|0558",
                "name": "太和",
                "pinyin": "Taihe",
                "zip": "0558",
                "szm": "TH"
            },
            {
                "label": "泰和|Taihe|TH|0796",
                "name": "泰和",
                "pinyin": "Taihe",
                "zip": "0796",
                "szm": "TH"
            },
            {
                "label": "太湖|Taihu|TH|0556",
                "name": "太湖",
                "pinyin": "Taihu",
                "zip": "0556",
                "szm": "TH"
            },
            {
                "label": "泰宁|Taining|TN|0598",
                "name": "泰宁",
                "pinyin": "Taining",
                "zip": "0598",
                "szm": "TN"
            },
            {
                "label": "台山|Taishan|TS|0750",
                "name": "台山",
                "pinyin": "Taishan",
                "zip": "0750",
                "szm": "TS"
            },
            {
                "label": "泰顺|Taishun|TS|0577",
                "name": "泰顺",
                "pinyin": "Taishun",
                "zip": "0577",
                "szm": "TS"
            },
            {
                "label": "泰兴|Taixing|TX|0523",
                "name": "泰兴",
                "pinyin": "Taixing",
                "zip": "0523",
                "szm": "TX"
            },
            {
                "label": "郯城|Tancheng|TC|0539",
                "name": "郯城",
                "pinyin": "Tancheng",
                "zip": "0539",
                "szm": "TC"
            },
            {
                "label": "唐海|Tanghai|TH|0315",
                "name": "唐海",
                "pinyin": "Tanghai",
                "zip": "0315",
                "szm": "TH"
            },
            {
                "label": "滕州|Tengzhou|TZ|0623",
                "name": "滕州",
                "pinyin": "Tengzhou",
                "zip": "0623",
                "szm": "TZ"
            },
            {
                "label": "天长|Tianchang|TC|0550",
                "name": "天长",
                "pinyin": "Tianchang",
                "zip": "0550",
                "szm": "TC"
            },
            {
                "label": "天台|Tiantai|TT|0576",
                "name": "天台",
                "pinyin": "Tiantai",
                "zip": "0576",
                "szm": "TT"
            },
            {
                "label": "桐城|Tongcheng|TC|0556",
                "name": "桐城",
                "pinyin": "Tongcheng",
                "zip": "0556",
                "szm": "TC"
            },
            {
                "label": "铜鼓|Tonggu|TG|0795",
                "name": "铜鼓",
                "pinyin": "Tonggu",
                "zip": "0795",
                "szm": "TG"
            },
            {
                "label": "通河|Tonghe|TH|0451",
                "name": "通河",
                "pinyin": "Tonghe",
                "zip": "0451",
                "szm": "TH"
            },
            {
                "label": "铜梁|Tongliang|TL|023",
                "name": "铜梁",
                "pinyin": "Tongliang",
                "zip": "023",
                "szm": "TL"
            },
            {
                "label": "铜陵|Tongling|TL|0562",
                "name": "铜陵",
                "pinyin": "Tongling",
                "zip": "0562",
                "szm": "TL"
            },
            {
                "label": "桐庐|Tonglu|TL|0571",
                "name": "桐庐",
                "pinyin": "Tonglu",
                "zip": "0571",
                "szm": "TL"
            },
            {
                "label": "潼南|Tongnan|TN|023",
                "name": "潼南",
                "pinyin": "Tongnan",
                "zip": "023",
                "szm": "TN"
            },
            {
                "label": "铜山|Tongshan|TS|0516",
                "name": "铜山",
                "pinyin": "Tongshan",
                "zip": "0516",
                "szm": "TS"
            },
            {
                "label": "桐乡|Tongxiang|TX|0573",
                "name": "桐乡",
                "pinyin": "Tongxiang",
                "zip": "0573",
                "szm": "TX"
            },
            {
                "label": "通州|Tongzhou|TZ|0513",
                "name": "通州",
                "pinyin": "Tongzhou",
                "zip": "0513",
                "szm": "TZ"
            },
            {
                "label": "瓦房店|Wafangdian|WFD|0411",
                "name": "瓦房店",
                "pinyin": "Wafangdian",
                "zip": "0411",
                "szm": "WFD"
            },
            {
                "label": "万安|Wanan|WA|0796",
                "name": "万安",
                "pinyin": "Wanan",
                "zip": "0796",
                "szm": "WA"
            },
            {
                "label": "望城|Wangcheng|WC|0731",
                "name": "望城",
                "pinyin": "Wangcheng",
                "zip": "0731",
                "szm": "WC"
            },
            {
                "label": "望江|Wangjiang|WJ|0556",
                "name": "望江",
                "pinyin": "Wangjiang",
                "zip": "0556",
                "szm": "WJ"
            },
            {
                "label": "万年|Wannian|WN|0793",
                "name": "万年",
                "pinyin": "Wannian",
                "zip": "0793",
                "szm": "WN"
            },
            {
                "label": "万载|Wanzai|WZ|0795",
                "name": "万载",
                "pinyin": "Wanzai",
                "zip": "0795",
                "szm": "WZ"
            },
            {
                "label": "微山|Weishan|WS|0537",
                "name": "微山",
                "pinyin": "Weishan",
                "zip": "0537",
                "szm": "WS"
            },
            {
                "label": "文成|Wencheng|WC|0577",
                "name": "文成",
                "pinyin": "Wencheng",
                "zip": "0577",
                "szm": "WC"
            },
            {
                "label": "文登|Wendeng|WD|0631",
                "name": "文登",
                "pinyin": "Wendeng",
                "zip": "0631",
                "szm": "WD"
            },
            {
                "label": "翁源|Wengyuan|WY|0751",
                "name": "翁源",
                "pinyin": "Wengyuan",
                "zip": "0751",
                "szm": "WY"
            },
            {
                "label": "温岭|Wenling|WL|0576",
                "name": "温岭",
                "pinyin": "Wenling",
                "zip": "0576",
                "szm": "WL"
            },
            {
                "label": "汶上|Wenshang|WS|0537",
                "name": "汶上",
                "pinyin": "Wenshang",
                "zip": "0537",
                "szm": "WS"
            },
            {
                "label": "温县|Wenxian|WX|0391",
                "name": "温县",
                "pinyin": "Wenxian",
                "zip": "0391",
                "szm": "WX"
            },
            {
                "label": "涡阳|Woyang|WY|0558",
                "name": "涡阳",
                "pinyin": "Woyang",
                "zip": "0558",
                "szm": "WY"
            },
            {
                "label": "五常|Wuchang|WC|0451",
                "name": "五常",
                "pinyin": "Wuchang",
                "zip": "0451",
                "szm": "WC"
            },
            {
                "label": "武城|Wucheng|WC|0534",
                "name": "武城",
                "pinyin": "Wucheng",
                "zip": "0534",
                "szm": "WC"
            },
            {
                "label": "吴川|Wuchuan|WC|0759",
                "name": "吴川",
                "pinyin": "Wuchuan",
                "zip": "0759",
                "szm": "WC"
            },
            {
                "label": "无棣|Wudi|WD|0543",
                "name": "无棣",
                "pinyin": "Wudi",
                "zip": "0543",
                "szm": "WD"
            },
            {
                "label": "五河|Wuhe|WH|0552",
                "name": "五河",
                "pinyin": "Wuhe",
                "zip": "0552",
                "szm": "WH"
            },
            {
                "label": "芜湖|Wuhu|WH|0553",
                "name": "芜湖",
                "pinyin": "Wuhu",
                "zip": "0553",
                "szm": "WH"
            },
            {
                "label": "五华|Wuhua|WH|0753",
                "name": "五华",
                "pinyin": "Wuhua",
                "zip": "0753",
                "szm": "WH"
            },
            {
                "label": "无极|Wuji|WJ|0311",
                "name": "无极",
                "pinyin": "Wuji",
                "zip": "0311",
                "szm": "WJ"
            },
            {
                "label": "吴江|Wujiang|WJ|0512",
                "name": "吴江",
                "pinyin": "Wujiang",
                "zip": "0512",
                "szm": "WJ"
            },
            {
                "label": "五莲|Wulian|WL|0633",
                "name": "五莲",
                "pinyin": "Wulian",
                "zip": "0633",
                "szm": "WL"
            },
            {
                "label": "武隆|Wulong|WL|023",
                "name": "武隆",
                "pinyin": "Wulong",
                "zip": "023",
                "szm": "WL"
            },
            {
                "label": "武鸣|Wuming|WM|0771",
                "name": "武鸣",
                "pinyin": "Wuming",
                "zip": "0771",
                "szm": "WM"
            },
            {
                "label": "武宁|Wuning|WN|0792",
                "name": "武宁",
                "pinyin": "Wuning",
                "zip": "0792",
                "szm": "WN"
            },
            {
                "label": "武平|Wuping|WP|0597",
                "name": "武平",
                "pinyin": "Wuping",
                "zip": "0597",
                "szm": "WP"
            },
            {
                "label": "巫山|Wushan|WS|023",
                "name": "巫山",
                "pinyin": "Wushan",
                "zip": "023",
                "szm": "WS"
            },
            {
                "label": "无为|Wuwei|WW|0565",
                "name": "无为",
                "pinyin": "Wuwei",
                "zip": "0565",
                "szm": "WW"
            },
            {
                "label": "巫溪|Wuxi|WX|023",
                "name": "巫溪",
                "pinyin": "Wuxi",
                "zip": "023",
                "szm": "WX"
            },
            {
                "label": "武义|Wuyi|WY|0579",
                "name": "武义",
                "pinyin": "Wuyi",
                "zip": "0579",
                "szm": "WY"
            },
            {
                "label": "武夷山|Wuyishan|WYS|0599",
                "name": "武夷山",
                "pinyin": "Wuyishan",
                "zip": "0599",
                "szm": "WYS"
            },
            {
                "label": "婺源|Wuyuan|WY|0793",
                "name": "婺源",
                "pinyin": "Wuyuan",
                "zip": "0793",
                "szm": "WY"
            },
            {
                "label": "武陟|Wuzhi|WZ|0391",
                "name": "武陟",
                "pinyin": "Wuzhi",
                "zip": "0391",
                "szm": "WZ"
            },
            {
                "label": "峡江|Xiajiang|XJ|0796",
                "name": "峡江",
                "pinyin": "Xiajiang",
                "zip": "0796",
                "szm": "XJ"
            },
            {
                "label": "夏津|Xiajin|XJ|0534",
                "name": "夏津",
                "pinyin": "Xiajin",
                "zip": "0534",
                "szm": "XJ"
            },
            {
                "label": "象山|Xiangshan|XS|0574",
                "name": "象山",
                "pinyin": "Xiangshan",
                "zip": "0574",
                "szm": "XS"
            },
            {
                "label": "响水|Xiangshui|XS|0515",
                "name": "响水",
                "pinyin": "Xiangshui",
                "zip": "0515",
                "szm": "XS"
            },
            {
                "label": "仙居|Xianju|XJ|0576",
                "name": "仙居",
                "pinyin": "Xianju",
                "zip": "0576",
                "szm": "XJ"
            },
            {
                "label": "仙游|Xianyou|XY|0594",
                "name": "仙游",
                "pinyin": "Xianyou",
                "zip": "0594",
                "szm": "XY"
            },
            {
                "label": "萧县|Xiaoxian|XX|0557",
                "name": "萧县",
                "pinyin": "Xiaoxian",
                "zip": "0557",
                "szm": "XX"
            },
            {
                "label": "霞浦|Xiapu|XP|0593",
                "name": "霞浦",
                "pinyin": "Xiapu",
                "zip": "0593",
                "szm": "XP"
            },
            {
                "label": "息烽|Xifeng|XF|0851",
                "name": "息烽",
                "pinyin": "Xifeng",
                "zip": "0851",
                "szm": "XF"
            },
            {
                "label": "新安|Xinan|XA|0379",
                "name": "新安",
                "pinyin": "Xinan",
                "zip": "0379",
                "szm": "XA"
            },
            {
                "label": "新昌|Xinchang|XC|0575",
                "name": "新昌",
                "pinyin": "Xinchang",
                "zip": "0575",
                "szm": "XC"
            },
            {
                "label": "信丰|Xinfeng|XF|0797",
                "name": "信丰",
                "pinyin": "Xinfeng",
                "zip": "0797",
                "szm": "XF"
            },
            {
                "label": "新丰|Xinfeng|XF|0751",
                "name": "新丰",
                "pinyin": "Xinfeng",
                "zip": "0751",
                "szm": "XF"
            },
            {
                "label": "新干|Xingan|XG|0796",
                "name": "新干",
                "pinyin": "Xingan",
                "zip": "0796",
                "szm": "XG"
            },
            {
                "label": "兴国|Xingguo|XG|0797",
                "name": "兴国",
                "pinyin": "Xingguo",
                "zip": "0797",
                "szm": "XG"
            },
            {
                "label": "兴化|Xinghua|XH|0523",
                "name": "兴化",
                "pinyin": "Xinghua",
                "zip": "0523",
                "szm": "XH"
            },
            {
                "label": "兴宁|Xingning|XN|0753",
                "name": "兴宁",
                "pinyin": "Xingning",
                "zip": "0753",
                "szm": "XN"
            },
            {
                "label": "行唐|Xingtang|XT|0311",
                "name": "行唐",
                "pinyin": "Xingtang",
                "zip": "0311",
                "szm": "XT"
            },
            {
                "label": "荥阳|Xingyang|YY|0371",
                "name": "荥阳",
                "pinyin": "Xingyang",
                "zip": "0371",
                "szm": "YY"
            },
            {
                "label": "星子|Xingzi|XZ|0792",
                "name": "星子",
                "pinyin": "Xingzi",
                "zip": "0792",
                "szm": "XZ"
            },
            {
                "label": "辛集|Xinji|XJ|0311",
                "name": "辛集",
                "pinyin": "Xinji",
                "zip": "0311",
                "szm": "XJ"
            },
            {
                "label": "新建|Xinjian|XJ|0791",
                "name": "新建",
                "pinyin": "Xinjian",
                "zip": "0791",
                "szm": "XJ"
            },
            {
                "label": "新津|Xinjin|XJ|028",
                "name": "新津",
                "pinyin": "Xinjin",
                "zip": "028",
                "szm": "XJ"
            },
            {
                "label": "新乐|Xinle|XY|0311",
                "name": "新乐",
                "pinyin": "Xinle",
                "zip": "0311",
                "szm": "XY"
            },
            {
                "label": "新民|Xinmin|XM|024",
                "name": "新民",
                "pinyin": "Xinmin",
                "zip": "024",
                "szm": "XM"
            },
            {
                "label": "新密|Xinmi|XM|0371",
                "name": "新密",
                "pinyin": "Xinmi",
                "zip": "0371",
                "szm": "XM"
            },
            {
                "label": "新泰|Xintai|XT|0538",
                "name": "新泰",
                "pinyin": "Xintai",
                "zip": "0538",
                "szm": "XT"
            },
            {
                "label": "新兴|Xinxing|XX|0766",
                "name": "新兴",
                "pinyin": "Xinxing",
                "zip": "0766",
                "szm": "XX"
            },
            {
                "label": "新沂|Xinyi|XY|0516",
                "name": "新沂",
                "pinyin": "Xinyi",
                "zip": "0516",
                "szm": "XY"
            },
            {
                "label": "信宜|Xinyi|XY|0668",
                "name": "信宜",
                "pinyin": "Xinyi",
                "zip": "0668",
                "szm": "XY"
            },
            {
                "label": "新郑|Xinzheng|XZ|0371",
                "name": "新郑",
                "pinyin": "Xinzheng",
                "zip": "0371",
                "szm": "XZ"
            },
            {
                "label": "休宁|Xiuning|XN|0559",
                "name": "休宁",
                "pinyin": "Xiuning",
                "zip": "0559",
                "szm": "XN"
            },
            {
                "label": "秀山|Xiushan|XS|023",
                "name": "秀山",
                "pinyin": "Xiushan",
                "zip": "023",
                "szm": "XS"
            },
            {
                "label": "修水|Xiushui|XS|0792",
                "name": "修水",
                "pinyin": "Xiushui",
                "zip": "0792",
                "szm": "XS"
            },
            {
                "label": "修文|Xiuwen|XW|0851",
                "name": "修文",
                "pinyin": "Xiuwen",
                "zip": "0851",
                "szm": "XW"
            },
            {
                "label": "修武|Xiuwu|XW|0391",
                "name": "修武",
                "pinyin": "Xiuwu",
                "zip": "0391",
                "szm": "XW"
            },
            {
                "label": "寻甸|Xundian|XD|0871",
                "name": "寻甸",
                "pinyin": "Xundian",
                "zip": "0871",
                "szm": "XD"
            },
            {
                "label": "寻乌|Xunwu|XW|0797",
                "name": "寻乌",
                "pinyin": "Xunwu",
                "zip": "0797",
                "szm": "XW"
            },
            {
                "label": "徐闻|Xuwen|XW|0759",
                "name": "徐闻",
                "pinyin": "Xuwen",
                "zip": "0759",
                "szm": "XW"
            },
            {
                "label": "盱眙|Xuyi|XY|0517",
                "name": "盱眙",
                "pinyin": "Xuyi",
                "zip": "0517",
                "szm": "XY"
            },
            {
                "label": "阳春|Yangchun|YC|0662",
                "name": "阳春",
                "pinyin": "Yangchun",
                "zip": "0662",
                "szm": "YC"
            },
            {
                "label": "阳东|Yangdong|YD|0662",
                "name": "阳东",
                "pinyin": "Yangdong",
                "zip": "0662",
                "szm": "YD"
            },
            {
                "label": "阳谷|Yanggu|YY|0635",
                "name": "阳谷",
                "pinyin": "Yanggu",
                "zip": "0635",
                "szm": "YY"
            },
            {
                "label": "阳山|Yangshan|YS|0763",
                "name": "阳山",
                "pinyin": "Yangshan",
                "zip": "0763",
                "szm": "YS"
            },
            {
                "label": "阳信|Yangxin|YX|0543",
                "name": "阳信",
                "pinyin": "Yangxin",
                "zip": "0543",
                "szm": "YX"
            },
            {
                "label": "阳西|Yangxi|YX|0662",
                "name": "阳西",
                "pinyin": "Yangxi",
                "zip": "0662",
                "szm": "YX"
            },
            {
                "label": "扬中|Yangzhong|YZ|0511",
                "name": "扬中",
                "pinyin": "Yangzhong",
                "zip": "0511",
                "szm": "YZ"
            },
            {
                "label": "偃师|Yanshi|YS|0379",
                "name": "偃师",
                "pinyin": "Yanshi",
                "zip": "0379",
                "szm": "YS"
            },
            {
                "label": "延寿|Yanshou|YS|0451",
                "name": "延寿",
                "pinyin": "Yanshou",
                "zip": "0451",
                "szm": "YS"
            },
            {
                "label": "兖州|Yanzhou|YZ|0537",
                "name": "兖州",
                "pinyin": "Yanzhou",
                "zip": "0537",
                "szm": "YZ"
            },
            {
                "label": "伊川|Yichuan|YC|0379",
                "name": "伊川",
                "pinyin": "Yichuan",
                "zip": "0379",
                "szm": "YC"
            },
            {
                "label": "宜丰|Yifeng|YF|0795",
                "name": "宜丰",
                "pinyin": "Yifeng",
                "zip": "0795",
                "szm": "YF"
            },
            {
                "label": "宜黄|Yihuang|YH|0794",
                "name": "宜黄",
                "pinyin": "Yihuang",
                "zip": "0794",
                "szm": "YH"
            },
            {
                "label": "依兰|Yilan|YL|0451",
                "name": "依兰",
                "pinyin": "Yilan",
                "zip": "0451",
                "szm": "YL"
            },
            {
                "label": "宜良|Yiliang|YL|0871",
                "name": "宜良",
                "pinyin": "Yiliang",
                "zip": "0871",
                "szm": "YL"
            },
            {
                "label": "沂南|Yinan|YN|0539",
                "name": "沂南",
                "pinyin": "Yinan",
                "zip": "0539",
                "szm": "YN"
            },
            {
                "label": "英德|Yingde|YD|0763",
                "name": "英德",
                "pinyin": "Yingde",
                "zip": "0763",
                "szm": "YD"
            },
            {
                "label": "颍上|Yingshang|YS|0558",
                "name": "颍上",
                "pinyin": "Yingshang",
                "zip": "0558",
                "szm": "YS"
            },
            {
                "label": "沂水|Yishui|YS|0539",
                "name": "沂水",
                "pinyin": "Yishui",
                "zip": "0539",
                "szm": "YS"
            },
            {
                "label": "义乌|Yiwu|YW|0579",
                "name": "义乌",
                "pinyin": "Yiwu",
                "zip": "0579",
                "szm": "YW"
            },
            {
                "label": "黟县|Yixian|YX|0559",
                "name": "黟县",
                "pinyin": "Yixian",
                "zip": "0559",
                "szm": "YX"
            },
            {
                "label": "宜兴|Yixing|YX|0510",
                "name": "宜兴",
                "pinyin": "Yixing",
                "zip": "0510",
                "szm": "YX"
            },
            {
                "label": "弋阳|Yiyang|YY|0793",
                "name": "弋阳",
                "pinyin": "Yiyang",
                "zip": "0793",
                "szm": "YY"
            },
            {
                "label": "宜阳|Yiyang|YY|0379",
                "name": "宜阳",
                "pinyin": "Yiyang",
                "zip": "0379",
                "szm": "YY"
            },
            {
                "label": "沂源|Yiyuan|YY|0533",
                "name": "沂源",
                "pinyin": "Yiyuan",
                "zip": "0533",
                "szm": "YY"
            },
            {
                "label": "仪征|Yizheng|YZ|0514",
                "name": "仪征",
                "pinyin": "Yizheng",
                "zip": "0514",
                "szm": "YZ"
            },
            {
                "label": "永安|Yongan|YA|0598",
                "name": "永安",
                "pinyin": "Yongan",
                "zip": "0598",
                "szm": "YA"
            },
            {
                "label": "永川|Yongchuan|YC|023",
                "name": "永川",
                "pinyin": "Yongchuan",
                "zip": "023",
                "szm": "YC"
            },
            {
                "label": "永春|Yongchun|YC|0595",
                "name": "永春",
                "pinyin": "Yongchun",
                "zip": "0595",
                "szm": "YC"
            },
            {
                "label": "永登|Yongdeng|YD|0931",
                "name": "永登",
                "pinyin": "Yongdeng",
                "zip": "0931",
                "szm": "YD"
            },
            {
                "label": "永定|Yongding|YD|0597",
                "name": "永定",
                "pinyin": "Yongding",
                "zip": "0597",
                "szm": "YD"
            },
            {
                "label": "永丰|Yongfeng|YF|0796",
                "name": "永丰",
                "pinyin": "Yongfeng",
                "zip": "0796",
                "szm": "YF"
            },
            {
                "label": "永吉|Yongji|YJ|0423",
                "name": "永吉",
                "pinyin": "Yongji",
                "zip": "0423",
                "szm": "YJ"
            },
            {
                "label": "永嘉|Yongjia|YJ|0577",
                "name": "永嘉",
                "pinyin": "Yongjia",
                "zip": "0577",
                "szm": "YJ"
            },
            {
                "label": "永康|Yongkang|YK|0579",
                "name": "永康",
                "pinyin": "Yongkang",
                "zip": "0579",
                "szm": "YK"
            },
            {
                "label": "邕宁|Yongning|YN|0771",
                "name": "邕宁",
                "pinyin": "Yongning",
                "zip": "0771",
                "szm": "YN"
            },
            {
                "label": "永泰|Yongtai|YT|0591",
                "name": "永泰",
                "pinyin": "Yongtai",
                "zip": "0591",
                "szm": "YT"
            },
            {
                "label": "永新|Yongxin|YX|0796",
                "name": "永新",
                "pinyin": "Yongxin",
                "zip": "0796",
                "szm": "YX"
            },
            {
                "label": "永修|Yongxiu|YX|0792",
                "name": "永修",
                "pinyin": "Yongxiu",
                "zip": "0792",
                "szm": "YX"
            },
            {
                "label": "尤溪|Youxi|YX|0598",
                "name": "尤溪",
                "pinyin": "Youxi",
                "zip": "0598",
                "szm": "YX"
            },
            {
                "label": "酉阳|Youyang|YY|023",
                "name": "酉阳",
                "pinyin": "Youyang",
                "zip": "023",
                "szm": "YY"
            },
            {
                "label": "元氏|Yuanshi|YZ|0311",
                "name": "元氏",
                "pinyin": "Yuanshi",
                "zip": "0311",
                "szm": "YZ"
            },
            {
                "label": "禹城|Yucheng|YC|0534",
                "name": "禹城",
                "pinyin": "Yucheng",
                "zip": "0534",
                "szm": "YC"
            },
            {
                "label": "于都|Yudu|YD|0797",
                "name": "于都",
                "pinyin": "Yudu",
                "zip": "0797",
                "szm": "YD"
            },
            {
                "label": "岳西|Yuexi|YX|0556",
                "name": "岳西",
                "pinyin": "Yuexi",
                "zip": "0556",
                "szm": "YX"
            },
            {
                "label": "余干|Yugan|YG|0793",
                "name": "余干",
                "pinyin": "Yugan",
                "zip": "0793",
                "szm": "YG"
            },
            {
                "label": "玉环|Yuhuan|YH|0576",
                "name": "玉环",
                "pinyin": "Yuhuan",
                "zip": "0576",
                "szm": "YH"
            },
            {
                "label": "余江|Yujiang|YJ|0701",
                "name": "余江",
                "pinyin": "Yujiang",
                "zip": "0701",
                "szm": "YJ"
            },
            {
                "label": "郁南|Yunan|YN|0766",
                "name": "郁南",
                "pinyin": "Yunan",
                "zip": "0766",
                "szm": "YN"
            },
            {
                "label": "云安|Yunan|YA|0766",
                "name": "云安",
                "pinyin": "Yunan",
                "zip": "0766",
                "szm": "YA"
            },
            {
                "label": "郓城|Yuncheng|YC|0530",
                "name": "郓城",
                "pinyin": "Yuncheng",
                "zip": "0530",
                "szm": "YC"
            },
            {
                "label": "云和|Yunhe|YH|0578",
                "name": "云和",
                "pinyin": "Yunhe",
                "zip": "0578",
                "szm": "YH"
            },
            {
                "label": "云霄|Yunxiao|YX|0596",
                "name": "云霄",
                "pinyin": "Yunxiao",
                "zip": "0596",
                "szm": "YX"
            },
            {
                "label": "云阳|Yunyang|YY|023",
                "name": "云阳",
                "pinyin": "Yunyang",
                "zip": "023",
                "szm": "YY"
            },
            {
                "label": "玉山|Yushan|YS|0793",
                "name": "玉山",
                "pinyin": "Yushan",
                "zip": "0793",
                "szm": "YS"
            },
            {
                "label": "榆树|Yushu|YS|0431",
                "name": "榆树",
                "pinyin": "Yushu",
                "zip": "0431",
                "szm": "YS"
            },
            {
                "label": "鱼台|Yutai|YT|0537",
                "name": "鱼台",
                "pinyin": "Yutai",
                "zip": "0537",
                "szm": "YT"
            },
            {
                "label": "玉田|Yutian|YT|0315",
                "name": "玉田",
                "pinyin": "Yutian",
                "zip": "0315",
                "szm": "YT"
            },
            {
                "label": "余姚|Yuyao|YY|0574",
                "name": "余姚",
                "pinyin": "Yuyao",
                "zip": "0574",
                "szm": "YY"
            },
            {
                "label": "榆中|Yuzhong|YZ|0931",
                "name": "榆中",
                "pinyin": "Yuzhong",
                "zip": "0931",
                "szm": "YZ"
            },
            {
                "label": "赞皇|Zanhuang|ZH|0311",
                "name": "赞皇",
                "pinyin": "Zanhuang",
                "zip": "0311",
                "szm": "ZH"
            },
            {
                "label": "增城|Zengcheng|ZC|020",
                "name": "增城",
                "pinyin": "Zengcheng",
                "zip": "020",
                "szm": "ZC"
            },
            {
                "label": "张家港|Zhangjiagang|ZJG|0512",
                "name": "张家港",
                "pinyin": "Zhangjiagang",
                "zip": "0512",
                "szm": "ZJG"
            },
            {
                "label": "漳平|Zhangping|ZP|0597",
                "name": "漳平",
                "pinyin": "Zhangping",
                "zip": "0597",
                "szm": "ZP"
            },
            {
                "label": "漳浦|Zhangpu|ZP|0596",
                "name": "漳浦",
                "pinyin": "Zhangpu",
                "zip": "0596",
                "szm": "ZP"
            },
            {
                "label": "章丘|Zhangqiu|ZQ|0531",
                "name": "章丘",
                "pinyin": "Zhangqiu",
                "zip": "0531",
                "szm": "ZQ"
            },
            {
                "label": "樟树|Zhangshu|ZS|0795",
                "name": "樟树",
                "pinyin": "Zhangshu",
                "zip": "0795",
                "szm": "ZS"
            },
            {
                "label": "沾化|Zhanhua|ZH|0543",
                "name": "沾化",
                "pinyin": "Zhanhua",
                "zip": "0543",
                "szm": "ZH"
            },
            {
                "label": "赵县|Zhaoxian|ZX|0311",
                "name": "赵县",
                "pinyin": "Zhaoxian",
                "zip": "0311",
                "szm": "ZX"
            },
            {
                "label": "招远|Zhaoyuan|ZY|0535",
                "name": "招远",
                "pinyin": "Zhaoyuan",
                "zip": "0535",
                "szm": "ZY"
            },
            {
                "label": "正定|Zhengding|ZD|0311",
                "name": "正定",
                "pinyin": "Zhengding",
                "zip": "0311",
                "szm": "ZD"
            },
            {
                "label": "政和|Zhenghe|ZH|0599",
                "name": "政和",
                "pinyin": "Zhenghe",
                "zip": "0599",
                "szm": "ZH"
            },
            {
                "label": "柘荣|Zherong|ZR|0593",
                "name": "柘荣",
                "pinyin": "Zherong",
                "zip": "0593",
                "szm": "ZR"
            },
            {
                "label": "中牟|Zhongmou|ZM|0371",
                "name": "中牟",
                "pinyin": "Zhongmou",
                "zip": "0371",
                "szm": "ZM"
            },
            {
                "label": "忠县|Zhongxian|ZX|023",
                "name": "忠县",
                "pinyin": "Zhongxian",
                "zip": "023",
                "szm": "ZX"
            },
            {
                "label": "周宁|Zhouning|ZN|0593",
                "name": "周宁",
                "pinyin": "Zhouning",
                "zip": "0593",
                "szm": "ZN"
            },
            {
                "label": "周至|Zhouzhi|ZZ|029",
                "name": "周至",
                "pinyin": "Zhouzhi",
                "zip": "029",
                "szm": "ZZ"
            },
            {
                "label": "庄河|Zhuanghe|ZH|0411",
                "name": "庄河",
                "pinyin": "Zhuanghe",
                "zip": "0411",
                "szm": "ZH"
            },
            {
                "label": "诸城|Zhucheng|ZC|0536",
                "name": "诸城",
                "pinyin": "Zhucheng",
                "zip": "0536",
                "szm": "ZC"
            },
            {
                "label": "诸暨|Zhuji|ZJ|0575",
                "name": "诸暨",
                "pinyin": "Zhuji",
                "zip": "0575",
                "szm": "ZJ"
            },
            {
                "label": "紫金|Zijin|ZJ|0762",
                "name": "紫金",
                "pinyin": "Zijin",
                "zip": "0762",
                "szm": "ZJ"
            },
            {
                "label": "资溪|Zixi|ZX|0794",
                "name": "资溪",
                "pinyin": "Zixi",
                "zip": "0794",
                "szm": "ZX"
            },
            {
                "label": "邹城|Zoucheng|ZC|0537",
                "name": "邹城",
                "pinyin": "Zoucheng",
                "zip": "0537",
                "szm": "ZC"
            },
            {
                "label": "邹平|Zouping|ZP|0543",
                "name": "邹平",
                "pinyin": "Zouping",
                "zip": "0543",
                "szm": "ZP"
            },
            {
                "label": "遵化|Zunhua|ZH|0315",
                "name": "遵化",
                "pinyin": "Zunhua",
                "zip": "0315",
                "szm": "ZH"
            }
        ]
    };
});