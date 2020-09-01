<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MemoCreateService;
use App\Services\MemoShowService;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\MemoCreateService $service
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, MemoCreateService $service)
    {
        $title = $request->title;
        $body = $request->body;

        $result = $service->create($title, $body);

        if (!$result) {
            return response()->json([
                'error' => '作成に失敗しました。',
            ]);
        }

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param \App\Services\MemoShowService $service
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, MemoShowService $service)
    {
        $memo = $service->show($id);

        // TODO メモが存在しない場合のエラー

        return response()->json($memo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
