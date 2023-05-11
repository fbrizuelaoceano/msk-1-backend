<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function GetAllLikes()
    {
        $likes = Like::all();
        return response()->json($likes);
    }
    public function CreateLike(Request $request)
    {
        $like = Like::create($request->all());
        return response()->json($like, 201);
    }
    public function GetByIdLike($id)
    {
        $like = Like::find($id);
        if ($like) {
            return response()->json($like);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
    public function UpdateLike(Request $request, $id)
    {
        $like = Like::find($id);
        if ($like) {
            $like->update($request->all());
            return response()->json($like);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
    public function DeleteLike($id)
    {
        $like = Like::find($id);
        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Like deleted']);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
    public function SwitchLike(Request $request){
        //le dio al liker
            // 'entity_id_crm_contrato',
            // 'product_code',
            // 'user_id',
        $fillable = [
            'entity_id_crm_contrato',
            'product_code',
            'user_id',
        ];

        $user = $request->user();

        $likes = Like::updateOrCreate([
                'entity_id_crm_contrato' => $request->entityId,
                'product_code' => $request->productCode
            ],
            [
                'user_id' => $user->id,
                'entity_id_crm_contrato' => $request->entityId,
                'product_code' => $request->productCode,
                'is_liked' => DB::raw('NOT is_liked')
            ]);        

        // $like = Like::find($id);
        if (!$likes) {
            $likes->delete();
            return response()->json(['message' => 'Like deleted']);
        } else {
            return response()->json(['message' => 'Like not found'], 404);
        }
    }
}
