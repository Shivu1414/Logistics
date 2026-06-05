<?php

namespace App\Http\Controllers\Logistics;

use Illuminate\Http\Request;
use App\Http\Controllers\Logistics\Utils\LogisticsHelper;
use App\Http\Controllers\Controller;

class LogisticsController extends Controller
{
    public function citySuggestions(Request $request)
    {
        try {

            $search = $request->search;

            if (empty($search)) {
                return response()->json([
                    'cities' => []
                ]);
            }

            $helper = new LogisticsHelper();

            $cities = $helper->getCitySuggestions($search);

            return response()->json([
                'status' => true,
                'cities' => $cities
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function routeMetrics(Request $request)
    {
        try {
    
            $request->validate([
                'origin_place_id' => 'required|string',
                'destination_place_id' => 'required|string',
            ]);
    
            $helper = new LogisticsHelper();
    
            $result = $helper->getRouteMetrics(
                $request->origin_place_id,
                $request->destination_place_id
            );
    
            return response()->json([
                'status' => true,
                'distance' => $result['distance'],
                'duration' => $result['duration'],
                'distance_value' => $result['distance_value'],
                'duration_value' => $result['duration_value'],
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
    
        }
    }

    // public function routeMetrics(Request $request)
    // {
    //     try {
    
    //         $request->validate([
    //             'origin_place_id' => 'required',
    //             'destination_place_id' => 'required'
    //         ]);
    
    //         $helper = new LogisticsHelper();
    
    //         $routes = $helper->getAlternativeRoutes(
    //             $request->origin_place_id,
    //             $request->destination_place_id
    //         );
    
    //         return response()->json([
    //             'status' => true,
    //             'routes' => $routes
    //         ]);
    
    //     } catch (\Exception $e) {
    
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    
    //     }
    // }
}