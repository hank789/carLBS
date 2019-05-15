<?php namespace App\Http\Controllers\Backend\Company;
/**
 * @author: wanghui
 * @date: 2019/5/13 10:29 PM
 * @email:    hank.HuiWang@gmail.com
 */

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\Company\VendorRequest;
use App\Models\Auth\Company;
use App\Models\Auth\VendorCompany;

class VendorController extends Controller
{
    /**
     * @param VendorRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(VendorRequest $request)
    {
        $filter =  $request->all();
        $query = VendorCompany::query();

        if (isset($filter['company_name']) && $filter['company_name']) {
            $query = $query->where('company_name','like','%'.$filter['company_name'].'%');
        }

        $list = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.company.vendor.index')
            ->with('companies',$list)->with('filter',$filter);
    }

    /**
     * @param VendorRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(VendorRequest $request, $id)
    {
        $main = VendorCompany::find($id);
        return view('backend.company.vendor.show')
            ->with('main',$main);
    }

    /**
     * @param VendorRequest    $request
     *
     * @return mixed
     */
    public function create(VendorRequest $request)
    {
        $user = $request->user();
        return view('backend.company.vendor.create')->with('company',$user->company());
    }

    /**
     * @param VendorRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(VendorRequest $request)
    {
        $vendor = $request->input('company_name');
        $user = $request->user();
        $userCompany = $user->company();
        $exist = VendorCompany::where('company_name',$vendor)->where('company_id',$userCompany->id)->first();
        if ($exist) {
            throw new GeneralException('该供应商已存在');
        }
        VendorCompany::create([
            'company_name' => $vendor,
            'company_id' => $userCompany->id
        ]);
        return redirect()->route('admin.company.vendor.index')->withFlashSuccess('供应商添加成功');
    }

    public function edit(VendorRequest $request,$id)
    {
        $vendor = VendorCompany::find($id);
        return view('backend.company.vendor.edit')
            ->with('vendor',$vendor);
    }


    public function update(VendorRequest $request, $id)
    {
        $vendor = VendorCompany::find($id);
        $user = $request->user();
        $userCompany = $user->company();
        if (get_class($userCompany) != Company::class || $userCompany->id != $vendor->company_id) {
            throw new GeneralException('您非公司管理员，无法修改供应商');
        }
        $vendor->company_name = $request->input('company_name');
        $vendor->save();
        return redirect()->route('admin.company.vendor.index')->withFlashSuccess('供应商修改成功');
    }

}