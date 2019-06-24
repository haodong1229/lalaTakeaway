define(["jquery", "clockpicker"], function (a) {
    //a是jquery的$
    var b = {
        polygons: {normal: {}, special: {}},
        colors: { //预先定义填充颜色和描边颜色
            1: {strokeColor: "#4589ef", fillColor: "#71a3ef"},
            2: {strokeColor: "#1ebd4f", fillColor: "#1ecb54"},
            3: {strokeColor: "#06954b", fillColor: "#41ad73"},
            4: {strokeColor: "#9a6a38", fillColor: "#b38f66"},
            5: {strokeColor: "#6b543c", fillColor: "#917e6a"},
            6: {strokeColor: "#4589ef", fillColor: "#71a3ef"},
            7: {strokeColor: "#1ebd4f", fillColor: "#1ecb54"},
            8: {strokeColor: "#06954b", fillColor: "#41ad73"},
            9: {strokeColor: "#9a6a38", fillColor: "#b38f66"},
            10: {strokeColor: "#6b543c", fillColor: "#917e6a"}
        },
        areas: {}
    };
    return b.init = function (a) {
        var c = new AMap.Map("allmap", { //加载高德地图
            resizeEnable: !0,
            zoom: 16,
            doubleClickZoom: !1,
            center: [a.store.location_y, a.store.location_x]
        });
        c.addControl(new AMap.ToolBar),//高德添加工具栏
            window.map = c,
            window.tmodtpl = a.tmodtpl;
            b.isChange = a.isChange,
            b.store = a.store,

            //像是缩放高度?? 注释了无效果
            b.length(a.areas) ? (b.length(a.areas.normal) || (a.areas.normal = {
                M1234567891001: {
                    startHour: "00:00",
                    endHour: "00:00",
                    areas: {}
                }
            }), b.length(a.areas.special) || (a.areas.special = {})) : a.areas = {
            normal: {
                M1234567891001: {
                    startHour: "00:00",
                    endHour: "00:00",
                    areas: {}
                }
            }, special: {}
        },

            b.areas = a.areas, //取得数据库的地址
            b.areasOriginal = a.areas,
            b.tplArea(), //添加区域及配送范围方法
            b.tplEditor(), // 初始化显示高德绘图方法
            b.initDom() //调用新增配送范围方法
    },
        b.tplArea = function () {
        var c = tmodtpl("tpl-area", b);
        a(".geofence-container").html(c) // 添加区域及配送范围
    },
        b.markerStore = function () {
        if (b.store.location_y && b.store.location_x) {
            new AMap.Marker({ //如果经纬度都有,添加标记点
                position: [b.store.location_y, b.store.location_x],
                offset: new AMap.Pixel(-10, -36),
                content: '<div class="marker-start-head-route"></div>'
            }).setMap(map)
        }
    },
        b.tplEditor = function () {
        a(".clockpicker :text").clockpicker({
            autoclose: !0, afterDone: function () { //点击时钟完成时自动关闭
                a(".clockpicker :text").trigger("change"), b.tplArea(), b.tplEditor()
            }
        }),
        map.clearMap(), //删除地图上所有的覆盖物
        b.markerStore(),  //放置门店icon
            a.each(b.areas, function (c, d) {
            a.each(d, function (d, e) {
                b.polygons[c][d] = {},
                a.each(e.areas, function (a, e) {
                    var f = b.colors[e.colorType]; //获得填充颜色和描边颜色
                        g = new AMap.Polygon({ //高德绘图配置
                        path: e.path,
                        strokeColor: f.strokeColor,
                        strokeOpacity: .9,
                        strokeWeight: 3,
                        fillColor: f.fillColor,
                        fillOpacity: .8
                    });
                    b.polygons[c][d][a] = g,
                        g.setMap(map) //绘画到地图对象
                })
            })
        }),

            a(':hidden[name="areas"]').val(encodeURI(JSON.stringify(b.areas))) //把配送范围信息放入隐藏框
    },
        b.initDom = function () {
        a('[data-toggle="tooltip"]').tooltip(),
        a(document).off("click", ".area-add"),
        a(document).on("click", ".area-add", function () {
            var c = a(this).data("type"), d = a(this).data("parentid");
            if (1 == b.isActive) return !1;
            var e = b.getId("M", 0); //给新增配送范围添加编号
            if (b.length(b.areas[c][d].areas) >= 10) return void Notify.info("最多可添加10个！");
            var f = b.getColor(c, d), //f是当前添加个数
                g = b.colors[f]; // g是获得当前的颜色
                b.isActive = 1,
                b.areas[c][d].areas[e] = {
                delivery_price: 0,
                delivery_free_price: 0,
                send_price: 0,
                description: "",
                strokeColor: g.strokeColor,
                fillColor: g.fillColor,
                isActive: 1,
                isAdd: 1,
                path: [],
                colorType: f
            };
            b.polygons[c][d] || (b.polygons[c][d] = {}), b.polygons[c][d][e] = {};
            var h = new AMap.MouseTool(map);
            h.polygon();

            AMap.event.addListener(h, "draw", function (a) { //绘画完成不可以再次绘画(做限制)
                h.close();
                var f = a.obj;
                new AMap.PolyEditor(map, f).open(),
                    b.areas[c][d].areas[e].path = f.getPath() //单击右键添加到b.areas[c][d].areas[e].path 这里面
                    b.tplArea(),
                    b.tplEditor()
                console.log(b.areas[c][d].areas[e]);
            });

            b.tplArea(); // 单击了直接显示配送范围input框
            b.tplEditor() // 单击了直接显示描边形状
        }),
            a(document).off("click", ".area-item .editor-area-item"),
            a(document).on("click", ".area-item .editor-area-item", function () {
            console.log('编辑执行');

            var type = a(this).data("type"),
                parentid = a(this).data("parentid"),
                id = a(this).data("id");
            if (!(type && parentid && id && b.polygons[type][parentid][id])) return !1;
            b.isActive = 1;
            var f = b.areas[type][parentid].areas[id];
            f.isActive = 1,
            f.isAdd = 0,
            new AMap.PolyEditor(map, b.polygons[type][parentid][id]).open(),
            b.tplArea();
                console.log(b.polygons[type]);
                
        }),
            a(document).off("click", ".area-item .btn-reset"),
            a(document).on("click", ".area-item .btn-reset", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("退出编辑后，此次修改将不会生效，是否确定退出？", function () {
                b.isActive = 0;
                var a = b.areas[c][d].areas[e];
                a.isActive = 0, 1 == a.isAdd ? delete b.areas[c][d].areas[e] : b.areas[c][d].areas[e] = b.areasOriginal[c][d].areas[e],
                    b.tplArea();
                    b.tplEditor();
            })
        }),
            a(document).off("click", ".area-item .btn-delete"),
            a(document).on("click", ".area-item .btn-delete", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("确定删除此区域吗？", function () {
                b.isActive = 0,
                    delete b.areas[c][d].areas[e],
                    b.tplArea(),
                    b.tplEditor()
            })
        }),
            a(document).off("click", ".area-item .btn-save"),
            a(document).on("click", ".area-item .btn-save", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("确定对该区域进行修改？", function () {
                var a = b.polygons[c][d][e];
                console.log(a);
                console.log(a.getPath());
                if (b.areas[c][d].areas[e].path = a.getPath(), !b.areas[c][d].areas[e].path.length) return void Notify.info("请设置高德配送范围！");
                b.areas[c][d].areas[e].isActive = 0,
                b.isActive = 0,
                b.tplArea()
                b.tplEditor()
            })
        }),
            a(document).off("click", "#add-hour"),
            a(document).on("click", "#add-hour", function () {
            var a = "special", c = b.getId("M", 0);
            if (1 == b.isActive) return !1;
            var d = b.getId("M", 0);
            if (b.length(b.areas[a]) >= 10) return void Notify.info("最多可添加10个特殊时段！");
            var e = b.getColor(a, c), f = b.colors[e];
            b.isActive = 1, b.areas[a][c] = {
                startHour: "00:00",
                endHour: "00:00",
                areas: {}
            },
                b.areas[a][c].areas[d] = {
                delivery_price: 0,
                delivery_free_price: 0,
                send_price: 0,
                description: "",
                strokeColor: f.strokeColor,
                fillColor: f.fillColor,
                isActive: 1,
                isAdd: 1,
                path: [],
                colorType: e
            }, {}[d] = {}, b.polygons[a][c] || (b.polygons[a][c] = {}), b.polygons[a][c][d] = {}; //添加
            var g = new AMap.MouseTool(map);
            g.polygon();
            AMap.event.addListener(g, "draw", function (e) {
                g.close();
                var f = e.obj;
                new AMap.PolyEditor(map, f).open(), b.areas[a][c].areas[d].path = f.getPath(), b.tplArea(), b.tplEditor()
            }), b.tplArea(), b.tplEditor()
        }),
            a(document).on("input propertychange change", ".diy-bind", function () {
            var c = a(this), d = c.data("bind"), e = c.data("bind-type"), f = c.data("bind-ancestor"),
                g = c.data("bind-child"), h = c.data("bind-parent"), i = "", j = this.tagName;
            if ("INPUT" == j) {
                var k = c.data("placeholder");
                i = c.val(), i = "" == i ? k : i
            } else "SELECT" == j ? i = c.find("option:selected").val() : "TEXTAREA" == j && (i = c.val());
            i = a.trim(i), e ? b.areas[e][f][g][h][d] = i : f ? b.areas[f][g][h][d] = i : g ? b.areas[g][h][d] = i : h ? b.areas[h][d] = i : b.areas[d] = i
        })
    },
    b.getColor = function (c, d) {
        var e = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        if (b.areas[c][d] && b.areas[c][d].areas) for (var f in b.areas[c][d].areas) {
            var g = a.inArray(b.areas[c][d].areas[f].colorType, e);
            -1 != g && e.splice(g, 1)
        }
        return e.shift()
    }, 
    b.length = function (a) {
        if (void 0 === a) return 0;
        var b = 0;
        for (var c in a) b++;
        return b
    }, 
    b.getId = function (a, b) {
        return a + (+new Date + b)
    }, b

});