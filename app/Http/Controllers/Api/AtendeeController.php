<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Atendee\AtendeeRequest;
use App\Http\Resources\Atendee\AtendeeResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Atendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AtendeeController extends Controller
{

    use CanLoadRelationship;

    protected $atendeeModel;

    private $relations = ['user'];

    public function __construct()
    {
        $this->atendeeModel = new Atendee();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationShip(Atendee::query(), $this->relations);

        $perPage = request('perPage', 10);
        $page = request('page', 1);

        $atendee = $query->latest()->paginate($perPage, ['*'], 'page', $page);

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
    public function store(AtendeeRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'user_id',
            'event_id'
        ]);

        $atendee = Atendee::create($payload);

        $this->loadRelationShip($atendee);

        return response()->success($atendee, 'Atendee berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $eventId, string $atendeeId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->failed('Event tidak ditemukan', 404);
        }

        $atendee = Atendee::where('id', $atendeeId)
            ->where('event_id', $eventId)
            ->first();

        if (!$atendee) {
            return response()->failed('Atendee tidak ditemukan atau tidak sesuai event', 404);
        }

        $this->loadRelationShip($atendee);

        return response()->success(new AtendeeResource($atendee), 'Atendee berhasil ditemukan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $eventId, string $atendeeId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return response()->failed('Event tidak ditemukan', 404);
        }

        $atendee = Atendee::where('id', $atendeeId)
            ->where('event_id', $eventId)
            ->first();

        if (!$atendee) {
            return response()->failed('Atendee tidak ditemukan atau tidak sesuai event', 404);
        }

        if (Gate::denies('delete-atendee', [$atendee, $event])) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Anda tidak memiliki akses untuk menghapus atendee ini'
            ], 403);
        }

        $atendee->delete();

        return response()->success('Atendee berhasil dihapus');
    }
}