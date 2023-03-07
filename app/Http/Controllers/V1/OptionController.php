<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OptionRequest;
use App\Http\Resources\V1\OptionResource;
use App\Models\V1\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $options = Option::
                orderBy('created_at', 'desc')
                ->paginate($request->limit ? $request->limit : Option::count());
        
        return OptionResource::collection($options);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OptionRequest $request)
    {
        $option = Option::create($request->all());

        return new OptionResource($option);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function show(Option $option)
    {
        return new OptionResource($option);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function update(OptionRequest $request, Option $option)
    {
        $option->update($request->all());

        return new OptionResource($option);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Option $option
     * @return \Illuminate\Http\Response
     */
    public function destroy(Option $option)
    {
        $option->where('autoload', 'no')->delete();
        
        return response(null, 204);
    }
}
