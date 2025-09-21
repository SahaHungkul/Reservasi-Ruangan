<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FixedScheduleRequest;
use App\Http\Resources\FixedScheduleResource;
use App\Models\FixedSchedule;

class FixedScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FixedScheduleResource::collection(
            FixedSchedule::with('room')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FixedScheduleRequest $request)
    {
        $fixedSchedule = FixedSchedule::create($request->validated());
        return new FixedScheduleResource($fixedSchedule->load('room'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fixedSchedule = FixedSchedule::with('room')->findOrFail($id);

        return new FixedScheduleResource($fixedSchedule); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FixedScheduleRequest $request, FixedSchedule $fixedSchedule)
    {
        $fixedSchedule->update($request->validated());
        return new FixedScheduleResource($fixedSchedule->load('room'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FixedSchedule $fixedSchedule)
    {
        $fixedSchedule->delete();
        return response()->json(['message' => 'Jadwal rutin berhasil dihapus']);
    }
}
