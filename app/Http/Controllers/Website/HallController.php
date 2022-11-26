<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Request;

use App\Models\Hall;

class HallController extends Controller
{
    public function get()
    {
        try {
            $type = Request::get('type');
            $id = Request::get('id');

            $hall = Hall::find($id);
            $categoryData = json_decode($hall->categories);
            $hallData = [];
            foreach ($categoryData as $category) {
                if ($type < 2) {
                    foreach($category->boothItems->get as $booth) {
                        array_push($hallData, $booth);
                    }
                } else {
                    foreach($category->posterItems->get as $booth) {
                        array_push($hallData, $booth);
                    }
                }
            }
            
            return response()->json($hallData);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    // public function get()
    // {
    //     try {

    //     } catch(\Exception $e) {
    //         echo json_encode($e->getMessage());
    //     }
    // }

    // public function get()
    // {
    //     try {

    //     } catch(\Exception $e) {
    //         echo json_encode($e->getMessage());
    //     }
    // }

    // public function get()
    // {
    //     try {

    //     } catch(\Exception $e) {
    //         echo json_encode($e->getMessage());
    //     }
    // }
}
