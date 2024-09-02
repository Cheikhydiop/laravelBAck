<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(title="Test API ODC Cheikh Diop", version="1.0.0")
 */
class TestController extends Controller
{
 
    public function testEndpoint()
    {
        return response()->json(['message' => 'Test successful']);
    }
}
