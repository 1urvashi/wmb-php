<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Make;
use App\Models;
use Yajra\Datatables\Datatables;
use Validator;
use DB;
use Auth;
use Redirect;
use Gate;

class ModelController extends Controller {

     public function __construct(){
        $user = Auth::guard('admin')->user();
        // if(Gate::denies('ModelMenu')){
        //      return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        // }
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(Gate::denies('model_read')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
       $makes = Make::orderBy('name','asc')->get();
        return view('admin.modules.models.index',compact('makes'));
    }

    public function data(Request $request) {
        DB::statement(DB::raw('set @rownum=0'));
        $models = Models::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'models.id', 'models.name', 'makes.name as mkname','models.make_id as make_id'])
                        ->leftJoin('makes', 'models.make_id', '=', 'makes.id')
                        ->orderBy('models.id', 'asc')->get();
        return Datatables::of($models)
                        ->addColumn('action', function ($models) {
                            $a = '';
                            if (Gate::allows('model_update')) {
                                $a .='<a href="model/' . $models->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                            }

                            return $a;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('make') && ($request->get('make')!=0)) {
                                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                        return ($row['make_id'] == $request->get('make')) ? true : false;
                                    });
                            }
                            if ($request->has('search') && ($request->get('search')!='')) {
                                  $needle = strtolower($request->get('search'));
                                  $instance->collection = $instance->collection->filter(function ($row) use ($request,$needle) {
                                    $row = $row->toArray();
                                    $result = 0;
                                    foreach ($row as $key => $value) {
                                      if(strpos(strtolower($value), $needle) > -1) {
                                        $result = 1;
                                      }
                                    }
                                    return $result ? true : false;
                                  });
                            }
                        })
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if(Gate::denies('model_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
       }
       $makes = Make::orderBy('name','asc')->get();
        return view('admin.modules.models.create', compact('makes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if(Gate::denies('model_create')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'make' => 'required',
                    'attributes' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        if ($request->has('attributes')) {
            $attributeValues = $request->get('attributes');
            foreach ($attributeValues as $key => $value) {
                $dynamicAttributes = new Models();
                $dynamicAttributes->make_id = $request->make;
                $dynamicAttributes->name = $value['name'];
                $dynamicAttributes->save();
            }
        }

        return redirect('model')->with('status', 'Successfully added.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if(Gate::denies('model_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $model = Models::where('id', $id)->first();
        $makes = Make::orderBy('name','asc')->get();
        return view('admin.modules.models.edit', compact('makes', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if(Gate::denies('model_update')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $model = Models::where('id', $id)->first();
        $model->name = $request->name;
        $model->make_id = $request->make;
        $model->save();

        return redirect('model')->with('status', 'Successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        return 1;
    }

    public function importModel() {
        if(Gate::denies('model_import')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        return view('admin.modules.models.import_model');
    }

    public function importExcel(Request $request) {
        if(Gate::denies('model_import')){
            return redirect('admin')->with('error', 'You dont have sufficient privlilege to access this area');
        }
        $validator = Validator::make($request->all(), [
                    'import_file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        if ($request->hasFile('import_file')) {

			set_time_limit(0);

            $fileD = fopen($request->file('import_file'), "r");
            $column = fgetcsv($fileD);
            while (!feof($fileD)) {
                $rowData[] = fgetcsv($fileD);
            }

            foreach ($rowData as $key => $value) {

				$makeTitle = trim($value[0]);
				$modelTitle = trim($value[1]);


                if (!empty($makeTitle) && !empty($modelTitle)) {

					$make = Make::where('name', $makeTitle)->first();

					if(empty($make)){

						$make = new Make();
						$make->name = $makeTitle;
						$make->save();
					}


                    $model = Models::where('make_id', $make->id)->where('name', $modelTitle)->first();
                    if ($model == null) {
                        $inserted_data = [
                            'make_id' => $make->id,
                            'name' => $modelTitle,
                        ];

                        Models::create($inserted_data);
                    }


                }
            }
            return redirect('model')->with('status', 'Successfully Imported.');
        }
    }

}
