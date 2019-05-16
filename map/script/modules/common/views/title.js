/**
 * @file 页标题 Reflux View
 * @author 崔健 cuijian03@baidu.com 2016.08.20
 */

import React, { Component } from 'react'
import { render } from 'react-dom'
import CommonAction from '../actions/commonAction'
import CommonStore from '../stores/commonStore'
var Title = React.createClass({
    getInitialState: function() {
        return {
            // 当前公司名
            companyName: '车辆管理'
        }
    },
    componentDidMount: function () {
        CommonAction.getcompanyname();
        CommonStore.listen(this.onStatusChange);
    },
    onStatusChange: function (type,data) {
        switch (type){
            case 'companyname':
                this.lisenUpdateCompanyName(data);
                break;
        }
    },
    /**
     * 响应Store companyname事件，更新公司名称
     *
     * @param {data} 标签页标识
     */
    lisenUpdateCompanyName: function(data) {
        this.setState({companyName: data});
    },
    render: function () {
        var companyName = this.state.companyName;
    	var logo = __uri('/static/images/logo_2x.png');
        return (
            <div className="title">
                <span className="headName">{companyName}</span>
            </div>
        )
    }
});

export default Title;
