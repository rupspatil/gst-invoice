<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'    => 'required',
            'email'   => 'nullable|email',
            'phone'   => 'nullable',
            'gstin'   => 'nullable',
            'address' => 'nullable',
            'city'    => 'nullable',
            'pincode' => 'nullable',
            'state'   => 'nullable',
            'company' => 'nullable',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $r, Customer $customer)
    {
        $data = $r->validate([
            'name'    => 'required',
            'email'   => 'nullable|email',
            'phone'   => 'nullable',
            'gstin'   => 'nullable',
            'address' => 'nullable',
            'company' => 'nullable',
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * GST Lookup API
     */
public function gstLookup($gst)
{
    $url = "https://gst-insights-api.p.rapidapi.com/getGSTDetailsUsingGST/" . $gst;

    $response = Http::withHeaders([
        "x-rapidapi-key" => env("RAPID_GST_API_KEY"),
        "x-rapidapi-host" => "gst-insights-api.p.rapidapi.com"
    ])->get($url);

    if ($response->failed()) {
        return response()->json(['error' => 'Invalid GST Number or API failed'], 400);
    }

    $data = $response->json();

    // IMPORTANT: Data is inside array index 0
    $info = $data['data'][0] ?? [];

    // Build full address
    $addr = $info['principalAddress']['address'] ?? [];
    $fullAddress =
        ($addr['buildingName'] ?? '') . ', ' .
        ($addr['buildingNumber'] ?? '') . ', ' .
        ($addr['street'] ?? '') . ', ' .
        ($addr['location'] ?? '') . ', ' .
        ($addr['district'] ?? '') . ' - ' .
        ($addr['pincode'] ?? '') . ', ' .
        ($addr['stateCode'] ?? '');

    return response()->json([
        'name'    => $info['legalName'] ?? '',
        'company' => $info['tradeName'] ?? '',
        'phone'   => '',  // free API does NOT provide mobile number
        'pincode' => $addr['pincode'] ?? '',
        'state'   => $addr['stateCode'] ?? '',
        'address' => $fullAddress
    ]);
}

}
