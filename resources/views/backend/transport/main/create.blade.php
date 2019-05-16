@extends('backend.layouts.app')

@section('title', '行程管理 | 添加行程')

@section('head-script')
    <script type="text/javascript" src="https://api.map.baidu.com/api?v=3.0&ak=oOBCAkeREzlDTnhp6MT1BcEiovp51S1l"></script>
@endsection

@section('content')
{{ html()->form('POST', route('admin.transport.main.store'))->class('form-horizontal')->open() }}
    <input type="hidden" id="transport_end_place_longitude" name="transport_end_place_longitude" />
    <input type="hidden" id="transport_end_place_latitude" name="transport_end_place_latitude" />
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        行程管理
                        <small class="text-muted">添加行程</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label('出发地')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_start_place') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_start_place')
                                ->class('form-control')
                                ->placeholder('车队出发地')
                                ->attribute('maxlength', 191)
                                ->required()
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('目的地')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_end_place') }}

                        <div class="col-md-10">
                            <div class="input-group">
                                {{ html()->text('transport_end_place')
                                ->class('form-control')
                                ->placeholder('车队目的地')
                                ->attribute('readonly')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                                <span class="input-group-prepend">
                                <button id="search-map" class="btn btn-primary" type="button" data-toggle="modal" data-target="#map-modal"><i class="fa fa-search"></i> 地图选址</button>
                            </span>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('供应商')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_vendor_company') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_vendor_company')
                                ->class('form-control')
                                ->placeholder('供应商')
                                ->attribute('maxlength', 255)
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('供应商联系人')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_vendor_people') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_vendor_people')
                                ->class('form-control')
                                ->placeholder('供应商联系人')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('供应商联系人电话')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_vendor_phone') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_vendor_phone')
                                ->class('form-control')
                                ->placeholder('供应商联系人电话')
                                ->type('number')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('目的地联系人')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_people') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_people')
                                ->class('form-control')
                                ->placeholder('目的地联系人')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('目的地联系人电话')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_phone') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_phone')
                                ->class('form-control')
                                ->placeholder('目的地联系人电话')
                                ->type('number')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('出发时间')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_start_time') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_start_time')
                                ->class('form-control')
                                ->placeholder('出发时间')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('状态')->class('col-md-2 form-control-label')->for('transport_status') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" name="transport_status">
                                <label class="form-check-label" for="inline-radio1">暂不发布</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" checked name="transport_status">
                                <label class="form-check-label" for="inline-radio2">马上发布</label>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('司机手机号列表(多个以逗号隔开)')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_phone_list') }}

                        <div class="col-md-10">
                            {{ html()->textarea('transport_phone_list')
                                ->class('form-control')
                                ->attribute('rows',9)
                                ->placeholder('司机手机号列表，以逗号隔开(如：15050378283,15050458789)，行程开始后会以短信通知司机')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('货物信息')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_goods') }}

                        <div class="col-md-10">
                            {{ html()->textarea('transport_goods')
                                ->class('form-control')
                                ->attribute('rows',9)
                                ->placeholder('货物信息')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.transport.main.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.create')) }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->form()->close() }}
<div class="modal fade" id="map-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">选择地址</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="r-result" style="width: 100%;">请输入:<input type="text" id="suggestId" size="20" value="百度" style="width:600px;" /></div>
                <div id="searchResultPanel" style="border:1px solid #C0C0C0;width:150px;height:auto; display:none;"></div>
                <div id='allmap' style='width: 100%; height: 400px;'></div>
                <div id="rr-result" style="width: 100%;"></div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">确认</button>
            </div>
        </div>

    </div>

</div>
@endsection
@section('script')
    <style>
        .tangram-suggestion-main {
            z-index: 1051;
        }
    </style>
    <script type="text/javascript">
        $(function(){
            /*daterange控件*/
            $('#transport_start_time').daterangepicker({
                timePicker: true,
                singleDatePicker: true,
                timePicker24Hour: true,
                locale: {
                    applyLabel: '确认',
                    cancelLabel: '取消',
                    fromLabel: '从',
                    toLabel: '到',
                    weekLabel: '星期',
                    customRangeLabel: '自定义范围',
                    daysOfWeek: '日_一_二_三_四_五_六'.split('_'),
                    monthNames: '1月_2月_3月_4月_5月_6月_7月_8月_9月_10月_11月_12月'.split('_'),
                    firstDay: 1,
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

            function G(id) {
                return document.getElementById(id);
            }

            var map = new BMap.Map("allmap");
            var geoc = new BMap.Geocoder();   //地址解析对象
            var markersArray = [];
            var geolocation = new BMap.Geolocation();


            var point = new BMap.Point(116.404412, 39.914714);
            map.centerAndZoom(point, 12); // 中心点
            map.enableScrollWheelZoom();
            geolocation.getCurrentPosition(function (r) {
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    var mk = new BMap.Marker(r.point);
                    map.addOverlay(mk);
                    map.panTo(r.point);
                    map.enableScrollWheelZoom(true);
                }
            }, {enableHighAccuracy: true});


            var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
                {"input" : "suggestId"
                    ,"location" : map
                });
            ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
                var str = "";
                var _value = e.fromitem.value;
                var value = "";
                if (e.fromitem.index > -1) {
                    value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
                }
                str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

                value = "";
                if (e.toitem.index > -1) {
                    _value = e.toitem.value;
                    value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
                }
                str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
                G("searchResultPanel").innerHTML = str;
            });

            var myValue;
            ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
                var _value = e.item.value;
                myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
                G("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
                document.getElementById('transport_end_place').value = myValue;
                setPlace();
            });

            $('#suggestId').bind('keydown',function(event){
                if(event.keyCode == "13") {
                    map.clearOverlays();    //清除地图上所有覆盖物
                    var local = new BMap.LocalSearch(map, { //智能搜索
                        onInfoHtmlSet: function(poi,html) {
                            html.addEventListener("click", function(e) {
                                if (confirm("确定选取该地址？")) {
                                    var pp = poi.marker.getPosition();
                                    console.log(JSON.stringify(pp));
                                    if (pp.lng) {
                                        document.getElementById('transport_end_place_longitude').value = pp.lng;
                                        document.getElementById('transport_end_place_latitude').value = pp.lat;

                                        //alert("marker2的位置是" + poi.marker.getPosition().lng + "," + poi.marker.getPosition().lat);
                                        document.getElementById('transport_end_place').value = html.getElementsByTagName('td')[1].innerHTML.replace('&nbsp;','');
                                        //alert(html.getElementsByTagName('td')[1].innerHTML.replace('&nbsp;',''));
                                    } else {
                                        alert("未能获取到地址，请刷新当前页面后再试~");
                                    }
                                }
                            });
                        },
                        renderOptions: {map: map, panel: "rr-result"}
                    });
                    local.search($('#suggestId').val());
                }
            });

            function setPlace(){
                map.clearOverlays();    //清除地图上所有覆盖物
                function myFun(){
                    var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
                    map.centerAndZoom(pp, 18);
                    map.addOverlay(new BMap.Marker(pp));    //添加标注
                    document.getElementById('transport_end_place_longitude').value = pp.lng;
                    document.getElementById('transport_end_place_latitude').value = pp.lat;

                    console.log(JSON.stringify(pp));
                }
                var local = new BMap.LocalSearch(map, { //智能搜索
                    onSearchComplete: myFun
                });
                local.search(myValue);
            }


            //点击地图时间处理
            function showInfo(e) {
                //document.getElementById('lng').value = e.point.lng;
                //document.getElementById('lat').value =  e.point.lat;
                geoc.getLocation(e.point, function (rs) {
                    var addComp = rs.addressComponents;
                    var address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber;
                    if (confirm("确定要地址是" + address + "?")) {
                        document.getElementById('transport_end_place').value = address;
                    }
                });
                addMarker(e.point);
            }
        });
    </script>
@endsection