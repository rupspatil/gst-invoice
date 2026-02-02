@extends('layouts.app')

@section('content')
<div class="section">
    <h4>Add Customer</h4>
    <hr>
<div class="container">
    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
<div class="row">
        <div class="form-group col-12">
    <label>GST Number</label>
    <input type="text" id="gstin" name="gstin" class="form-control" placeholder="Enter GST Number">
</div>

<div class="form-group col-6">
    <label>Customer Name</label>
    <input type="text" id="name" name="name" class="form-control">
</div>

<div class="form-group col-6">
    <label>Email</label>
    <input type="email" id="email" name="email" class="form-control">
</div>
<div class="form-group col-6">
    <label>Phone</label>
    <input type="number" id="phone" name="phone" class="form-control">
</div>

<div class="form-group col-6">
    <label>Company Name</label>
    <input type="text" id="company" name="company" class="form-control">
</div>

<div class="form-group col-6">
    <label>Pincode</label>
    <input type="text" id="pincode" name="pincode" class="form-control">
</div>
<div class="form-group col-6">
    <label>City</label>
    <input type="text" id="city" name="city" class="form-control">
</div>
<div class="form-group col-12">
    <label>State</label>
    <input type="text" id="state" name="state" class="form-control">
</div>

<div class="form-group col-12">
    <label>Address</label>
    <textarea id="address" name="address" class="form-control" rows="3"></textarea>
</div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function () {

    $("#gstin").on("keyup", function () {
        let gst = $(this).val().trim();

        if (gst.length === 15) {  
            $.ajax({
                url: "{{ url('/gst-lookup') }}/" + gst,
                method: "GET",
                success: function (res) {
                   
                    $("#company").val(res.company);
                    $("#phone").val(res.phone);
                    $("#pincode").val(res.pincode);
                    $("#state").val(res.state);
                    $("#address").val(res.address);
                    if (res.pincode) {
                        getCityByPincode(res.pincode);
                    }
                },

                error: function () {
                    console.log("GST Lookup Failed");
                }
            });
        }
    });

});
</script>
<script>
async function getCityByPincode() {
    const pincode = document.getElementById("pincode").value;

    if (pincode.length !== 6) return;

    const res = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
    const data = await res.json();

    if (data[0].Status === "Success") {
        document.getElementById("city").value =
            data[0].PostOffice[0].District;
    }
}

document.getElementById("pincode").addEventListener("blur", getCityByPincode);
</script>
@endpush
