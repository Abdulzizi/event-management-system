<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Atendee\AtendeeResource;
use App\Models\Atendee;
use Illuminate\Http\Request;

class AtendeeController extends Controller
{
    protected $atendeeModel;

    public function __construct()
    {
        $this->atendeeModel = new Atendee();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = request('perPage', 10);
        $page = request('page', 1);

        $atendee = $this->atendeeModel->paginate($perPage, ['*'], 'page', $page);

        return response()->success([
            'list' => AtendeeResource::collection($atendee),
            'meta' => [
                'total' => $atendee->total(),
                'last_page' => $atendee->lastPage(),
                'current_page' => $atendee->currentPage(),
                'per_page' => $atendee->perPage(),
            ],
        ], 'Atendee berhasil ditemukan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}