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
use App\Models\Auth\CompanyRel;

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
        $user = $request->user();
        if ($user->company_id != 1) {
            $query = CompanyRel::where('company_id',$user->company_id)->leftJoin('company','company_rel.vendor_id','=','company.id');;
        } else {
            $query = CompanyRel::leftJoin('company','company_rel.vendor_id','=','company.id');;
        }

        if (isset($filter['company_name']) && $filter['company_name']) {
            $query = $query->where('company_name','like','%'.$filter['company_name'].'%');
        }

        $list = $query->orderBy('company.id','desc')->paginate(config('backend.page_size'));
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
        $main = Company::find($id);
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
        return view('backend.company.vendor.create')->with('company',$user->company);
    }

    /**
     * @param VendorRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(VendorRequest $request)
    {
        $company_name = $request->input('company_name');
        $user = $request->user();
        $userCompany = $user->company;
        $vendor = Company::where('company_name',$company_name)->first();
        if (!$vendor) {
            $vendor = Company::create([
                'company_name' => $company_name,
                'company_type' => Company::COMPANY_TYPE_VENDOR
            ]);
        }
        $rel = CompanyRel::where('company_id',$userCompany->id)->where('vendor_id',$vendor->id)->first();
        if (!$rel) {
            CompanyRel::create([
                'company_id' => $userCompany->id,
                'vendor_id' => $vendor->id
            ]);
        } else {
            throw new GeneralException('供应商已存在');
        }

        return redirect()->route('admin.company.vendor.index')->withFlashSuccess('供应商添加成功');
    }

    public function edit(VendorRequest $request,$id)
    {
        $vendor = Company::find($id);
        return view('backend.company.vendor.edit')
            ->with('vendor',$vendor);
    }


    public function update(VendorRequest $request, $id)
    {
        $vendor = Company::find($id);
        $user = $request->user();
        $userCompany = $user->company;
        $company_name = $request->input('company_name');

        $rel = CompanyRel::where('company_id',$userCompany->id)->where('vendor_id',$vendor->id)->first();
        if (!$rel) {
            throw new GeneralException('您非公司管理员，无法修改供应商');
        }
        $exist = Company::where('company_name',$company_name)->where('id','!=',$vendor->id)->first();
        if ($exist) {
            throw new GeneralException('供应商已存在');
        }
        $vendor->company_name = $company_name;
        $vendor->save();
        return redirect()->route('admin.company.vendor.index')->withFlashSuccess('供应商修改成功');
    }

}