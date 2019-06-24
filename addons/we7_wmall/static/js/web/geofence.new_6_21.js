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
            var lat = parseFloat(a.store.location_x);
            var lng = parseFloat(a.store.location_y);
            var mapOptions = {
                zoom: 14,
                center: {
                    lat: lat,
                    lng: lng
                },
                disableDefaultUI: true
            };
            window.map = new google.maps.Map(document.getElementById('allmap'), mapOptions);


            window.tmodtpl = a.tmodtpl;
            b.isChange = a.isChange,
            b.store = a.store,

            //如果是空的则初始化参数
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
        //-----谷歌
            b.tplGoogle(), //初始化显示谷歌绘图
        //-----谷歌
            b.initDom() //调用编辑和添加配送范围方法

    },
        b.tplArea = function () {
        var c = tmodtpl("tpl-area", b);
        a(".geofence-container").html(c) // 添加区域及配送范围
    },
        b.markerStore = function () {
        if (b.store.location_y && b.store.location_x) {
            var lat = parseFloat(b.store.location_x);
            var lng = parseFloat(b.store.location_y);
            new google.maps.Marker({//定义标记
                position: {lat: lat, lng: lng},
                icon: {url: '../addons/we7_wmall/static/img/shop_marker.png', scaledSize: {width: 40, height: 50}},
                map: map
            });
        }
    },
        b.tplGoogle = function () {
        a(".clockpicker :text").clockpicker({
            autoclose: !0, afterDone: function () { //点击时钟完成时自动关闭
                a(".clockpicker :text").trigger("change"), b.tplArea(), b.tplGoogle()
            }
        }),

        b.markerStore();  //放置门店icon
            //循环遍历绘出多边形
            a.each(b.areas, function (c, d) {
            a.each(d, function (d, e) {
                b.polygons[c][d] = {},
                a.each(e.areas, function (a, e) {
                    var points=[];
                    for(var i=0; i < e.path.length; i++) { //转换成谷歌地图需要的经纬度对象
                        points.push(new google.maps.LatLng(e.path[i][1],
                            e.path[i][0]));
                    }
                    window.gPolygon = new google.maps.Polygon({ //谷歌绘图配置
                        paths: points,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        strokeColor: e.strokeColor,
                        fillColor: e.fillColor,
                        fillOpacity: 0.35
                    });
                    b.polygons[c][d][a] = gPolygon,
                    gPolygon.setMap(map); //显示绘图工具
                })
            })
        }),

            a(':hidden[name="areas"]').val(encodeURI(JSON.stringify(b.areas))) //把配送范围信息放入隐藏框
    },
    b.initDom = function () {
        // window.drawingManager = '';

        a('[data-toggle="tooltip"]').tooltip(),
        a(document).off("click", ".area-add"),
        a(document).on("click", ".area-add", function () {
            var c = a(this).data("type"), d = a(this).data("parentid");
            if (1 == b.isActive) return !1;
            var e = b.getId("M", 0); //给新增配送范围添加编号
            if (b.length(b.areas[c][d].areas) >= 10) return void Notify.info("最多可添加10个！");
            var f = b.getColor(c, d), //f是当前添加个数
                g = b.colors[f]; // g是获得当前的颜色
                b.isActive = 1;

            //高德地图设置绘图样式
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

                // b.polygons[c][d] || (b.polygons[c][d] = {}),
                // b.polygons[c][d][e] = {};

            //----------------谷歌地图进行绘图
            window.field = new google.maps.Polygon({
                paths: [],
                strokeColor: g.strokeColor,
                fillColor: g.fillColor
            });

            window.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                polygonOptions: {

                    strokeColor: g.strokeColor,
                    fillColor: g.fillColor,
                    editable: false
                },
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                }
            });
            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                drawingManager.setOptions({
                    drawingMode: null,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: []
                    },
                    polygonOptions: {
                        strokeColor: g.strokeColor,
                        fillColor: g.fillColor,
                        editable: true
                    }
                });
                field.setPath(polygon.getPath().getArray());
                polygon.setMap(null);
                polygon = null;
                field.setMap(map);
                //循环放入
                b.areas[c][d].areas[e].path = [];//先赋空值

                var polygonBounds = field.getPath();
                var coordinates = [];
                for(var i = 0 ; i < polygonBounds.length ; i++)
                {
                    coordinates.push([polygonBounds.getAt(i).lng(),polygonBounds.getAt(i).lat()]);
                }
                b.areas[c][d].areas[e].path = coordinates; //经纬度赋值给path变量

                b.areas[c][d].field = 'add';

            });
            b.tplArea(); // 单击了直接显示配送范围input框
        }),
            a(document).off("click", ".area-item .editor-area-item"),
            a(document).on("click", ".area-item .editor-area-item", function () {
            //单击编辑
            var c = a(this).data("type"),
                d = a(this).data("parentid"),
                e = a(this).data("id");
            if (!(c && d && e && b.polygons[c][d][e])) return !1;
            b.isActive = 1;
            var f = b.areas[c][d].areas[e];
            f.isActive = 1,
            f.isAdd = 0;
            b.areas[c][d].field = 'normal';
                console.log(b.areas[c][d].field);
                if( b.areas[c][d].field == 'normal' || b.areas[c][d].field == undefined) {
                    b.polygons[c][d][e].setEditable(true);
                    //删除顶点
                    google.maps.event.addListener(b.polygons[c][d][e], 'click', function(event) { //参数一:多边形形状,参数二:单击事件
                        if (event.vertex == undefined) {
                            return;
                        }
                        (b.polygons[c][d][e]).getPath().removeAt(event.vertex); //执行删除顶点
                    });

                }
                if ( b.areas[c][d].field == 'add' ) {
                    // if ( check_drawingManager == 'add') {drawingManager.setMap(null); } //删除绘画控件
                    // field.setMap(null); //删除多边形
                    drawingManager.setMap(null);//删除绘画控件
                    field.setEditable(true); //设置可编辑
                    // b.areas[c][d].field = 'normal';
                }

                // b.polygons[c][d][e].setEditable(true);
                b.tplArea();
        }),
            a(document).off("click", ".area-item .btn-reset"),
            a(document).on("click", ".area-item .btn-reset", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("退出编辑后，此次修改将不会生效，是否确定退出？", function () {
                console.log(b.areas[c][d].field);
                console.log(b.polygons[c][d][e]);
                if( b.areas[c][d].field == 'normal' || b.areas[c][d].field == undefined) {
                    b.polygons[c][d][e].setMap(null);
                }
                if ( b.areas[c][d].field == 'add' ) {
                    // if ( check_drawingManager == 'add') {drawingManager.setMap(null); } //删除绘画控件
                    // field.setMap(null); //删除多边形
                    drawingManager.setMap(null);//删除绘画控件
                    field.setMap(null); //删除多边形
                    // b.areas[c][d].field = 'normal';
                }

                    b.isActive = 0;
                    var a = b.areas[c][d].areas[e];
                    a.isActive = 0,
                        1 == a.isAdd ? delete b.areas[c][d].areas[e] : b.areas[c][d].areas[e] = b.areasOriginal[c][d].areas[e],
                        b.tplArea();
                    b.tplGoogle();
            })
        }),
            a(document).off("click", ".area-item .btn-delete"),
            a(document).on("click", ".area-item .btn-delete", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("确定删除此区域吗？", function () {

                if( b.areas[c][d].field == 'normal' || b.areas[c][d].field == undefined) {
                    b.polygons[c][d][e].setMap(null);
                }
                if ( b.areas[c][d].field == 'add' ) {
                    // if ( check_drawingManager == 'add') {drawingManager.setMap(null); } //删除绘画控件
                    // field.setMap(null); //删除多边形
                    drawingManager.setMap(null);//删除绘画控件
                    field.setMap(null); //删除多边形
                    b.polygons[c][d][e].setMap(null);

                    // b.areas[c][d].field = 'normal';
                }

                    b.isActive = 0,
                    delete b.areas[c][d].areas[e], //删除对象
                    b.tplArea();
                a(':hidden[name="areas"]').val(encodeURI(JSON.stringify(b.areas))) //把配送范围信息放入隐藏框
            })
        }),
            a(document).off("click", ".area-item .btn-save"),
            a(document).on("click", ".area-item .btn-save", function () {
            var c = a(this).data("type"), d = a(this).data("parentid"), e = a(this).data("id");
            Notify.confirm("确定对该区域进行修改？", function () {
                // var a = b.polygons[c][d][e];
                // console.log(a);
                // console.log(typeof(drawingManager));

                if( b.areas[c][d].field == 'normal' || b.areas[c][d].field == undefined) {
                    b.polygons[c][d][e].setEditable(false);

                    var polygonBounds = b.polygons[c][d][e].getPath();
                    var coordinates = [];
                    for(var i = 0 ; i < polygonBounds.length ; i++)
                    {
                        coordinates.push([polygonBounds.getAt(i).lng(),polygonBounds.getAt(i).lat()]);
                    }
                    b.areas[c][d].areas[e].path = coordinates; //经纬度赋值给path变量
                    // console.log(b.areas[c][d].areas);
                }
                if ( b.areas[c][d].field == 'add' ) {
                    // if ( check_drawingManager == 'add') {drawingManager.setMap(null); } //删除绘画控件
                    // field.setMap(null); //删除多边形
                    drawingManager.setMap(null);//删除绘画控件
                    field.setEditable(false); //设置不可编辑
                    b.areas[c][d].field = 'normal';
                }

                console.log(b.areas[c][d].areas);
                b.areas[c][d].areas[e].isActive = 0,
                b.isActive = 0,
                b.tplArea();

                a(':hidden[name="areas"]').val(encodeURI(JSON.stringify(b.areas))) //把配送范围信息放入隐藏框
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
            }, b.areas[a][c].areas[d] = {
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

            // var g = new AMap.MouseTool(map);
            // g.polygon();
            // AMap.event.addListener(g, "draw", function (e) {
            //     g.close();
            //     var f = e.obj;
            //     new AMap.PolyEditor(map, f).open(),
                //     b.areas[a][c].areas[d].path = f.getPath(),
                //     b.tplArea(), b.tplGoogle()
            // });
                //----------------谷歌地图进行绘图
                window.field = new google.maps.Polygon({
                    paths: [],
                    strokeColor: f.strokeColor,
                    fillColor: f.fillColor
                });

                window.drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    polygonOptions: {

                        strokeColor: f.strokeColor,
                        fillColor: f.fillColor,
                        editable: false
                    },
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                    }
                });
                drawingManager.setMap(map);

                google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                    drawingManager.setOptions({
                        drawingMode: null,
                        drawingControlOptions: {
                            position: google.maps.ControlPosition.TOP_CENTER,
                            drawingModes: []
                        },
                        polygonOptions: {
                            strokeColor: f.strokeColor,
                            fillColor: f.fillColor,
                            editable: true
                        }
                    });
                    field.setPath(polygon.getPath().getArray());
                    polygon.setMap(null);
                    polygon = null;
                    field.setMap(map);
                    //循环放入
                    b.areas[c][d].areas[e].path = [];//先赋空值
                    var polygonBounds = field.getPath();
                    var coordinates = [];
                    for(var i = 0 ; i < polygonBounds.length ; i++)
                    {
                        coordinates.push([polygonBounds.getAt(i).lng(),polygonBounds.getAt(i).lat()]);
                    }
                    b.areas[c][d].areas[e].path = coordinates; //经纬度赋值给path变量
                    b.areas[c][d].field = 'add';
                });

            b.tplArea();
            // b.tplGoogle()
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