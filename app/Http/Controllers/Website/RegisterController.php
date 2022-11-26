<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Request;
use Schema;
use DB;

use App\Models\RegisterSetting;
use App\Models\Domain;

class RegisterController extends Controller
{
    public function set()
    {
        try {
            $id = Request::get('id');
            $sortFields = Request::get('sortItems');
            $domain = Domain::get($id);
            $domain->register_text = Request::get('text');
            $domain->save();

            if (!Schema::hasTable('users_'.$id)) {
                Schema::connection('mysql')->create('users_'.$id, function($table)
                {
                    $table->increments('id');
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->string('token');
                    $table->string('avatar')->default('default.jpg');
                    $table->timestamps();
                });
            }

            $oldFields = RegisterSetting::where('domain_id', $id)->get();

            $oldColumns = [];
            foreach ($oldFields as $field) {
                if ($field->label != 'email' && $field->label != 'password') {
                    $column = $field->label;
                    Schema::table('users_'.$id, function ($table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }

            Schema::table('users_'.$id, function ($table) use ($oldColumns, $sortFields) {
                foreach($sortFields as $field) {
                    if (strtolower($field['label']) != 'email' && strtolower($field['label']) != 'password') {
                        if ($field['required']) {
                            $table->string(strtolower($field['label']));
                        } else {
                            $table->string(strtolower($field['label']))->nullable();
                        }
                    }
                }
            });

            $isExist = RegisterSetting::where('domain_id', $id)->where('label', 'email')->count();
            if (!$isExist) {
                RegisterSetting::create([
                    'domain_id' =>  $id,
                    'label'     =>  'email',
                    'required'  =>  true,
                    'user_id'    =>  auth()->user()->id,
                    'order'     =>  RegisterSetting::where('domain_id', $id)->count()
                ]);
            }

            $isExist = RegisterSetting::where('domain_id', $id)->where('label', 'password')->count();
            if (!$isExist) {
                RegisterSetting::create([
                    'domain_id' =>  $id,
                    'label'     =>  'password',
                    'required'  =>  true,
                    'user_id'    =>  auth()->user()->id,
                    'order'     =>  RegisterSetting::where('domain_id', $id)->count()
                ]);
            }

            $oldFields = RegisterSetting::where('domain_id', $id)->get();

            foreach ($oldFields as $field) {
                if ($field->label != 'email' && $field->label != 'password') {
                    $field->delete();
                }
            }

            $fields = Request::get('items');

            foreach($fields as $field) {
                RegisterSetting::create([
                    'domain_id' =>  $id,
                    'label'     =>  strtolower($field['label']),
                    'required'  =>  $field['required'],
                    'disabled'  =>  $field['disabled'],
                    'user_id'    =>  auth()->user()->id,
                    'order'     =>  RegisterSetting::where('domain_id', $id)->count()
                ]);
            }
            
            foreach($sortFields as $key => $field) {
                $data = RegisterSetting::where('domain_id', $id)->where('label', strtolower($field['label']))->first();
                $data->order = $key;
                $data->save();
            }
            // Schema::connection('mysql')->dropIfExists('mytable_'.Request::get('id'));
            return response()->json(200);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function get()
    {
        try {
            $id = Request::get('id');
            $data = RegisterSetting::where('domain_id', $id)->orderBy('order', 'asc')->get();
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
