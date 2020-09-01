<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MemoCreateService;
use App\Services\MemoShowService;
use App\Services\MemoUpdateService;
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
     * @param MemoUpdateService $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id, MemoUpdateService $service)
    {
        // TODO 1つも更新項目がない場合のエラー（ループでチェック？）

        $contents = [];

        if ($request->title !== null) {
            $contents['title'] = $request->title;
        }

        if ($request->body !== null) {
            $contents['body'] = $request->body;
        }

        $memo = $service->update($id, $contents);

        // TODO 更新に失敗した場合のエラー

        return response()->json($memo);
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
