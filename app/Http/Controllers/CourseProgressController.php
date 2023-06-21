<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CourseProgress;
use Illuminate\Http\Request;

class CourseProgressController extends Controller
{
    public function GetAll(Request $request){
        
        $coursesProgress = CourseProgress::all();
        
        return response()->json(
            $coursesProgress
        );
    }
    public function Create(Request $request){

        $requestBody = $request->only(CourseProgress::$formAttributes);
        $requestBody["entity_id_crm"] = $request["id"];

        $coursesProgress = CourseProgress::create($requestBody);
        
        return response()->json(
            [
                $coursesProgress
            ]
        );
    }
    
}
