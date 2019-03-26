<?php
/**
 * @author: wanghui
 * @date: 2019/3/15 1:12 AM
 * @email:    hank.HuiWang@gmail.com
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ManageVersionRequest;
use App\Models\System\AppVersion;

class VersionController extends Controller
{
    /*新闻创建校验*/
    protected $validateRules = [
        'app_version'        => 'required|regex:/^([0-9]+.[0-9]+.[0-9])/',
        'package_url' => 'required|max:255',
        'is_ios_force' => 'required|in:0,1,2',
        'is_android_force' => 'required|in:0,1,2',
        'update_msg' => 'required|max:255',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManageVersionRequest $request)
    {
        $query = AppVersion::query();

        $versions = $query->orderBy('app_version','desc')->paginate(20);
        return view("backend.version.index")->with('versions',$versions);
    }



    public function create()
    {
        return view("backend.version.create");
    }

    public function store(ManageVersionRequest $request)
    {
        $loginUser = $request->user();

        $request->flash();

        $this->validateRules['app_version'] = 'required|regex:/^([0-9]+.[0-9]+.[0-9])/|unique:app_version';

        $this->validate($request,$this->validateRules);

        $data = [
            'user_id'      => $loginUser->id,
            'app_version'        => trim($request->input('app_version')),
            'package_url'  =>$request->input('package_url'),
            'is_ios_force' => $request->input('is_ios_force'),
            'is_android_force' => $request->input('is_android_force'),
            'update_msg'   => $request->input('update_msg'),
            'status'       => 0,
        ];

        $version = AppVersion::create($data);

        if($version){
            $message = '发布成功,等待管理员审核! ';
            return $this->success(route('admin.version.index'),$message);
        }

        return  $this->error(route('admin.version.index'),"发布失败，请稍后再试");

    }

    /**
     * 显示文字编辑页面
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,ManageVersionRequest $request)
    {
        $version = AppVersion::find($id);

        if(!$version){
            abort(404);
        }

        return view("backend.version.edit")->with(compact('version'));

    }


    public function update(ManageVersionRequest $request)
    {
        $id = $request->input('id');
        $version = AppVersion::find($id);
        if(!$version){
            abort(404);
        }

        $request->flash();

        $this->validate($request,$this->validateRules);

        $version->app_version = trim($request->input('app_version'));
        $version->package_url = trim($request->input('package_url'));
        $version->is_ios_force = trim($request->input('is_ios_force'));
        $version->is_android_force = trim($request->input('is_android_force'));
        $version->update_msg = $request->input('update_msg');

        $version->save();

        return $this->success(route('admin.version.index'),"编辑成功");
    }


    /*审核*/
    public function verify($id,ManageVersionRequest $request)
    {
        \Log::info('test',[$id]);
        AppVersion::where('id',$id)->update(['status'=>1]);

        return $this->ajaxSuccess('审核成功');
    }

    /**
     * 删除
     */
    public function destroy($id,ManageVersionRequest $request)
    {
        AppVersion::where('id',$id)->update(['status'=>0]);
        return $this->ajaxSuccess('禁用成功');
    }
}