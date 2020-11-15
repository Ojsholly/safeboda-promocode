<?php

namespace App\Http\Controllers\API\v1\PromoCode;

use Polyline;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use App\Http\Traits\DistanceTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\PromoCodeResource;
use App\Http\Requests\CreatePromoCodeRequest;
use App\Http\Requests\UpdatePromoCodeRequest;
use App\Http\Requests\ValidatePromoCodeRequest;
use App\Http\Resources\PromoCodeResourceCollection;

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
        return new PromoCodeResourceCollection(PromoCode::withTrashed()->get());
    }

    public function active()
    {
        return new PromoCodeResourceCollection(PromoCode::withoutExpired()->get());
    }

    public function expired()
    {
        return new PromoCodeResourceCollection(PromoCode::onlyExpired()->get());
    }

    public function deactivated()
    {
        return new PromoCodeResourceCollection(PromoCode::onlyTrashed()->get());
    }

    public function deactivate($id)
    {
        $promo_code = PromoCode::findByUuid($id);

        if ($promo_code == null) {
            # code...
            abort(404, 'Promo Code not found');
        }


        if ($promo_code->delete()) {
            # code...
            $response = [
                'status' => 'success',
                'message' => 'Promo Code Successfully deactivated'
            ];

            return response()->json($response, 200);
        }

        $response = [
            'status' => 'error',
            'message' => 'Error Deactivating Promo Code'
        ];

        return response()->json($response, 400);
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


    public function verify($id, array $origin, array $destination)
    {
        $promo_code = PromoCode::findByUuid($id);

        $venue = $promo_code->venue;

        $venue_coordinates = $this->get_latitude_and_longitude($venue);

        $origin_distance = $this->get_distance($venue_coordinates, $origin);

        $destination_distance = $this->get_distance($venue_coordinates, $destination);

        $radius = $promo_code->radius;

        if ($origin_distance > $radius && $destination_distance > $radius) {
            # code...

            return false;
        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ValidatePromoCodeRequest $request, $code)
    {
        //
        $promo_code = PromoCode::where('code', $code)->first();

        if ($promo_code == null) {
            # code...

            abort(404, 'Promo Code not found');
        }

        if ($promo_code->expired()) {
            # code...

            $response = [
                'status' => 'error',
                'message' => 'Sorry. This promo code has already expired.'
            ];

            return response()->json($response, 400);
        }

        $origin_coordinates = $this->get_latitude_and_longitude($request->origin);

        $destination_coordinates = $this->get_latitude_and_longitude($request->destination);

        if ($origin_coordinates == null || $destination_coordinates == null) {
            # code...

            $response = [
                'status' => 'error',
                'message' => 'Sorry. Locations not found.'
            ];

            return response()->json($response, 400);
        }

        $valid_code = $this->verify($promo_code->uuid, $origin_coordinates, $destination_coordinates);

        if ($valid_code == false) {
            # code...

            $response = [
                'status' => 'error',
                'message' => 'Current location not valid for promo code.'
            ];

            return response()->json($response, 400);
        }


        $points = [$destination_coordinates, $origin_coordinates];

        $promo_code->polyline = Polyline::encode($points);

        return new PromoCodeResource($promo_code);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePromoCodeRequest $request, $id)
    {
        //
        $promo_code = PromoCode::findByUuid($id);

        if ($promo_code == null) {
            # code...
            abort(404, 'Promo Code Not Found');
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

        $update_promo_code = PromoCode::where('uuid', $id)->update([
            'code' => $request->code,
            'venue' => $request->venue,
            'value' => $request->value,
            'radius' => $request->radius,
            'expires_at' => $request->expiry_date
        ]);

        if ($update_promo_code == null) {
            # code...
            $response = [
                'status' => 'error',
                'message' => 'Error updating promo code.'
            ];

            return response()->json($response, 400);
        }

        $response = [
            'status' => 'success',
            'message' => 'Promo Code successfully updated.'
        ];

        return response()->json($response, 400);
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
        $promo_code = PromoCode::findByUuid($id);

        if ($promo_code == null) {
            # code...
            abort(404, 'Promo Code not found');
        }

        if ($promo_code->forceDelete()) {
            # code...
            $response = [
                'status' => 'success',
                'message' => 'Promo Code Successfully deleted'
            ];

            return response()->json($response, 200);
        }

        $response = [
            'status' => 'error',
            'message' => 'Error Deleting Promo Code'
        ];

        return response()->json($response, 400);
    }
}