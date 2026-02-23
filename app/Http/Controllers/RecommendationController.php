<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class RecommendationController extends Controller
{
    public function index()
    {
        try {
            
            $response = Http::get('http://127.0.0.1:5000/latest_result.json');

            if ($response->failed()) {
                $errorMessage = $response->json('error') ?? 'API request failed.';
                return back()->with('error', $errorMessage);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                return back()->with('error', $data['error']);
            }

            $foodImages = [
                'Apple Pie' => 'pie.jpg',
                'Fried Rice' => 'rice.jpg',
                'Chicken Soup' => 'soup.jpg',
                'Popcorn' => 'popcorn.jpg',
                'Ice Cream' => 'icecream.jpg',
            ];

            $recommendedFood = $data['recommended_food'] ?? '';

            return view('recommend', compact('data'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }
}
