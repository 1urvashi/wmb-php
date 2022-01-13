<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use DB;
use Auth;
use Validator;
use File;
use Input;
use App\TraderUser;
use App\User;

class ImportController extends Controller {

    public function import() {
        return view('admin.modules.admin_import.import_model');
    }

    public function importPost(Request $request) {
        $validator = Validator::make($request->all(), [
                    'import_file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($request->hasFile('import_file')) {
            ini_set('auto_detect_line_endings', TRUE);
            $rows = array_map('str_getcsv', file($request->import_file));
            $header = array_shift($rows);
            $csv = array();

            foreach ($rows as $row) {
                if ($row[0] != null) {
                    $csv[] = array_combine($header, $row);
                }
            }

            foreach ($csv as $key => $value) {
                $drm_user = User::where('id', (int) $value['new_dmr_id'])->first();

                if (!empty($drm_user)) {
                    TraderUser::where('id', (int) $value['id'])->update(['dmr_id' => (int) $value['new_dmr_id']]);
                } else {
                    TraderUser::where('id', (int) $value['id'])->update(['dmr_id' => null]);
                }
            }
            return redirect('import-trader-drm')->with('status', 'Trader DRM successfully updated.');
        }
    }

    /* public function importPost(Request $request) {
      $validator = Validator::make($request->all(), [
      'import_file' => 'required|mimes:csv,txt',
      ]);

      if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
      }
      if ($request->hasFile('import_file')) {
      $file = $request->import_file;
      $path = $file->getRealPath();
      $fileName = $file->getClientOriginalName();
      set_time_limit(0);
      $data = Excel::load($path, false, 'ISO-8859-1', function ($reader) { })->get()->toArray();
      // dd($data);
      if (!empty($data) && count($data)) {
      foreach ($data as $key => $value) {
      $drm_user = User::where('id', (int)$value['new_dmr_id'])->first();

      if(!empty($drm_user)) {
      // $trader = TraderUser::where('id', 25)->first();
      TraderUser::where('id', (int)$value['id'])->update(['dmr_id' => (int)$value['new_dmr_id']]);

      } else {
      TraderUser::where('id', (int)$value['id'])->update(['dmr_id' => null]);
      }
      }
      }
      return redirect('import-trader-drm')->with('status', 'Trader DRM successfully updated.');
      }

      } */
}
