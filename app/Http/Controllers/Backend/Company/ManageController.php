<?php namespace App\Http\Controllers\Backend\Company;
/**
 * @author: wanghui
 * @date: 2019/5/13 10:29 PM
 * @email:    hank.HuiWang@gmail.com
 */

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\Company\ManageRequest;
use App\Models\Auth\Company;

class ManageController extends Controller
{
    /**
     * @param ManageRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageRequest $request)
    {
        $filter =  $request->all();
        $query = Company::where('company_type',Company::COMPANY_TYPE_MAIN);

        if (isset($filter['company_name']) && $filter['company_name']) {
            $query = $query->where('company_name','like','%'.$filter['company_name'].'%');
        }

        $list = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.company.manage.index')
            ->with('companies',$list)->with('filter',$filter);
    }

    /**
     * @param ManageRequest $request
     * @return mixed
     */
    public function show(ManageRequest $request, $id)
    {
        $company = Company::find($id);
        return view('backend.company.manage.show')
            ->with('company',$company);
    }

    /**
     * @param ManageRequest    $request
     *
     * @return mixed
     */
    public function create(ManageRequest $request)
    {
        return view('backend.company.manage.create');
    }

    /**
     * @param ManageRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(ManageRequest $request)
    {
        $company_name = $request->input('company_name');
        $status = $request->input('active',0);
        $exist = Company::where('company_name',$company_name)->first();
        if ($exist) {
            throw new GeneralException('该公司已存在');
        }
        Company::create([
            'company_name' => $company_name,
            'company_type' => Company::COMPANY_TYPE_MAIN,
            'status' => $status
        ]);
        return redirect()->route('admin.company.manage.index')->withFlashSuccess('公司添加成功');
    }

    public function edit(ManageRequest $request,$id)
    {
        $company = Company::find($id);
        return view('backend.company.manage.edit')
            ->with('company',$company);
    }


    public function update(ManageRequest $request, $id)
    {
        \Log::info('test',$request->all());
        $company = Company::find($id);
        $company->company_name = $request->input('company_name');
        $company->status = $request->input('active',0);
        $company->save();
        return redirect()->route('admin.company.manage.index')->withFlashSuccess('公司修改成功');
    }

    public function mark(ManageRequest $request, $id, $status)
    {
        $company = Company::find($id);

        \Log::info('test',[$id,$status]);
        $company->status = $status;
        $company->save();

        return response('success');
    }

}