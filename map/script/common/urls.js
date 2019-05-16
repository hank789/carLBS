/**
 * @file 存放php proxy的url和Jquery AJAX方法 
 * @author 崔健 cuijian03@baidu.com 2016.08.20
 */
import Commonfun from 'commonfun'
var urls = {
    // 矩形区域检索entity
    boundsearchEntity: '/admin/map/entity/boundsearch',
    // 获取track的distance
    getDistance: '/admin/map/track/getdistance',
    // 获取track信息
    getTrack: '/admin/map/track/gettrack',

    // 获取自定义字段列表
    columnsList: '/admin/map/entity/listcolumn',
    // 经纬度解析
    getAddress: '/admin/map/getAddress',
    // 通过新的search接口获取数据，包括所有entity、模糊搜索entity、在线entity、离线entity
    // searchEntity: '//yingyan.baidu.com/api/v3/entity/search',
    searchEntity: '/admin/map/entity/search',
    // 获取track列表
    trackList: '/admin/map/track/gethistory',
    // 获取停留点
    getstaypoint: '/admin/map/analysis/staypoint',
    // 获取驾驶行为分析信息
    getBehaviorAnalysis: '/admin/map/analysis/drivingbehavior',
    // 获取公司信息
    getCompany: '/admin/map/getCompany',

    /**
     * Jquery AJAX GET
     *
     * @param {string} url 请求url
     * @param {object} params 请求参数
     * @param {function} success 请求成功回调函数
     * @param {function} before 请求前函数
     * @param {function} fail 请求失败回调函数
     * @param {function} after 请求完成回调函数
     */
    post: function (url, params, success, before, fail, after) {
        if (before) {
            before();
        }
        params.timeStamp = new Date().getTime();
        fail = fail || function () { };
        after = after || function () { };
        // 严重推荐自己编写代理服务，将service_id和ak隐藏！！通过service_id和ak可以
        // 拿到该服务的所有数据，一旦泄露，后果严重!!!
        params.ak = Commonfun.getQueryString('ak');
        params.service_id = Commonfun.getQueryString('service_id');
        $.ajax({
            type: 'POST',
            url: url,
            data: params,
            dataType: 'json',
            success: success,
            error: fail,
            complete: after
        });
    },
  /**
   * Jquery AJAX GET
   *
   * @param {string} url 请求url
   * @param {object} params 请求参数
   * @param {function} success 请求成功回调函数
   * @param {function} before 请求前函数
   * @param {function} fail 请求失败回调函数
   * @param {function} after 请求完成回调函数
   */
  get: function (url, params, success, before, fail, after) {
    if (before) {
      before();
    }
    params.timeStamp = new Date().getTime();
    fail = fail || function () { };
    after = after || function () { };

    $.ajax({
      type: 'GET',
      url: url,
      data: params,
      dataType: 'json',
      success: success,
      error: fail,
      complete: after
    });
  },

    /**
     * JSONP
     *
     * @param {string} url 请求url
     * @param {object} params 请求参数
     * @param {function} callbakc 请求成功回调函数
     * @param {function} before 请求前函数
     */
    jsonp: function (url, params, callback, before) {
        this.get(url, params,callback,before);
        return;
        var that = this;
        if (before) {
            before();
        }
        params.timeStamp = new Date().getTime();
        params.ak = '';
        params.service_id = 0;
        url = url + '?';
        for (let i in params) {
            url = url + i + '=' + params[i] + '&';
        }
        
        var timeStamp = (Math.random() * 100000).toFixed(0);
        window['ck' + timeStamp] = callback || function () {};
        var completeUrl = url + '&callback=ck' + timeStamp;
        var script = document.createElement('script');
        script.src = completeUrl;
        script.id = 'jsonp';
        document.getElementsByTagName('head')[0].appendChild(script);
        script.onload = function (e) {
            $('#jsonp').remove();
        };
        script.onerror = function (e) {
            that.jsonp(url, params, callback, before)
        };
    }
}

export default urls;