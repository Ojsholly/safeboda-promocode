<?php

namespace App\Http\Controllers\API\v1\PromoCode;

use DateTime;
use Carbon\Carbon;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use App\Http\Traits\DistanceTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePromoCodeRequest;

class PromoCodeController extends Controller
{

    use DistanceTrait;

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
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePromoCodeRequest $request)
    {
        if ($request->code == null) {
            # code...
            $request->code = PromoCode::generate();
        }

        $venue_coordinates = $this->get_latitude_and_longitude($request->venue);

        if ($venue_coordinates == null) {
            # code...

            $response = [
                'status' => 'error',
                'message' => 'Sorry. Location not found.'
            ];

            return response()->json($response, 400);
        }

        $expiry = PromoCode::fetch_ttl($request->expiry_date);

        $promo_code = PromoCode::create([
            'code' => $request->code,
            'venue' => $request->venue,
            'value' => $request->value,
            'radius' => $request->radius,
            'expires_at' => $expiry
        ]);

        if ($promo_code == null) {
            # code...
            $response = [
                'status' => 'error',
                'message' => 'Error saving promo code.'
            ];

            return response()->json($response, 400);
        }


        $response = [
            'status' => 'success',
            'message' => 'New Promo Code Successfully saved.'
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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